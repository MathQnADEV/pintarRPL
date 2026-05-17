<?php

namespace App\Filament\Dosen\Resources\Topics\Schemas;

use App\Models\Topic;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TopicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('title')
                    ->label('Judul Sub Bahasan')
                    ->columnSpanFull(),

                TextEntry::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemula'   => 'success',
                        'menengah' => 'warning',
                        'lanjut'   => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextEntry::make('order_position')
                    ->label('Urutan'),

                IconEntry::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextEntry::make('description')
                    ->label('Deskripsi')
                    ->placeholder('–')
                    ->columnSpanFull(),

                TextEntry::make('video_url')
                    ->label('Video YouTube')
                    ->placeholder('Belum ada video')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->columnSpanFull(),

                TextEntry::make('content')
                    ->label('Konten Materi')
                    ->html()
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('deleted_at')
                    ->label('Diarsipkan')
                    ->dateTime('d M Y, H:i')
                    ->visible(fn (Topic $record): bool => $record->trashed()),
            ]);
    }
}
