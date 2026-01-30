<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Language;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $service = new Service();

        // Ana tablo verilerini doldur
        $service->fill(Arr::only($data, [
            'image',
            'icon',
            'is_active',
            'is_featured',
            'sort_order',
        ]));

        $service->save();

        // Çevirileri kaydet
        foreach ($data['translations'] ?? [] as $langId => $fields) {
            // KRİTİK NOKTA: Eğer başlık girilmemişse bu dilin çevirisini oluşturma
            if (empty($fields['title'])) {
                continue;
            }

            $service->translations()->create([
                'language_id' => $langId,
                'title'             => $fields['title'],
                'slug'              => $fields['slug'] ?? \Illuminate\Support\Str::slug($fields['title']),
                'subtitle'          => $fields['subtitle'] ?? null,
                'short_description' => $fields['short_description'] ?? null,
                'content'           => $fields['content'] ?? null,
                'meta_title'        => $fields['meta_title'] ?? null,
                'meta_description'  => $fields['meta_description'] ?? null,
                'meta_keywords'     => $fields['meta_keywords'] ?? null,
                // Gallery veya diğer json alanların varsa onları da buraya ekle
            ]);
        }

        return $service;
    }
}