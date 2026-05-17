<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PostTestResult;
use App\Models\PretestResult;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostTestController extends Controller
{
    private const PASS_SCORE = 60;

    // ── Tampilkan satu soal post-test ────────────────────────────────────

    public function show(Request $request): View|RedirectResponse
    {
        $user       = Auth::user();
        $level      = PretestResult::effectiveLevel($user->id);
        $sessionKey = "posttest_{$level}";

        // Buang sesi lama yang sudah selesai
        if ($request->session()->has($sessionKey)) {
            $stale = $request->session()->get($sessionKey);
            if ($stale['current'] >= count($stale['question_ids'])) {
                $request->session()->forget($sessionKey);
            }
        }

        if (! $request->session()->has($sessionKey)) {
            $questions = Question::where('is_posttest', true)
                ->where('level', $level)
                ->with('options')
                ->get()
                ->shuffle();

            if ($questions->isEmpty()) {
                return redirect()->route('mahasiswa.dashboard')
                    ->with('error', 'Soal post-test untuk level ini belum tersedia. Hubungi dosen Anda.');
            }

            $request->session()->put($sessionKey, [
                'level'        => $level,
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

        if (! $question) {
            $request->session()->forget($sessionKey);
            return redirect()->route('mahasiswa.posttest')
                ->with('error', 'Beberapa soal tidak tersedia lagi. Sesi direset — silakan mulai kembali.');
        }

        return view('posttest', [
            'question' => $question,
            'current'  => $current + 1,
            'total'    => $total,
            'progress' => (int) round(($current / $total) * 100),
            'level'    => $level,
            'user'     => $user,
        ]);
    }

    // ── Terima satu jawaban ──────────────────────────────────────────────

    public function answer(Request $request): RedirectResponse
    {
        $user       = Auth::user();
        $level      = PretestResult::effectiveLevel($user->id);
        $sessionKey = "posttest_{$level}";

        $session = $request->session()->get($sessionKey);
        if (! $session) {
            return redirect()->route('mahasiswa.posttest');
        }

        $questionId = $session['question_ids'][$session['current']] ?? null;
        $question   = $questionId ? Question::find($questionId) : null;
        $type       = $question?->type ?? 'multiple_choice';

        if ($type === 'code_arrange') {
            $request->validate([
                'answer'   => ['required', 'array', 'min:1'],
                'answer.*' => ['integer'],
            ]);
            $answerValue = array_map('intval', $request->input('answer'));
        } else {
            $request->validate(['answer' => ['required', 'integer']]);
            $answerValue = (int) $request->input('answer');
        }

        $session['answers'][$session['current']] = $answerValue;
        $session['current']++;
        $request->session()->put($sessionKey, $session);

        if ($session['current'] >= count($session['question_ids'])) {
            return $this->finishPostTest($level, $session, $request);
        }

        return redirect()->route('mahasiswa.posttest');
    }

    // ── Halaman hasil post-test ──────────────────────────────────────────

    public function hasil(Request $request): View|RedirectResponse
    {
        $user       = Auth::user();
        $reviewKey  = 'posttest_review';

        if (! $request->session()->has($reviewKey)) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return view('posttest-hasil', [
            'review' => $request->session()->get($reviewKey),
            'user'   => $user,
        ]);
    }

    // ── Helper: selesaikan post-test ─────────────────────────────────────

    private function finishPostTest(string $level, array $session, Request $request): RedirectResponse
    {
        $questionIds     = $session['question_ids'];
        $answers         = $session['answers'];
        $total           = count($questionIds);
        $correct         = 0;
        $reviewQuestions = [];

        foreach ($questionIds as $position => $questionId) {
            $answer   = $answers[$position] ?? null;
            $question = Question::with('options')->find($questionId);

            if (! $question) {
                continue;
            }

            if ($question->type === 'code_arrange') {
                // Jawaban benar = tile dengan order >= 1
                // Tile order = 0 (atau null) = pengecoh — TIDAK masuk correctOrder
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

        $score  = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= self::PASS_SCORE;

        // Simpan / update hasil post-test (simpan yang terbaik)
        $existing = PostTestResult::where('user_id', Auth::id())
            ->where('from_level', $level)
            ->first();

        if (! $existing || $score >= $existing->score) {
            PostTestResult::updateOrCreate(
                ['user_id' => Auth::id(), 'from_level' => $level],
                [
                    'score'        => $score,
                    'passed'       => $passed,
                    'completed_at' => now(),
                ]
            );
        }

        // Simpan review ke session
        $request->session()->put('posttest_review', [
            'level'   => $level,
            'score'   => $score,
            'correct' => $correct,
            'total'   => $total,
            'passed'  => $passed,
            'questions' => $reviewQuestions,
        ]);

        // Hapus sesi aktif
        $request->session()->forget("posttest_{$level}");

        return redirect()->route('mahasiswa.posttest.hasil');
    }
}
