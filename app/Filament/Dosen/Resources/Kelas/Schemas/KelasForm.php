<?php

namespace App\Filament\Dosen\Resources\Kelas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                // ── Informasi Mata Kuliah ────────────────────────────────
                Section::make('Informasi Mata Kuliah')
                    ->schema([
                        TextInput::make('mata_kuliah')
                            ->label('Nama Mata Kuliah')
                            ->placeholder('contoh: Struktur Data')
                            ->required()
                            ->maxLength(100),

                        Select::make('program_studi')
                            ->label('Program Studi')
                            ->options([
                                'Teknik Komputer'               => 'Teknik Komputer',
                                'Pendidikan Teknik Informatika dan Komputer' => 'Pendidikan Teknik Informatika dan Komputer',
                            ])
                            ->searchable()
                            ->required()
                            ->native(false),

                        TextInput::make('academic_year')
                            ->label('Tahun Akademik')
                            ->placeholder('contoh: 2025/2026')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('semester')
                            ->label('Semester')
                            ->placeholder('contoh: 2')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(8),

                        TextInput::make('sks')
                            ->label('SKS')
                            ->placeholder('contoh: 4')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(6),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ── Informasi Kelas ──────────────────────────────────────
                Section::make('Detail Kelas')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama / Kode Kelas')
                            ->placeholder('contoh: TEKOM A')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('schedule')
                            ->label('Jadwal')
                            ->placeholder('contoh: Senin, 08.00-09.40')
                            ->maxLength(60),

                        TextInput::make('ruangan')
                            ->label('Ruangan')
                            ->placeholder('contoh: AE-201')
                            ->maxLength(50),

                        Toggle::make('is_active')
                            ->label('Kelas Aktif')
                            ->helperText('Nonaktifkan jika semester sudah selesai')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
