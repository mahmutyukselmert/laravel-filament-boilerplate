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
            'template',
            'sort_order',
        ]));
        $page->save();

        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $langId => $fields) {
                
                if (empty($fields['title'])) {
                    continue;
                }

                $page->translations()->create(array_merge(
                    ['language_id' => $langId],
                    $fields
                ));
            }
        }

        return $page;
    }
}

