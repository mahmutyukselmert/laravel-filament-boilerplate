<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Bunu ekle

class Section extends Model
{
    protected $fillable = ['admin_title', 'key', 'type', 'images', 'extra_fields', 'is_active', 'sort_order'];

    protected $casts = [
        'images' => 'array',
        'extra_fields' => 'array',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(SectionTranslation::class);
    }

    /**
     * Filament'ten gelen çeviri dizisini veritabanına işler.
     */
    public function saveTranslations(array $translations): void
    {
        foreach ($translations as $langId => $data) {
            // Dil ID'si ve Section ID'sine göre kaydı bul veya güncelle
            $this->translations()->updateOrCreate(
                ['language_id' => $langId],
                [
                    'title'       => $data['title'] ?? null,
                    'subtitle'    => $data['subtitle'] ?? null,
                    'description' => $data['description'] ?? null,
                    'content'     => $data['content'] ?? null,
                    'buttons'     => $data['buttons'] ?? null,
                    'images'      => $data['images'] ?? null,
                    'extra_fields' => $data['extra_fields'] ?? null,
                ]
            );
        }
    }
}