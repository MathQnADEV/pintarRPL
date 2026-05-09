<?php

namespace App\Filament\Dosen\Resources\Topics\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label('Judul Sub Bahasan')
                    ->placeholder('contoh: Array — Pengenalan dan Operasi Dasar')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('level')
                    ->label('Level Kemampuan')
                    ->options([
                        'pemula'   => 'Pemula',
                        'menengah' => 'Menengah',
                        'lanjut'   => 'Lanjut',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('order_position')
                    ->label('Urutan dalam Level')
                    ->helperText('Angka kecil tampil lebih dulu')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi Singkat')
                    ->placeholder('Ringkasan 1–2 kalimat tentang sub bahasan ini')
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Tampilkan ke Mahasiswa')
                    ->helperText('Nonaktifkan jika materi belum siap dipublish')
                    ->default(true),

                RichEditor::make('content')
                    ->label('Konten Materi')
                    ->helperText('Tambahkan teks, gambar, atau kode program. Animasi visualisasi dapat disisipkan sebagai HTML.')
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'h2', 'h3',
                        'bulletList', 'orderedList',
                        'blockquote', 'codeBlock',
                        'link', 'attachFiles',
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
