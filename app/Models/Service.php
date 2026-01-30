<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['image', 'icon', 'is_active', 'is_featured', 'sort_order'];

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    /**
     * Hizmete ait galeriyi getirir.
     */
    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class, 'model_id')
            ->where('model_type', 'service') // Sadece hizmete ait olanları çek
            ->orderBy('sort_order');
    }

    public function activeTranslation(): HasOne
    {
        return $this->hasOne(ServiceTranslation::class)
            ->where('language_id', app()->getLocale() === 'tr' ? 1 : 2);
    }

    public function getActiveTranslationAttribute()
    {
        // 1. Önce aktif dildeki çeviriyi ara
        $locale = app()->getLocale();
        $translation = $this->translations->where('language_id', function($query) use ($locale) {
            return \App\Models\Language::where('code', $locale)->first()?->id;
        })->first();

        // 2. Yoksa varsayılan dildeki (ID: 1) çeviriyi getir
        if (!$translation) {
            $translation = $this->translations->where('language_id', 1)->first();
        }

        return $translation;
    }

    public function getDynamicUrl()
    {
        $locale = app()->getLocale();
        $prefix = \App\Models\Language::getServicePrefix($locale);
        $translation = $this->activeTranslation; // Daha önce tanımladığımız attribute

        if (!$translation) return '#';

        // Eğer dil Türkçe ise (varsayılan) /tr ekleme
        $langPath = ($locale === 'tr') ? '' : "/{$locale}";
        
        return url("{$langPath}/{$prefix}/{$translation->slug}");
    }
}