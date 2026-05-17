<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PretestResult;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PretestController extends Controller
{
    // Jumlah soal yang ditampilkan per sesi pre-test
    private const QUESTION_COUNT = 10;

    // Threshold skor per level untuk rule-based placement (0–100)
    private const THRESHOLD = 70; // >= 70% pada soal suatu level → tempatkan di level itu

    // Threshold fallback (jika soal tidak berlevel) — sama dengan rule-based threshold
    private const THRESHOLD_LANJUT   = 70;
    private const THRESHOLD_MENENGAH = 40;

    // ── Show ─────────────────────────────────────────────────────────

    public function show(): View|RedirectResponse
    {
        // Sudah mengerjakan pre-test → ke dashboard
        if (PretestResult::where('user_id', Auth::id())->exists()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        // Ambil soal secara seimbang per level agar rule-based placement akurat
        $perLevel  = (int) ceil(self::QUESTION_COUNT / count(PretestResult::LEVELS));
        $questions = collect();

        foreach (PretestResult::LEVELS as $lvl) {
            $lvlQs = Question::where('is_pretest', true)
                ->where('level', $lvl)
                ->with('options')
                ->inRandomOrder()
                ->take($perLevel)
                ->get();
            $questions = $questions->merge($lvlQs);
        }

        // Jika kurang (soal tidak berlevel), tambahkan soal pretest lainnya
        if ($questions->count() < self::QUESTION_COUNT) {
            $exclude = $questions->pluck('id');
            $extra   = Question::where('is_pretest', true)
                ->when($exclude->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $exclude))
                ->with('options')
                ->inRandomOrder()
                ->take(self::QUESTION_COUNT - $questions->count())
                ->get();
            $questions = $questions->merge($extra);
        }

        $questions = $questions->shuffle();

        // Belum ada soal sama sekali
        if ($questions->isEmpty()) {
            $this->savePretestResult(score: 0, level: 'pemula');
            return redirect()->route('mahasiswa.dashboard')
                ->with('info', 'Soal pre-test belum tersedia. Level awal ditetapkan: Pemula.');
        }

        return view('pretest', compact('questions'));
    }

    // ── Submit ───────────────────────────────────────────────────────

    public function submit(Request $request): RedirectResponse
    {
        // Jika sudah ada hasil pre-test, abaikan submit ulang
        if (PretestResult::where('user_id', Auth::id())->exists()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        // Jawaban pilihan ganda:  answers[question_id] = option_id
        // Jawaban susun kode:     answers_arranged[question_id][] = option_id (berurutan)
        $answers         = $request->input('answers', []);
        $answersArranged = $request->input('answers_arranged', []);

        // Gabungkan semua question_id yang masuk
        $allQuestionIds = array_unique(
            array_merge(array_keys($answers), array_keys($answersArranged))
        );

        // Ambil soal beserta opsi dari DB
        $questions = Question::where('is_pretest', true)
            ->whereIn('id', $allQuestionIds)
            ->with('options')
            ->get()
            ->keyBy('id');

        $correct    = 0;
        $total      = $questions->count();
        $levelStats = [];   // ['pemula' => ['correct' => n, 'total' => n], ...]
        $detail     = [];   // per-soal untuk halaman hasil

        foreach ($allQuestionIds as $questionId) {
            $question = $questions->get($questionId);
            if (! $question) {
                continue;
            }

            $lvl = $question->level ?? 'unknown';
            $levelStats[$lvl] ??= ['correct' => 0, 'total' => 0];
            $levelStats[$lvl]['total']++;

            if ($question->type === 'code_arrange') {
                // Jawaban = urutan tile yang dipilih mahasiswa
                $userOrder = array_map('intval', $answersArranged[$questionId] ?? []);

                // Jawaban benar = tile dengan order >= 1, diurutkan ascending
                $correctOrder = $question->options
                    ->where('order', '>', 0)
                    ->sortBy('order')
                    ->pluck('id')
                    ->values()
                    ->toArray();

                $isCorrect = ($userOrder === $correctOrder);

                // Teks untuk halaman hasil
                $correctText = $question->options
                    ->where('order', '>', 0)
                    ->sortBy('order')
                    ->pluck('option_text')
                    ->implode(' → ');
                $selectedText = count($userOrder)
                    ? $question->options
                        ->whereIn('id', $userOrder)
                        ->sortBy(fn ($o) => array_search($o->id, $userOrder))
                        ->pluck('option_text')
                        ->implode(' → ')
                    : '(tidak dijawab)';

                $detail[] = [
                    'text'          => $question->question_text,
                    'level'         => $lvl,
                    'type'          => 'code_arrange',
                    'selected_text' => $selectedText,
                    'correct_text'  => $correctText,
                    'explanation'   => $question->explanation,
                    'is_correct'    => $isCorrect,
                ];
            } else {
                // Pilihan ganda biasa
                $optionId       = $answers[$questionId] ?? null;
                $selectedOption = $question->options->firstWhere('id', $optionId);
                $correctOption  = $question->options->firstWhere('is_correct', true);
                $isCorrect      = (bool) ($selectedOption?->is_correct);

                $detail[] = [
                    'text'          => $question->question_text,
                    'level'         => $lvl,
                    'type'          => 'multiple_choice',
                    'selected_text' => $selectedOption?->option_text ?? '(tidak dijawab)',
                    'correct_text'  => $correctOption?->option_text ?? '—',
                    'explanation'   => $question->explanation,
                    'is_correct'    => $isCorrect,
                ];
            }

            if ($isCorrect) {
                $correct++;
                $levelStats[$lvl]['correct']++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $level = $this->assignLevel($score, $levelStats);

        $this->savePretestResult($score, $level);

        // Simpan detail ke session untuk halaman hasil
        session()->put('pretest_hasil', [
            'score'       => $score,
            'correct'     => $correct,
            'total'       => $total,
            'level'       => $level,
            'level_stats' => $levelStats,
            'questions'   => $detail,
        ]);

        return redirect()->route('mahasiswa.pretest.hasil');
    }

    // ── Hasil ────────────────────────────────────────────────────────

    public function hasil(): View|RedirectResponse
    {
        // Harus sudah selesai pre-test
        if (! PretestResult::where('user_id', Auth::id())->exists()) {
            return redirect()->route('mahasiswa.pretest');
        }

        $data = session('pretest_hasil');

        // Jika session habis (misal di-refresh), langsung ke dashboard
        if (! $data) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('pretest-hasil', $data);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Rule-based placement:
     *   1. Periksa dari level tertinggi (lanjut) ke terendah (pemula).
     *   2. Jika skor soal suatu level >= THRESHOLD → tempatkan di sana.
     *   3. Fallback: gunakan skor total jika soal tidak berlevel.
     */
    private function assignLevel(int $score, array $levelStats): string
    {
        foreach (array_reverse(PretestResult::LEVELS) as $lvl) {
            $stat = $levelStats[$lvl] ?? null;
            if ($stat && $stat['total'] > 0) {
                $pct = ($stat['correct'] / $stat['total']) * 100;
                if ($pct >= self::THRESHOLD) {
                    return $lvl;
                }
            }
        }

        // Fallback jika soal tidak memiliki tag level
        if ($score >= self::THRESHOLD_LANJUT)   return 'lanjut';
        if ($score >= self::THRESHOLD_MENENGAH) return 'menengah';

        return 'pemula';
    }

    private function savePretestResult(int $score, string $level): void
    {
        PretestResult::create([
            'user_id'      => Auth::id(),
            'score'        => $score,
            'level'        => $level,
            'completed_at' => now(),
        ]);
    }
}
