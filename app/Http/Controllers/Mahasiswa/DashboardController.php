<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use App\Models\PostTestResult;
use App\Models\PretestResult;
use App\Models\QuizResult;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class DashboardController extends Controller
{
    public function index(): View
    {
        $user          = Auth::user();
        $pretestResult = PretestResult::where('user_id', $user->id)->latest()->first();
        $level         = PretestResult::effectiveLevel($user->id); // pemula | menengah | lanjut

        // ── Topik untuk level mahasiswa ini ─────────────────────────
        $topics = Topic::where('level', $level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        // ── Progress per topik ───────────────────────────────────────
        $progressMap = LearningProgress::where('user_id', $user->id)
            ->whereIn('topic_id', $topics->pluck('id'))
            ->get()
            ->keyBy('topic_id');

        // ── Nilai kuis per topik ─────────────────────────────────────
        $quizMap = QuizResult::where('user_id', $user->id)
            ->whereIn('topic_id', $topics->pluck('id'))
            ->orderByDesc('score')  // ambil skor terbaik jika retry
            ->get()
            ->keyBy('topic_id');

        // ── Statistik ringkasan ──────────────────────────────────────
        $completedCount = $progressMap->where('status', 'completed')->count();
        $totalCount     = $topics->count();
        $avgQuiz        = $quizMap->isNotEmpty()
            ? (int) round($quizMap->avg('score'))
            : 0;

        // ── Status post-test untuk level ini ────────────────────────
        $postTestResult = PostTestResult::where('user_id', $user->id)
            ->where('from_level', $level)
            ->latest()
            ->first();

        $postTestUnlocked = ($completedCount >= $totalCount) && $totalCount > 0;

        // ── Topik "saat ini" = topik pertama yang belum selesai ──────
        $currentTopicId = null;
        foreach ($topics as $topic) {
            $progress = $progressMap->get($topic->id);
            if (! $progress || $progress->status !== 'completed') {
                $currentTopicId = $topic->id;
                break;
            }
        }

        // ── Peta belajar: semua level ────────────────────────────────
        $allLevelTopics = collect(PretestResult::LEVELS)
            ->mapWithKeys(fn ($lvl) => [
                $lvl => Topic::where('level', $lvl)
                    ->where('is_active', true)
                    ->orderBy('order_position')
                    ->get(),
            ]);

        $allTopicIds = $allLevelTopics->flatten()->pluck('id');

        $allProgressMap = LearningProgress::where('user_id', $user->id)
            ->whereIn('topic_id', $allTopicIds)
            ->get()
            ->keyBy('topic_id');

        $allQuizMap = QuizResult::where('user_id', $user->id)
            ->whereIn('topic_id', $allTopicIds)
            ->get()
            ->keyBy('topic_id');

        $allPostTestResults = PostTestResult::where('user_id', $user->id)
            ->get()
            ->keyBy('from_level');

        // ── Kelas yang diikuti ───────────────────────────────────────
        $kelas = $user->kelas()->first();

        return view('dashboard-mahasiswa', compact(
            'user',
            'level',
            'pretestResult',
            'topics',
            'progressMap',
            'quizMap',
            'completedCount',
            'totalCount',
            'avgQuiz',
            'currentTopicId',
            'postTestResult',
            'postTestUnlocked',
            'allLevelTopics',
            'allProgressMap',
            'allQuizMap',
            'allPostTestResults',
            'kelas',
        ));
    }
}
