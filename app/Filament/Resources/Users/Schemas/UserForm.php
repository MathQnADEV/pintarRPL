<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Select::make('role')
                    // ->relationship('roles', 'name')
                    ->options([
                        'mahasiswa' => 'mahasiswa',
                        'dosen' => 'dosen',
                        'admin' => 'admin',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set) => $state !== 'mahasiswa' ? $set('nim', null) : null)
                    ->afterStateUpdated(fn($state, $set) => $state !== 'dosen' ? $set('nip', null) : null),
                TextInput::make('nim')
                    ->label('NIM')
                    ->unique()
                    ->visible(fn(Get $get) => $get('role') === 'mahasiswa')
                    ->required(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->unique()
                    ->visible(fn(Get $get) => $get('role') === 'dosen')
                    ->required(),
                FileUpload::make('photo')
                    ->image()
                    ->directory('user-photos'),
            ]);
    }
}
