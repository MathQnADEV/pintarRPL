<?php

namespace App\Filament\Dosen\Widgets;

use App\Models\Kelas;
use Filament\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelasCardWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public ?int $kelasId = null;

    // ── Lifecycle ────────────────────────────────────────────────────

    public function mount(): void
    {
        $first = Kelas::where('dosen_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('mata_kuliah')
            ->orderBy('name')
            ->first();

        if ($first) {
            $this->kelasId = $first->id;
            // Beri tahu widget lain kelas mana yang dipilih pertama kali
            $this->dispatch('kelas-selected', kelasId: $first->id);
        }
    }

    // ── Action: pilih kelas ──────────────────────────────────────────

    public function selectKelas(int $kelasId): void
    {
        $this->kelasId = $kelasId;
        $this->dispatch('kelas-selected', kelasId: $kelasId);
        $this->resetTable(); // refresh visual "Dipilih" indicator
    }

    // ── Table definition ─────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->heading('Kelas yang Diampu')
            ->description($this->buildDescription())
            ->query($this->buildQuery())
            ->contentGrid([
                'sm' => 2,
                'xl' => 4,
            ])
            ->columns([
                // Nama kelas (misal: TEKOM A)
                TextColumn::make('name')
                    ->label('Kelas')
                    ->weight(FontWeight::ExtraBold)
                    ->size(TextSize::Large)
                    ->color('primary'),

                // Badge "Dipilih" untuk kelas yang sedang aktif
                TextColumn::make('selected_badge')
                    ->label('')
                    ->state(fn (Kelas $record): ?string =>
                        $this->kelasId === $record->id ? 'Dipilih' : null
                    )
                    ->badge()
                    ->color('primary')
                    ->placeholder(''),

                // Program studi + jadwal + ruangan
                TextColumn::make('program_studi')
                    ->label('Program')
                    ->description(fn (Kelas $record): string =>
                        collect([
                            $record->schedule,
                            $record->ruangan,
                        ])->filter()->join(' · ')
                    ),

                // Tahun akademik + semester
                TextColumn::make('academic_year')
                    ->label('Tahun Akademik')
                    ->description(fn (Kelas $record): string =>
                        collect([
                            $record->semester ? "Semester {$record->semester}" : null,
                            $record->sks ? "{$record->sks} SKS" : null,
                        ])->filter()->join(' · ') ?: '—'
                    ),

                // Total mahasiswa
                TextColumn::make('mahasiswa_count')
                    ->label('Mahasiswa')
                    ->state(fn (Kelas $record): string =>
                        ($record->mahasiswa_count ?? 0) . ' mahasiswa'
                    )
                    ->weight(FontWeight::SemiBold),

                // Rata-rata kuis
                TextColumn::make('avg_quiz')
                    ->label('Rata-rata')
                    ->state(fn (Kelas $record): string =>
                        ($record->avg_quiz !== null ? (int) $record->avg_quiz : '—') . ''
                    ),

                // Jumlah yang lulus post-test
                TextColumn::make('lulus_posttest')
                    ->label('Post-test Lulus')
                    ->state(fn (Kelas $record): string =>
                        ($record->lulus_posttest ?? 0) . '/' . ($record->mahasiswa_count ?? 0)
                    ),
            ])
            ->filters([
                SelectFilter::make('program_studi')
                    ->label('Kategori Kelas')
                    ->options($this->getProgramStudiOptions())
                    ->placeholder('Semua Kelas'),
            ], FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('select')
                    ->label('Pilih Kelas')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->action(fn (Kelas $record) => $this->selectKelas($record->id)),
            ])
            ->recordAction('select')
            ->recordClasses(fn (Kelas $record) =>
                $this->kelasId === $record->id
                    ? 'ring-2 ring-inset ring-primary-500 dark:ring-primary-400'
                    : null
            )
            ->paginated(false)
            ->searchable(false);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    protected function buildQuery(): Builder
    {
        return Kelas::query()
            ->where('dosen_id', Auth::id())
            ->where('is_active', true)
            ->select('classes.*')
            // Total mahasiswa
            ->withCount('mahasiswa')
            // Rata-rata kuis seluruh mahasiswa di kelas ini
            ->selectSub(
                DB::table('quiz_results')
                    ->selectRaw('ROUND(AVG(quiz_results.score))')
                    ->join('class_enrollments', 'class_enrollments.user_id', '=', 'quiz_results.user_id')
                    ->whereColumn('class_enrollments.class_id', 'classes.id'),
                'avg_quiz'
            )
            // Jumlah mahasiswa yang pernah lulus post-test
            ->selectSub(
                DB::table('post_test_results')
                    ->selectRaw('COUNT(DISTINCT post_test_results.user_id)')
                    ->join('class_enrollments', 'class_enrollments.user_id', '=', 'post_test_results.user_id')
                    ->whereColumn('class_enrollments.class_id', 'classes.id')
                    ->where('post_test_results.passed', true),
                'lulus_posttest'
            )
            ->orderBy('mata_kuliah')
            ->orderBy('name');
    }

    protected function buildDescription(): string
    {
        $kelas = Kelas::where('dosen_id', Auth::id())
            ->where('is_active', true)
            ->get();

        if ($kelas->isEmpty()) {
            return 'Belum ada kelas aktif. Buat kelas baru untuk memulai.';
        }

        $totalKelas     = $kelas->count();
        $totalMahasiswa = $kelas->sum(fn ($k) => $k->mahasiswa()->count());
        $mataKuliah     = $kelas->pluck('mata_kuliah')->unique()->join(' · ');

        return "Klik kartu kelas untuk melihat ringkasan · {$totalKelas} kelas aktif · {$totalMahasiswa} mahasiswa · {$mataKuliah}";
    }

    protected function getProgramStudiOptions(): array
    {
        return Kelas::where('dosen_id', Auth::id())
            ->where('is_active', true)
            ->distinct('program_studi')
            ->pluck('program_studi', 'program_studi')
            ->all();
    }
}
