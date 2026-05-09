<?php

namespace App\Filament\Dosen\Resources\Questions\Pages;

use App\Filament\Dosen\Resources\Questions\QuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;
}
