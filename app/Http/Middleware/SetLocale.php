<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('lang') ?? $request->segment(1);

        // Veritabanından aktif dilleri ve ID'lerini tek seferde çekelim (Performans için)
        $activeLanguages = \App\Models\Language::where('active', true)->get(['id', 'code']);
        $locales = $activeLanguages->pluck('code')->toArray();

        if ($locale && in_array($locale, $locales)) {
            app()->setLocale($locale);
            
            // Seçilen dilin ID'sini bul ve session'a at
            $currentLang = $activeLanguages->where('code', $locale)->first();
            session(['language_id' => $currentLang->id, 'locale' => $locale]);
        } else {
            // Varsayılan dil ayarları
            $defaultLocale = config('app.locale');
            app()->setLocale($defaultLocale);
            
            $defaultLang = $activeLanguages->where('code', $defaultLocale)->first();
            session(['language_id' => $defaultLang->id ?? 1, 'locale' => $defaultLocale]);
        }

        return $next($request);
    }
}
