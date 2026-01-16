<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        unset($data['translations']);

        $languages = Language::where('active', true)->orderBy('sort_order')->get();

        foreach ($languages as $lang) {
            $translation = $this->record->translations()
                ->where('language_id', $lang->id)
                ->first();

            $data['translations'][$lang->id] = $translation
                ? Arr::only($translation->toArray(), [
                    'title', 'slug', 'subtitle', 'short_description',
                    'content', 'sections', 'meta_title', 'meta_description', 'meta_keywords'
                ])
                : [];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Ana tablo güncelle
        $record->fill(Arr::only($data, ['image', 'is_active', 'sort_order']));
        $record->save();

        // Çeviriler güncelle
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $langId => $fields) {
                $record->translations()->updateOrCreate(
                    ['language_id' => $langId],
                    $fields
                );
            }
        }

        return $record;
    }
}
