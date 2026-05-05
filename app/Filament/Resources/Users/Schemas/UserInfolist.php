<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                ImageEntry::make('photo')
                    ->circular()
                    ->placeholder('No photo'),
                TextEntry::make('roles.name')
                    ->label('Role')
                    ->placeholder('-'),
                TextEntry::make('nim')
                    ->label('NIM')
                    ->visible(fn (User $record): bool => $record->hasRole('mahasiswa')),
                TextEntry::make('nip')
                    ->label('NIP')
                    ->visible(fn (User $record): bool => $record->hasRole('dosen')),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
            ]);
    }
}
