<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Filament\Resources\Sections\SectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->getRecord()->images;
        $data['extra_fields'] = $this->getRecord()->extra_fields;
        
        // Veritabanındaki çevirileri alıp formun anlayacağı "translations.ID.alan" formatına sokuyoruz
        foreach ($this->getRecord()->translations as $translation) {
            $data['translations'][$translation->language_id] = [
                'title' => $translation->title,
                'subtitle' => $translation->subtitle,
                'description' => $translation->description,
                'content' => $translation->content,
                'buttons' => $translation->buttons,
                'images' => $translation->images,
                'extra_fields' => $translation->extra_fields,
            ];
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        
        $record->update($data);
        
        // Modelindeki o meşhur saveTranslations metodunu çağırıyoruz
        $record->saveTranslations($translations);

        return $record;
    }
}
