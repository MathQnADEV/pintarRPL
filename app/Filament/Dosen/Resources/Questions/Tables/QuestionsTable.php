<?php

namespace App\Filament\Dosen\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Tipe soal: Pre-test / Kuis / Post-test
                TextColumn::make('question_category')
                    ->label('Tipe')
                    ->badge()
                    ->state(fn ($record): string => match ($record->question_category) {
                        'pretest'  => 'Pre-test',
                        'posttest' => 'Post-test',
                        default    => 'Kuis',
                    })
                    ->color(fn ($record): string => match ($record->question_category) {
                        'pretest'  => 'warning',
                        'posttest' => 'success',
                        default    => 'info',
                    })
                    ->icon(fn ($record): string => match ($record->question_category) {
                        'pretest'  => 'heroicon-o-academic-cap',
                        'posttest' => 'heroicon-o-trophy',
                        default    => 'heroicon-o-pencil-square',
                    })
                    ->sortable(false),

                // Sub bahasan (hanya untuk kuis) — atau level untuk posttest
                TextColumn::make('topic.title')
                    ->label('Sub Bahasan / Level')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(function ($state, $record): string {
                        if ($record->question_category === 'posttest') {
                            return 'Post-test ' . ucfirst($record->level ?? '—');
                        }
                        if ($record->question_category === 'pretest') {
                            return 'Pre-test Global';
                        }
                        return $state ?? '—';
                    })
                    ->searchable(),

                TextColumn::make('question_text')
                    ->label('Pertanyaan')
                    ->formatStateUsing(fn (string $state): string => trim(strip_tags($state)))
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('options_count')
                    ->label('Pilihan')
                    ->counts('options')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                /*
                 * Filter tipe soal — pakai Filter::make() dengan form Select
                 * supaya query-nya pasti dijalankan (SelectFilter::query() tidak
                 * berjalan di Filament v4 untuk kolom virtual).
                 */
                Filter::make('tipe_soal')
                    ->form([
                        Select::make('tipe')
                            ->label('Tipe Soal')
                            ->options([
                                'pretest'  => '🎓 Pre-test',
                                'kuis'     => '✏️ Kuis',
                                'posttest' => '🏆 Post-test',
                            ])
                            ->placeholder('— Semua Tipe —')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['tipe'] ?? null) {
                            'pretest'  => $query->where('is_pretest', true)
                                               ->where('is_posttest', false),
                            'kuis'     => $query->where('is_pretest', false)
                                               ->where('is_posttest', false),
                            'posttest' => $query->where('is_posttest', true)
                                               ->where('is_pretest', false),
                            default    => $query,   // tidak ada pilihan → tampilkan semua
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $labels = [
                            'pretest'  => '🎓 Pre-test',
                            'kuis'     => '✏️ Kuis',
                            'posttest' => '🏆 Post-test',
                        ];
                        $tipe = $data['tipe'] ?? null;
                        return $tipe ? 'Tipe: ' . ($labels[$tipe] ?? $tipe) : null;
                    }),

                // Filter berdasarkan sub bahasan (untuk kuis)
                SelectFilter::make('topic_id')
                    ->label('Sub Bahasan')
                    ->relationship('topic', 'title')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan level (untuk pre-test & post-test)
                SelectFilter::make('level')
                    ->label('Level')
                    ->options([
                        'pemula'   => 'Pemula',
                        'menengah' => 'Menengah',
                        'lanjut'   => 'Lanjut',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
