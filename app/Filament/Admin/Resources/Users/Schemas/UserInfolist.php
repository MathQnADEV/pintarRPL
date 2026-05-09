<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama'),
                TextEntry::make('email')
                    ->label('Email'),
                TextEntry::make('phone')
                    ->label('No. Telepon')
                    ->placeholder('-'),
                TextEntry::make('roles.name')
                    ->label('Role')
                    ->badge(),
                TextEntry::make('nim')
                    ->label('NIM')
                    ->visible(fn(User $record): bool => $record->hasRole('mahasiswa'))
                    ->placeholder('-'),
                TextEntry::make('nip')
                    ->label('NIP')
                    ->visible(fn(User $record): bool => $record->hasRole('dosen'))
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label('Status Akun')
                    ->boolean(),
                ImageEntry::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->placeholder('Tidak ada foto'),
                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label('Dinonaktifkan')
                    ->dateTime()
                    ->visible(fn(User $record): bool => $record->trashed()),
            ]);
    }
}
