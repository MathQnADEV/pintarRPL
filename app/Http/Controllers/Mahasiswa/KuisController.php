<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use App\Models\PretestResult;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KuisController extends Controller
{
    // Minimum skor (0-100) agar topik dianggap "selesai"
    private const PASS_SCORE = 60;

    // ── Tampilkan satu soal kuis sesuai sesi ─────────────────────────

    public function show(Topic $topic, Request $request): View|RedirectResponse
    {
        $user  = Auth::user();
        $level = PretestResult::effectiveLevel($user->id);

        // Topik harus aktif dan sesuai level
        if (! $topic->is_active || $topic->level !== $level) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Kuis ini tidak tersedia untuk level Anda saat ini.');
        }

        // Pastikan mahasiswa sudah membuka materi topik ini
        $progress = LearningProgress::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();

        if (! $progress || $progress->status === 'not_started') {
            return redirect()->route('mahasiswa.materi', $topic)
                ->with('info', 'Baca materi terlebih dahulu sebelum mengerjakan kuis.');
        }

        // ── Inisialisasi/lanjutkan sesi kuis ────────────────────────
        $sessionKey = "quiz_{$topic->id}";

        // Buang sesi lama jika sudah selesai (current >= total) agar retake mulai dari awal,
        // bukan meneruskan sesi yang tertinggal akibat error sebelumnya.
        if ($request->session()->has($sessionKey)) {
            $stale = $request->session()->get($sessionKey);
            if ($stale['current'] >= count($stale['question_ids'])) {
                $request->session()->forget($sessionKey);
            }
        }

        if (! $request->session()->has($sessionKey)) {
            $questions = Question::where('topic_id', $topic->id)
                ->where('is_pretest', false)
                ->where('is_posttest', false)
                ->with('options')
                ->get()
                ->shuffle();

            if ($questions->isEmpty()) {
                return redirect()->route('mahasiswa.materi', $topic)
                    ->with('error', 'Soal kuis untuk topik ini belum tersedia. Hubungi dosen Anda.');
            }

            $request->session()->put($sessionKey, [
                'question_ids' => $questions->pluck('id')->toArray(),
                'current'      => 0,
                'answers'      => [],
            ]);
        }

        $session     = $request->session()->get($sessionKey);
        $current     = $session['current'];
        $questionIds = $session['question_ids'];
        $total       = count($questionIds);

        $question = Question::with('options')->find($questionIds[$current]);

        // Soal tidak ditemukan (mungkin dihapus dosen setelah sesi dimulai)
        if (! $question) {
            $request->session()->forget($sessionKey);
            return redirect()->route('mahasiswa.kuis', $topic)
                ->with('error', 'Beberapa soal tidak tersedia lagi. Sesi kuis direset — silakan mulai kembali.');
        }

        return view('kuis', [
            'topic'    => $topic,
            'question' => $question,
            'current'  => $current + 1,
            'total'    => $total,
            'progress' => (int) round(($current / $total) * 100),
            'user'     => $user,
        ]);
    }

    // ── Terima jawaban satu soal, lanjut ke soal berikutnya ──────────

    public function answer(Topic $topic, Request $request): RedirectResponse
    {
        $sessionKey = "quiz_{$topic->id}";
        $session    = $request->session()->get($sessionKey);

        if (! $session) {
            return redirect()->route('mahasiswa.kuis', $topic);
        }

        // Tentukan tipe soal saat ini untuk validasi & penyimpanan jawaban
        $questionId = $session['question_ids'][$session['current']] ?? null;
        $question   = $questionId ? Question::find($questionId) : null;
        $type       = $question?->type ?? 'multiple_choice';

        if ($type === 'code_arrange') {
            $request->validate(['answer'   => ['required', 'array', 'min:1'],
                                'answer.*' => ['integer']]);
            $answerValue = array_map('intval', $request->input('answer'));
        } else {
            $request->validate(['answer' => ['required', 'integer']]);
            $answerValue = (int) $request->input('answer');
        }

        // Simpan jawaban untuk soal saat ini
        $session['answers'][$session['current']] = $answerValue;
        $session['current']++;

        $request->session()->put($sessionKey, $session);

        // Semua soal terjawab?
        if ($session['current'] >= count($session['question_ids'])) {
            return $this->finishQuiz($topic, $session, $request);
        }

        return redirect()->route('mahasiswa.kuis', $topic);
    }

    // ── Halaman hasil & review setelah kuis selesai ─────────────────

    public function hasil(Topic $topic, Request $request): View|RedirectResponse
    {
        $reviewKey = "quiz_{$topic->id}_review";

        if (! $request->session()->has($reviewKey)) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('kuis-hasil', [
            'topic'  => $topic,
            'review' => $request->session()->get($reviewKey),
            'user'   => Auth::user(),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function finishQuiz(Topic $topic, array $session, Request $request): RedirectResponse
    {
        $questionIds = $session['question_ids'];
        $answers     = $session['answers'];
        $total       = count($questionIds);

        // Hitung jawaban benar + kumpulkan data review per soal
        $correct         = 0;
        $reviewQuestions = [];

        foreach ($questionIds as $position => $questionId) {
            $answer   = $answers[$position] ?? null;
            $question = Question::with('options')->find($questionId);

            if (! $question) {
                continue;
            }

            // ── Hitung kebenaran berdasarkan tipe soal ───────────────────
            if ($question->type === 'code_arrange') {
                // Jawaban benar = tile dengan order >= 1, diurutkan ascending
                // Tile dengan order = 0 (atau null) = pengecoh — tidak masuk jawaban benar
                $correctOrder = $question->options
                    ->where('order', '>', 0)
                    ->sortBy('order')
                    ->pluck('id')
                    ->values()
                    ->toArray();
                $userOrder = is_array($answer) ? $answer : [];
                $isCorrect = ($userOrder === $correctOrder);

                $reviewQuestions[] = [
                    'type'          => 'code_arrange',
                    'text'          => $question->question_text,
                    'explanation'   => $question->explanation,
                    'is_correct'    => $isCorrect,
                    'correct_order' => $correctOrder,
                    'user_order'    => $userOrder,
                    'tiles'         => $question->options->pluck('option_text', 'id')->toArray(),
                    // Tile pengecoh (order = 0 atau null) untuk review
                    'distractors'   => $question->options
                        ->filter(fn ($o) => ! ($o->order > 0))
                        ->pluck('option_text', 'id')
                        ->toArray(),
                    'options'       => $question->options->map(fn ($o) => [
                        'id'    => $o->id,
                        'text'  => $o->option_text,
                        'order' => $o->order,
                    ])->values()->toArray(),
                ];
            } else {
                // multiple_choice
                $optionId  = is_int($answer) ? $answer : null;
                $isCorrect = $optionId !== null && $question->options
                    ->where('id', $optionId)
                    ->where('is_correct', true)
                    ->isNotEmpty();

                $reviewQuestions[] = [
                    'type'        => 'multiple_choice',
                    'text'        => $question->question_text,
                    'explanation' => $question->explanation,
                    'is_correct'  => $isCorrect,
                    'selected_id' => $optionId,
                    'options'     => $question->options->map(fn ($o) => [
                        'id'         => $o->id,
                        'text'       => $o->option_text,
                        'is_correct' => (bool) $o->is_correct,
                    ])->values()->toArray(),
                ];
            }

            if ($isCorrect) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;

        // Simpan / update QuizResult (upsert — unique key user_id+topic_id)
        $existingResult = QuizResult::where('user_id', Auth::id())
            ->where('topic_id', $topic->id)
            ->first();

        if (! $existingResult || $score >= $existingResult->score) {
            QuizResult::updateOrCreate(
                ['user_id' => Auth::id(), 'topic_id' => $topic->id],
                [
                    'score'           => $score,
                    'total_correct'   => $correct,
                    'total_questions' => $total,
                    'completed_at'    => now(),
                ]
            );
        }

        // Update LearningProgress
        $progress = LearningProgress::where('user_id', Auth::id())
            ->where('topic_id', $topic->id)
            ->first();

        if ($progress) {
            $progress->quiz_attempts++;
            if ($score > ($progress->best_score ?? 0)) {
                $progress->best_score = $score;
            }
            if ($score >= self::PASS_SCORE && $progress->status !== 'completed') {
                $progress->status = 'completed';
            }
            $progress->last_accessed_at = now();
            $progress->save();
        } else {
            LearningProgress::create([
                'user_id'          => Auth::id(),
                'topic_id'         => $topic->id,
                'status'           => $score >= self::PASS_SCORE ? 'completed' : 'in_progress',
                'quiz_attempts'    => 1,
                'best_score'       => $score,
                'last_accessed_at' => now(),
            ]);
        }

        // Simpan data review ke session (untuk halaman hasil)
        $request->session()->put("quiz_{$topic->id}_review", [
            'topic_title' => $topic->title,
            'score'       => $score,
            'correct'     => $correct,
            'total'       => $total,
            'passed'      => $score >= self::PASS_SCORE,
            'questions'   => $reviewQuestions,
        ]);

        // Hapus sesi kuis aktif
        $request->session()->forget("quiz_{$topic->id}");

        return redirect()->route('mahasiswa.kuis.hasil', $topic);
    }
}
