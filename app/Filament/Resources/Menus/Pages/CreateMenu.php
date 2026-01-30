<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Çeviri verilerini ayır
        $translations = Arr::pull($data, 'translations', []);

        // 2. Ana kaydı oluştur
        $record = static::getModel()::create($data);

        // 3. Çevirileri kaydet
        if (!empty($translations)) {
            foreach ($translations as $langId => $fields) {
                if (empty($fields['title'])) continue;

                $record->translations()->create([
                    'language_id' => $langId,
                    'title' => $fields['title'],
                    // 'extra_fields' varsa buraya ekleyebilirsin
                ]);
            }
        }

        return $record;
    }
}
