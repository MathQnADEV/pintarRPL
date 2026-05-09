<?php

namespace App\Filament\Dosen\Resources\Kelas\RelationManagers;

use App\Models\Kelas;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'mahasiswa';

    protected static ?string $title = 'Daftar Mahasiswa';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->placeholder('–'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Mahasiswa')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        fn ($query) => $query->role('mahasiswa')->orderBy('name')
                    )
                    ->recordSelectSearchColumns(['name', 'nim', 'email'])
                    ->recordTitle(
                        fn ($record): string => $record->name . ($record->nim ? ' — ' . $record->nim : '')
                    )
                    ->before(function (AttachAction $action, array $data): void {
                        $userId = $data['recordId'] ?? null;

                        if (! $userId) {
                            return;
                        }

                        // Cek apakah mahasiswa sudah terdaftar di kelas LAIN
                        $existingEnrollment = DB::table('class_enrollments')
                            ->where('user_id', $userId)
                            ->where('class_id', '!=', $this->getOwnerRecord()->getKey())
                            ->first();

                        if (! $existingEnrollment) {
                            return;
                        }

                        // Ambil info kelas yang sudah didaftari
                        $kelasLain = Kelas::find($existingEnrollment->class_id);

                        Notification::make()
                            ->title('Mahasiswa Sudah Terdaftar di Kelas Lain')
                            ->body(
                                'Mahasiswa ini sudah terdaftar di kelas ' .
                                ($kelasLain
                                    ? "**{$kelasLain->mata_kuliah} — {$kelasLain->name}**"
                                    : 'lain') .
                                '. Satu mahasiswa hanya dapat masuk ke satu kelas.'
                            )
                            ->warning()
                            ->persistent()
                            ->send();

                        $action->halt();
                    }),
            ])
            ->recordActions([
                DetachAction::make()->label('Keluarkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()->label('Keluarkan Terpilih'),
                ]),
            ]);
    }
}
