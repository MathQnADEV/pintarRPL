<?php

namespace App\Filament\Dosen\Resources\Questions\Schemas;

use App\Models\Question;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QuestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Tipe soal berdasarkan question_category accessor
                TextEntry::make('question_category')
                    ->label('Tipe Soal')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pretest'  => 'Pre-test',
                        'posttest' => 'Post-test',
                        default    => 'Kuis Sub Bahasan',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pretest'  => 'warning',
                        'posttest' => 'success',
                        default    => 'info',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pretest'  => 'heroicon-o-academic-cap',
                        'posttest' => 'heroicon-o-trophy',
                        default    => 'heroicon-o-pencil-square',
                    }),

                // Sub bahasan (untuk kuis) atau level (untuk posttest)
                TextEntry::make('topic.title')
                    ->label('Sub Bahasan')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(function ($state, Question $record): string {
                        if ($record->question_category === 'posttest') {
                            return 'Post-test Level ' . ucfirst($record->level ?? '—');
                        }
                        if ($record->question_category === 'pretest') {
                            return 'Pre-test Global';
                        }
                        return $state ?? '—';
                    }),

                TextEntry::make('question_text')
                    ->label('Pertanyaan')
                    ->columnSpanFull(),

                TextEntry::make('explanation')
                    ->label('Penjelasan Jawaban')
                    ->placeholder('–')
                    ->columnSpanFull(),

                RepeatableEntry::make('options')
                    ->label('Pilihan Jawaban')
                    ->schema([
                        TextEntry::make('option_text')
                            ->label('Pilihan'),
                        IconEntry::make('is_correct')
                            ->label('Benar')
                            ->boolean(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('deleted_at')
                    ->label('Diarsipkan')
                    ->dateTime('d M Y, H:i')
                    ->visible(fn (Question $record): bool => $record->trashed()),
            ]);
    }
}
