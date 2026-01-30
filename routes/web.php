<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Models\Language;
use App\Models\PageTranslation;
use App\Models\Service;
use App\Models\ServiceTranslation;

use App\Http\Controllers\ServiceController;

// Sitemap index
Route::get('/sitemap.xml', function() {
    $languages = Language::where('active', 1)->orderBy('sort_order')->get();
    return response()->view('frontend.sitemap.index', compact('languages'))
        ->header('Content-Type', 'application/xml');
});

// Dil bazlı sitemap
Route::get('/sitemap-{code}.xml', function($code) {
    $language = Language::where('code', $code)->where('active', 1)->first();
    if (!$language) abort(404);

    // Uygulama dilini sitemap diline set edelim (getDynamicUrl içindeki app()->getLocale() için)
    app()->setLocale($code);

    // 1. Sayfaları Çek
    $pages = PageTranslation::with('page')
    ->where('language_id', $language->id)
    ->whereHas('page', function($q) {
        $q->where('is_active', 1)
          ->whereNull('deleted_at'); // Silinmiş olanları kesinlikle getirme
    })
    ->get()
    ->sortBy(fn($translation) => $translation->page->sort_order);

    // 2. Hizmetleri Çek
    $services = Service::where('is_active', 1)
        ->with(['translations' => fn($q) => $q->where('language_id', $language->id)])
        ->orderBy('sort_order', 'asc') // Ana tablodaki sütun adı
        ->get();

    $homePage = $pages->where('page.template', 'home')->first();
    $others = $pages->where('page.template', '!=', 'home');

    return response()->view('frontend.sitemap.pages', [
        'language' => $language,
        'homePage' => $homePage,
        'services' => $services,
        'others' => $others
    ])->header('Content-Type', 'application/xml');
});

// robots.txt
Route::get('/robots.txt', function() {
    $content = \App\Models\RobotsSetting::first()?->content ?? '';
    return response($content, 200)
        ->header('Content-Type', 'text/plain');
});

$locales = \App\Models\Language::where('active', 1)->pluck('code')->implode('|');
$servicePrefixes = ['tr' => 'transfer', 'en' => 'services', 'de' => 'leistungen', 'ru' => 'uslugi'];

// Middleware 'setLocale' isminin bootstrap/app.php'de tanımlı olduğundan emin ol
Route::middleware(['setLocale'])->group(function () use ($locales, $servicePrefixes) {

    // 1. DİL DEĞİŞTİRME ROTASI (Buraya Ekle)
    Route::get('/lang/switch/{locale}', function ($locale) {
        $exists = \App\Models\Language::where('code', $locale)->where('active', 1)->exists();
        if ($exists) {
            session()->put('locale', $locale);
            return redirect()->back(); 
        }
        return redirect()->to('/');
    })->name('lang.switch');
    
    /*
    |--------------------------------------------------------------------------
    | HİZMETLER (Dinamik SEO Prefix Yapısı)
    |--------------------------------------------------------------------------
    */
    foreach ($servicePrefixes as $lang => $prefix) {
        $path = ($lang === 'tr') ? "/{$prefix}/{slug}" : "/{$lang}/{$prefix}/{slug}";
        
        Route::get($path, [ServiceController::class, 'show'])
            ->name("service.show.{$lang}");
    }

    // Varsayılan dil için hizmet rotası (örn: /hizmet/vip-transfer)
    Route::get("/hizmet/{slug}", [ServiceController::class, 'showNoLang'])
        ->name('service.show.no-lang');

    /*
    |--------------------------------------------------------------------------
    | Ana Sayfa
    |--------------------------------------------------------------------------
    */
    Route::get('/{lang?}', [PageController::class, 'home'])
        ->where('lang', $locales) 
        ->name('home.lang');
    Route::get('/', [PageController::class, 'home'])
        ->name('home');

    /*
    |--------------------------------------------------------------------------
    | Diğer Sayfalar
    |--------------------------------------------------------------------------
    */
    Route::get('/{lang}/{slug}', [PageController::class, 'show'])
        ->where('lang', $locales)
        ->name('page.show');

    Route::get('/{slug}', [PageController::class, 'showNoLang'])
        ->name('page.show.no-lang');

});
