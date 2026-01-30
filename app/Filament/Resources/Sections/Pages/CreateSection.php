<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Filament\Resources\Sections\SectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        
        $record = static::getModel()::create($data);

        $record->saveTranslations($translations);

        return $record;
    }
}
