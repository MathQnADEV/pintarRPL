<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use App\Models\PretestResult;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function show(Topic $topic): View|RedirectResponse
    {
        $user  = Auth::user();
        $level = PretestResult::effectiveLevel($user->id);

        // Topik boleh dari level saat ini ATAU level yang sudah dilewati
        $levelOrder    = PretestResult::LEVELS;
        $userLevelIdx  = array_search($level, $levelOrder, true);
        $topicLevelIdx = array_search($topic->level, $levelOrder, true);

        if (! $topic->is_active || $topicLevelIdx === false || $topicLevelIdx > $userLevelIdx) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Topik ini tidak tersedia untuk level Anda.');
        }

        // User harus sudah pernah membuka topik ini (minimal in_progress)
        $progress = LearningProgress::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->whereIn('status', ['in_progress', 'completed'])
            ->first();

        if (! $progress) {
            return redirect()->route('mahasiswa.progres')
                ->with('info', 'Kerjakan kuis topik ini terlebih dahulu agar bisa mereview soalnya.');
        }

        $questions = Question::where('topic_id', $topic->id)
            ->where('is_pretest', false)
            ->where('is_posttest', false)
            ->with('options')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('mahasiswa.progres')
                ->with('error', 'Belum ada soal kuis untuk topik ini.');
        }

        $quizResult = QuizResult::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();

        return view('review-soal', [
            'topic'      => $topic,
            'questions'  => $questions,
            'progress'   => $progress,
            'quizResult' => $quizResult,
            'user'       => $user,
        ]);
    }
}
