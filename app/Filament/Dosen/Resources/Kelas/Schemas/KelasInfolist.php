<?php

namespace App\Filament\Dosen\Resources\Kelas\Schemas;

use App\Models\Kelas;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KelasInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('name')
                    ->label('Nama Kelas'),

                TextEntry::make('academic_year')
                    ->label('Tahun Akademik'),

                TextEntry::make('mata_kuliah')
                    ->label('Mata Kuliah')
                    ->badge()
                    ->color('primary'),

                TextEntry::make('program_studi')
                    ->label('Program Studi')
                    ->badge()
                    ->color('gray'),

                IconEntry::make('is_active')
                    ->label('Status Kelas')
                    ->boolean(),

                TextEntry::make('enrollments_count')
                    ->label('Jumlah Mahasiswa')
                    ->state(fn (Kelas $record): int => $record->enrollments()->count()),

                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('deleted_at')
                    ->label('Diarsipkan')
                    ->dateTime('d M Y, H:i')
                    ->visible(fn (Kelas $record): bool => $record->trashed()),
            ]);
    }
}
