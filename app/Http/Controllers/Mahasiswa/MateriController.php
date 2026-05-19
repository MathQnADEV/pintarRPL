<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LearningProgress;
use App\Models\PretestResult;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MateriController extends Controller
{
    public function show(Topic $topic): View|RedirectResponse
    {
        $user  = Auth::user();
        $level = PretestResult::effectiveLevel($user->id);

        $levelOrder    = PretestResult::LEVELS;
        $userLevelIdx  = array_search($level, $levelOrder, true);
        $topicLevelIdx = array_search($topic->level, $levelOrder, true);
        $isPastLevel   = $topicLevelIdx !== false && $topicLevelIdx < $userLevelIdx;

        // Blok jika topik dari level masa depan atau tidak aktif
        if (! $topic->is_active || $topicLevelIdx === false || $topicLevelIdx > $userLevelIdx) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Materi ini tidak tersedia untuk level Anda saat ini.');
        }

        // Sidebar: tampilkan topik dari level milik topik ini (bukan hanya level saat ini)
        $topicsInLevel = Topic::where('level', $topic->level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        $topicIndex = $topicsInLevel->search(fn ($t) => $t->id === $topic->id);

        // Cek sekuensial hanya untuk level saat ini (level lampau sudah bebas dibuka)
        if (! $isPastLevel && $topicIndex > 0) {
            $prevTopic    = $topicsInLevel[$topicIndex - 1];
            $prevProgress = LearningProgress::where('user_id', $user->id)
                ->where('topic_id', $prevTopic->id)
                ->first();

            if (! $prevProgress || $prevProgress->status !== 'completed') {
                return redirect()->route('mahasiswa.dashboard')
                    ->with('error', 'Selesaikan materi dan kuis sebelumnya terlebih dahulu.');
            }
        }

        // Progress untuk topik ini
        $progress = LearningProgress::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();

        if ($isPastLevel) {
            // Level lampau: buat progress jika belum ada (misal: ditempatkan via pretest),
            // atau sekadar perbarui waktu akses jika sudah ada.
            if (! $progress) {
                $progress = LearningProgress::create([
                    'user_id'          => $user->id,
                    'topic_id'         => $topic->id,
                    'status'           => 'in_progress',
                    'last_accessed_at' => now(),
                ]);
            } else {
                $progress->update(['last_accessed_at' => now()]);
            }
        } elseif (! $progress) {
            $progress = LearningProgress::create([
                'user_id'          => $user->id,
                'topic_id'         => $topic->id,
                'status'           => 'in_progress',
                'last_accessed_at' => now(),
            ]);
        } elseif ($progress->status === 'not_started') {
            $progress->update([
                'status'           => 'in_progress',
                'last_accessed_at' => now(),
            ]);
        } else {
            $progress->update(['last_accessed_at' => now()]);
        }

        // Untuk view: $pretestResult masih dipakai di materi.blade.php
        $pretestResult = PretestResult::where('user_id', $user->id)->latest()->first();

        // Progress map untuk sidebar (status tiap topik)
        $progressMap = LearningProgress::where('user_id', $user->id)
            ->whereIn('topic_id', $topicsInLevel->pluck('id'))
            ->get()
            ->keyBy('topic_id');

        // Topik berikutnya (untuk tombol "Lanjut ke Kuis")
        $nextTopic = $topicsInLevel[$topicIndex + 1] ?? null;

        // Cek apakah ada soal kuis untuk topik ini
        $hasKuis = $topic->quizQuestions()->exists();

        return view('materi', compact(
            'topic',
            'progress',
            'topicsInLevel',
            'progressMap',
            'pretestResult',
            'nextTopic',
            'hasKuis',
            'user',
            'isPastLevel',
        ));
    }
}
