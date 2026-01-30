<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'title',
        'code',
        'is_default',
        'active',
        'sort_order',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    protected static function booted(): void
    {
        static::saving(function (self $language): void {
            if (! $language->active) {
                $language->is_default = false;
            }
        });

        static::saved(function (self $language): void {
            if (! $language->is_default) {
                return;
            }

            static::withoutEvents(function () use ($language): void {
                self::query()
                    ->whereKeyNot($language->getKey())
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            });
        });
    }

    public static function getServicePrefix($locale)
    {
        $prefixes = [
            'tr' => 'transfer',
            'en' => 'services',
            'de' => 'leistungen',
            'ru' => 'uslugi'
        ];
        return $prefixes[$locale] ?? 'services';
    }
}
