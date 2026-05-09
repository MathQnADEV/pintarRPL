<?php

namespace App\Filament\Dosen\Resources\Kelas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KelasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mata_kuliah')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('program_studi')
                    ->label('Program Studi')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('academic_year')
                    ->label('Tahun Akademik')
                    ->sortable(),

                TextColumn::make('enrollments_count')
                    ->label('Mahasiswa')
                    ->counts('enrollments')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('program_studi')
                    ->label('Filter Prodi')
                    ->options([
                        'Teknik Komputer'           => 'Teknik Komputer',
                        'Teknik Informatika'        => 'Teknik Informatika',
                        'Sistem Informasi'          => 'Sistem Informasi',
                        'PTK'                       => 'PTK',
                        'Pendidikan Teknik Informatika' => 'Pendidikan Teknik Informatika',
                    ]),
                SelectFilter::make('academic_year')
                    ->label('Filter T.A.')
                    ->relationship('', 'academic_year'),
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
