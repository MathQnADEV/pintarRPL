<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Kelas;
use App\Models\PostTestResult;
use App\Models\PretestResult;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class MahasiswaTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public ?int $kelasId = null;

    public function mount(): void
    {
        $first = Kelas::where('dosen_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('mata_kuliah')
            ->first();

        if ($first) {
            $this->kelasId = $first->id;
        }
    }

    #[On('kelas-selected')]
    public function kelasSelected(int $kelasId): void
    {
        $this->kelasId = $kelasId;
        $this->resetTable(); // flush cached records → paksa re-query dengan kelasId baru
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->getHeading())
            ->query($this->buildQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Mahasiswa')
                    ->weight(FontWeight::SemiBold)
                    ->searchable(),

                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pretest_level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pemula'   => 'info',
                        'menengah' => 'warning',
                        'lanjut'   => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '—'),

                Tables\Columns\TextColumn::make('sub_bahasan')
                    ->label('Sub Bahasan')
                    ->state(fn (User $record): string => $record->completed_topics . '/' . $record->total_topics)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('rata_kuis')
                    ->label('Rata Kuis')
                    ->state(fn (User $record): int => (int) round($record->quiz_results_score_avg ?? 0))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (User $record): string => match (true) {
                        $record->post_test_count > 0 && (bool) $record->last_post_test_passed => 'Lulus',
                        $record->post_test_count > 0                                           => 'Tidak Lulus',
                        default                                                                => 'Belum',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lulus'       => 'success',
                        'Tidak Lulus' => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label('CSV / Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->url(fn (): string => $this->kelasId
                        ? route('dosen.export.mahasiswa.csv', ['kelas_id' => $this->kelasId])
                        : '#'
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => (bool) $this->kelasId),

                Action::make('exportPdf')
                    ->label('Cetak / PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn (): string => $this->kelasId
                        ? route('dosen.export.mahasiswa.pdf', ['kelas_id' => $this->kelasId])
                        : '#'
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => (bool) $this->kelasId),
            ])
            ->paginated(false);
    }

    // ── Helpers ─────────────────────────────────────────────────────

    protected function getHeading(): string
    {
        if (! $this->kelasId) {
            return 'Pilih kelas di atas untuk melihat data mahasiswa';
        }

        $kelas = Kelas::find($this->kelasId);

        return $kelas
            ? "Ringkasan Mahasiswa — {$kelas->mata_kuliah} ({$kelas->name})"
            : 'Ringkasan Mahasiswa';
    }

    protected function buildQuery(): Builder
    {
        // Kembalikan empty query jika belum ada kelas terpilih
        if (! $this->kelasId) {
            return User::query()->whereRaw('0 = 1');
        }

        return User::query()
            ->whereHas('kelas', fn (Builder $q) => $q->where('classes.id', $this->kelasId))
            ->select('users.*')
            // Level dari pre-test terakhir
            ->selectSub(
                PretestResult::select('level')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1),
                'pretest_level'
            )
            // Status lulus/tidak dari post-test terakhir
            ->selectSub(
                PostTestResult::select('passed')
                    ->whereColumn('user_id', 'users.id')
                    ->orderByDesc('completed_at')
                    ->limit(1),
                'last_post_test_passed'
            )
            // Total post-test yang pernah dikerjakan
            ->selectSub(
                PostTestResult::selectRaw('COUNT(*)')
                    ->whereColumn('user_id', 'users.id'),
                'post_test_count'
            )
            // Jumlah sub-bahasan selesai vs total
            ->withCount([
                'learningProgress as completed_topics' => fn (Builder $q) => $q->where('status', 'completed'),
                'learningProgress as total_topics',
            ])
            // Rata-rata skor kuis → attribute: quiz_results_score_avg
            ->withAvg('quizResults', 'score')
            ->orderBy('name');
    }
}
