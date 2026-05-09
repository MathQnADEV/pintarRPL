<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Question;
use App\Models\Topic;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Menampilkan ringkasan konten pembelajaran berdasarkan alur:
 * Pre-test → Level Pemula → Post-test Pemula → Level Menengah → ... dst.
 *
 * Widget ini membantu dosen memastikan semua komponen pembelajaran sudah
 * tersedia sebelum mahasiswa mulai belajar.
 */
class LearningFlowWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;  // tampil paling atas di dashboard

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Konten Pembelajaran — Ringkasan Alur';

    protected ?string $description =
        'Alur: Pre-test Awal → Materi Level Pemula → Post-test Pemula → Materi Level Menengah → Post-test Menengah → Materi Level Lanjut → Post-test Lanjut';

    protected function getStats(): array
    {
        // ── Hitung soal pre-test ─────────────────────────────────────
        $pretestCount = Question::where('is_pretest', true)->count();

        // ── Hitung materi & soal kuis per level ──────────────────────
        $levels = ['pemula', 'menengah', 'lanjut'];

        $topicCounts = Topic::where('is_active', true)
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        $quizCounts = Question::where('is_pretest', false)
            ->where('is_posttest', false)
            ->whereNotNull('topic_id')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->selectRaw('topics.level, COUNT(questions.id) as total')
            ->groupBy('topics.level')
            ->pluck('total', 'topics.level');

        // ── Hitung soal post-test per level ──────────────────────────
        $posttestCounts = Question::where('is_posttest', true)
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        // ── Bangun stat cards ─────────────────────────────────────────

        $stats = [];

        // 1. Pre-test Pool
        $stats[] = Stat::make('Soal Pre-test', $pretestCount)
            ->description('Pool soal untuk menentukan level awal mahasiswa')
            ->icon('heroicon-o-academic-cap')
            ->color($pretestCount > 0 ? 'warning' : 'danger')
            ->extraAttributes(['title' => 'Soal pre-test tidak terkait topik tertentu']);

        // 2–4. Per level: materi + kuis + posttest
        $levelLabels = [
            'pemula'   => 'Pemula',
            'menengah' => 'Menengah',
            'lanjut'   => 'Lanjut',
        ];
        $levelColors = [
            'pemula'   => 'info',
            'menengah' => 'warning',
            'lanjut'   => 'danger',
        ];

        foreach ($levels as $level) {
            $topicTotal   = $topicCounts[$level]   ?? 0;
            $quizTotal    = $quizCounts[$level]     ?? 0;
            $posttestTotal = $posttestCounts[$level] ?? 0;

            $label = $levelLabels[$level];
            $color = $levelColors[$level];

            $stats[] = Stat::make("Materi {$label}", $topicTotal . ' sub bahasan')
                ->description("{$quizTotal} soal kuis tersedia")
                ->icon('heroicon-o-book-open')
                ->color($topicTotal > 0 ? $color : 'gray');

            $stats[] = Stat::make("Post-test {$label}", $posttestTotal . ' soal')
                ->description("Syarat naik dari level {$label}")
                ->icon('heroicon-o-trophy')
                ->color($posttestTotal > 0 ? 'success' : 'gray');
        }

        return $stats;
    }
}
