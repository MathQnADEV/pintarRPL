<?php

namespace App\Filament\Dosen\Resources\Kelas\Pages;

use App\Filament\Dosen\Resources\Kelas\KelasResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateKelas extends CreateRecord
{
    protected static string $resource = KelasResource::class;

    // Auto-isi dosen_id dengan user yang sedang login
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dosen_id'] = Auth::id();

        return $data;
    }
}
