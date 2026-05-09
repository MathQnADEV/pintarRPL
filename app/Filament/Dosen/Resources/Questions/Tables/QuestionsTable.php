<?php

namespace App\Filament\Dosen\Resources\Questions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
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
                    ->limit(70)
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
                // Filter berdasarkan tipe soal
                Filter::make('pretest')
                    ->label('Soal Pre-test')
                    ->query(fn (Builder $q) => $q->where('is_pretest', true))
                    ->toggle(),

                Filter::make('kuis')
                    ->label('Soal Kuis')
                    ->query(fn (Builder $q) => $q->where('is_pretest', false)->where('is_posttest', false))
                    ->toggle(),

                Filter::make('posttest')
                    ->label('Soal Post-test')
                    ->query(fn (Builder $q) => $q->where('is_posttest', true))
                    ->toggle(),

                // Filter berdasarkan sub bahasan (untuk kuis)
                SelectFilter::make('topic_id')
                    ->label('Filter Sub Bahasan')
                    ->relationship('topic', 'title')
                    ->searchable()
                    ->preload(),

                // Filter berdasarkan level (untuk post-test)
                SelectFilter::make('level')
                    ->label('Filter Level')
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
