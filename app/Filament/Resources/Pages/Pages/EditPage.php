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

        $translatedAttributes = $this->record->translatedAttributes ?? [];

        $locales = Language::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();

        foreach ($locales as $locale) {
            $translation = $this->record->translate($locale, false);

            $data[$locale] = $translation
                ? Arr::only($translation->toArray(), $translatedAttributes)
                : [];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill(Arr::only($data, [
            'image',
            'is_active',
            'sort_order',
        ]));

        $locales = Language::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();

        foreach ($locales as $locale) {
            if (! isset($data[$locale]) || ! is_array($data[$locale])) {
                continue;
            }

            $record->translateOrNew($locale)->fill($data[$locale]);
        }

        $record->save();

        return $record;
    }
}

