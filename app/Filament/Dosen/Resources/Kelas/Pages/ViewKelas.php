<?php

namespace App\Filament\Dosen\Resources\Kelas\Pages;

use App\Filament\Dosen\Resources\Kelas\KelasResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKelas extends ViewRecord
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
