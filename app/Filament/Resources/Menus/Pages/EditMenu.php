<?php

namespace App\Filament\Resources\Menus\Pages;

use App\Filament\Resources\Menus\MenuResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use App\Models\Language;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Form açıldığında veritabanındaki çevirileri forma yükler
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $languages = Language::where('active', true)->get();

        foreach ($languages as $lang) {
            $translation = $this->record->translations()
                ->where('language_id', $lang->id)
                ->first();

            if ($translation) {
                $data['translations'][$lang->id] = [
                    'title' => $translation->title,
                ];
            }
        }

        return $data;
    }

    /**
     * Kaydet butonuna basıldığında hem ana kaydı hem çevirileri günceller
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Çeviri verilerini çek ve ana datadan temizle
        $translations = Arr::pull($data, 'translations', []);

        // 2. Ana tabloyu güncelle
        $record->update($data);

        // 3. Çevirileri güncelle veya yoksa oluştur (updateOrCreate)
        if (!empty($translations)) {
            foreach ($translations as $langId => $fields) {
                if (empty($fields['title'])) {
                    // Eğer title boşsa ve veritabanında varsa silebilirsin de (tercihe bağlı)
                    continue;
                }

                $record->translations()->updateOrCreate(
                    ['language_id' => $langId],
                    ['title' => $fields['title']]
                );
            }
        }

        return $record;
    }
}
