<?php
namespace App\Services;

use App\Models\Menu;
use App\Models\Language;

class FooterService
{
    public function menus()
    {
        // Mevcut dilin ID'sini alıyoruz (Örn: 'tr' -> 1)
        $locale = app()->getLocale();
        $langId = Language::where('code', $locale)->first()?->id ?? 1;

        return Menu::where('location', 'footer')
            ->where('active', true)
            ->with([
                // 1. Menü başlığı çevirileri (menu_translations)
                'translations' => function($query) use ($langId) {
                    $query->where('language_id', $langId);
                },
                // 2. Menü öğeleri ve onların etiket çevirileri (menu_items + menu_item_translations)
                'items' => function($query) {
                    $query->where('active', true)->orderBy('sort_order', 'asc');
                },
                'items.translations' => function($query) use ($langId) {
                    $query->where('language_id', $langId);
                }
            ])
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
