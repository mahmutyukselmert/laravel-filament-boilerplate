<?php 
use App\Models\StaticTranslation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

if (!function_exists('_translate')) {
    function _translate($key) {
        $locale = App::getLocale();
        
        // 1. Tüm çevirileri cache'ten getir. Cache yoksa veritabanından çek ve 24 saat sakla.
        $translations = Cache::remember('static_translations', 86400, function () {
            return StaticTranslation::all()->pluck('text', 'key')->toArray();
        });

        // 2. Eğer anahtar (key) mevcut dilde varsa döndür
        if (isset($translations[$key][$locale])) {
            return $translations[$key][$locale];
        }

        // 3. Eğer anahtar cache'te veya veritabanında hiç yoksa otomatik oluştur
        if (!isset($translations[$key])) {
            $newTranslation = StaticTranslation::firstOrCreate(
                ['key' => $key],
                ['text' => [$locale => $key]]
            );
            
            // Yeni kayıt eklendiği için cache'i temizle ki bir sonraki yenilemede gelsin
            Cache::forget('static_translations');
            
            return $key;
        }

        // 4. Anahtar var ama bu dilde karşılığı henüz yoksa anahtarı döndür
        return $key;
    }
}

if (!function_exists('__')) { 
    function __($key, $replace = [], $locale = null) {
        return _translate($key); 
    }
}