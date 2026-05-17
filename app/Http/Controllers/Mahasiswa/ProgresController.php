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

class ProgresController extends Controller
{
    public function index(): View
    {
        $user          = Auth::user();
        $pretestResult = PretestResult::where('user_id', $user->id)->latest()->first();
        $level         = PretestResult::effectiveLevel($user->id);

        // ── Semua level ──────────────────────────────────────────────
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

        // ── Stat ringkasan untuk level saat ini ──────────────────────
        $topics         = $allLevelTopics[$level] ?? collect();
        $completedCount = $allProgressMap->whereIn('topic_id', $topics->pluck('id')->all())
            ->where('status', 'completed')->count();
        $totalCount     = $topics->count();
        $percentage     = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;
        $postTestResult = $allPostTestResults[$level] ?? null;

        return view('progres', compact(
            'user',
            'pretestResult',
            'level',
            'allLevelTopics',
            'allProgressMap',
            'allQuizMap',
            'allPostTestResults',
            'completedCount',
            'totalCount',
            'percentage',
            'postTestResult',
        ));
    }
}
