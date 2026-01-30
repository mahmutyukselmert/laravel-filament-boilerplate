<?php
use App\Models\SiteSetting;
use App\Helpers\ContactHelper;

if (! function_exists('get_section')) {
    function get_section($key)
    {
        $currentLangId = session('language_id', 1); // Aktif dil
        $defaultLangId = 1; // Varsayılan dil (Türkçe)

        $section = \App\Models\Section::where('key', $key)
            ->where('is_active', 1)
            ->with(['translations']) // Tüm çevirileri çek
            ->first();

        if (! $section) {
            return null;
        }

        // 1. Önce aktif dile ait çeviriyi ara
        $translation = $section->translations->where('language_id', $currentLangId)->first();

        // 2. Eğer aktif dilde çeviri yoksa ve aktif dil TR değilse, TR çevirisini getir
        if (! $translation && $currentLangId != $defaultLangId) {
            $translation = $section->translations->where('language_id', $defaultLangId)->first();
        }

        // Blade'de kolay erişim için translation'ı section nesnesine inject edelim
        $section->active_translation = $translation;

        return $section;
    }
}

if (! function_exists('settings')) {
    function settings(): ?SiteSetting
    {
        return cache()->rememberForever('site_settings', function () {
            return SiteSetting::query()->first();
        });
    }
}

if (! function_exists('formatPhone')) {
    function formatPhone(?string $value, string $type = 'phone'): ?string
    {
        return $value
            ? ContactHelper::format($value, $type)
            : null;
    }
}

if (! function_exists('contact_link')) {
    function contact_link(?string $value, string $type = 'phone'): ?string
    {
        return $value
            ? ContactHelper::link($value, $type)
            : null;
    }
}