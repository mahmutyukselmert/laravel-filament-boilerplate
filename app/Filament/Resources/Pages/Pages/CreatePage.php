<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $page = new Page();

        $page->fill(Arr::only($data, [
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

            $page->translateOrNew($locale)->fill($data[$locale]);
        }

        $page->save();

        return $page;
    }
}

