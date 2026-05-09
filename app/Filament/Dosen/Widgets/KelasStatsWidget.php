<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Kelas;
use App\Models\LearningProgress;
use App\Models\QuizResult;
use App\Models\Topic;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class KelasStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    // Kelas yang sedang aktif — diterima dari KelasCardWidget via event
    public ?int $kelasId = null;

    // ── Event listener ───────────────────────────────────────────────

    #[On('kelas-selected')]
    public function kelasSelected(int $kelasId): void
    {
        $this->kelasId     = $kelasId;
        $this->cachedStats = null; // paksa getStats() dipanggil ulang
    }

    // ── Schema: tambah judul + action buttons di atas stat cards ─────

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            // Header section dengan judul, subtitle, dan tombol aksi
            Section::make($this->getKelasTitle())
                ->description($this->getKelasSubtitle())
                ->headerActions($this->getSectionHeaderActions())
                ->contained(false),

            // Stat cards
            $this->getSectionContentComponent(),
        ]);
    }

    // ── Judul & subtitle ─────────────────────────────────────────────

    protected function getKelasTitle(): string
    {
        if (! $this->kelasId) {
            return 'Pilih kelas di atas untuk melihat ringkasan';
        }

        $kelas = Kelas::find($this->kelasId);

        return $kelas
            ? "{$kelas->mata_kuliah} — {$kelas->name} · Ringkasan"
            : 'Ringkasan Kelas';
    }

    protected function getKelasSubtitle(): string
    {
        if (! $this->kelasId) {
            return 'Klik salah satu kartu kelas di atas.';
        }

        $kelas = Kelas::find($this->kelasId);

        if (! $kelas) {
            return '';
        }

        return collect([
            $kelas->mahasiswa()->count() . ' mahasiswa',
            $kelas->program_studi,
            $kelas->schedule,
            $kelas->ruangan,
        ])->filter()->join(' · ');
    }

    // ── Action buttons di pojok kanan section ────────────────────────

    protected function getSectionHeaderActions(): array
    {
        if (! $this->kelasId) {
            return [];
        }

        return [
            Action::make('kelolaMateri')
                ->label('Kelola Materi')
                ->icon('heroicon-o-book-open')
                ->color('primary')
                ->url(route('filament.dosen.resources.topics.index')),

            Action::make('bankSoal')
                ->label('Bank Soal')
                ->icon('heroicon-o-question-mark-circle')
                ->color('info')
                ->url(route('filament.dosen.resources.questions.index')),

            Action::make('exportLaporan')
                ->label('Laporan CSV/PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('dosen.export.mahasiswa.csv', ['kelas_id' => $this->kelasId]))
                ->openUrlInNewTab(),
        ];
    }

    // ── Stat cards ────────────────────────────────────────────────────

    protected function getStats(): array
    {
        if (! $this->kelasId) {
            return [
                Stat::make('Total Mahasiswa', '—')->icon('heroicon-o-users'),
                Stat::make('Aktif Minggu Ini', '—')->icon('heroicon-o-fire'),
                Stat::make('Rata-rata Kuis', '—')->icon('heroicon-o-chart-bar'),
                Stat::make('Median Sub-bahasan', '—')->icon('heroicon-o-book-open'),
            ];
        }

        $kelas        = Kelas::with('mahasiswa')->find($this->kelasId);
        $mahasiswaIds = $kelas?->mahasiswa->pluck('id') ?? collect();
        $total        = $mahasiswaIds->count();

        // Mahasiswa yang mengerjakan kuis dalam 7 hari terakhir
        $aktif = QuizResult::whereIn('user_id', $mahasiswaIds)
            ->where('completed_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Rata-rata nilai kuis
        $avgQuiz = (int) round(
            QuizResult::whereIn('user_id', $mahasiswaIds)->avg('score') ?? 0
        );

        // Median sub-bahasan selesai per mahasiswa
        $medianStr = $this->computeMedianSubBahasan($mahasiswaIds);

        return [
            Stat::make('Total Mahasiswa', $total)
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Aktif Minggu Ini', $aktif)
                ->description("dari {$total} mahasiswa")
                ->icon('heroicon-o-fire')
                ->color('warning'),

            Stat::make('Rata-rata Kuis', $avgQuiz)
                ->icon('heroicon-o-chart-bar')
                ->color($avgQuiz >= 70 ? 'success' : ($avgQuiz >= 50 ? 'warning' : 'danger')),

            Stat::make('Median Sub-bahasan', $medianStr)
                ->icon('heroicon-o-book-open')
                ->color('primary'),
        ];
    }

    /**
     * Hitung median jumlah sub-bahasan selesai per mahasiswa.
     * Mengembalikan "X/Y" — X = median selesai, Y = total topik aktif.
     */
    protected function computeMedianSubBahasan(Collection $mahasiswaIds): string
    {
        $totalTopics = Topic::where('is_active', true)->count();

        if ($mahasiswaIds->isEmpty() || $totalTopics === 0) {
            return "—/{$totalTopics}";
        }

        $counts = LearningProgress::whereIn('user_id', $mahasiswaIds)
            ->where('status', 'completed')
            ->selectRaw('user_id, COUNT(*) as completed')
            ->groupBy('user_id')
            ->pluck('completed');

        // Mahasiswa tanpa progress dihitung sebagai 0
        $noProgress = $mahasiswaIds->count() - $counts->count();
        for ($i = 0; $i < $noProgress; $i++) {
            $counts->push(0);
        }

        $sorted = $counts->sort()->values();
        $count  = $sorted->count();
        $median = $count % 2 === 0
            ? (int) round(($sorted[$count / 2 - 1] + $sorted[$count / 2]) / 2)
            : $sorted[intdiv($count, 2)];

        return "{$median}/{$totalTopics}";
    }
}
