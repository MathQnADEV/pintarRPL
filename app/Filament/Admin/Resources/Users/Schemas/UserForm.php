<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn(string $operation) => $operation === 'create')
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel(),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'mahasiswa' => 'Mahasiswa',
                        'dosen'     => 'Dosen',
                        'admin'     => 'Admin',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn($state, callable $set) => $state !== 'mahasiswa' ? $set('nim', null) : null),
                TextInput::make('nim')
                    ->label('NIM')
                    ->unique(ignoreRecord: true)
                    ->visible(fn(Get $get) => $get('role') === 'mahasiswa' || $get('role') === ['mahasiswa']),
                TextInput::make('nip')
                    ->label('NIP')
                    ->unique(ignoreRecord: true)
                    ->visible(fn(Get $get) => $get('role') === 'dosen' || $get('role') === ['dosen']),
                Toggle::make('is_active')
                    ->label('Akun Aktif')
                    ->default(true),
                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('user-photos'),
            ]);
    }
}
