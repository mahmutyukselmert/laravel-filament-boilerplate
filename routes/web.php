<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Models\Language;
use App\Models\PageTranslation;

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

    $pages = PageTranslation::with('page')
        ->where('language_id', $language->id)
        ->whereHas('page', fn($q) => $q->where('is_active', 1))
        ->get();

    return response()->view('frontend.sitemap.pages', compact('pages','language'))
        ->header('Content-Type', 'application/xml');
});

// robots.txt
Route::get('/robots.txt', function() {
    $content = \App\Models\RobotsSetting::first()?->content ?? '';
    return response($content, 200)
        ->header('Content-Type', 'text/plain');
});

/*
|--------------------------------------------------------------------------
| Ana Sayfa
|--------------------------------------------------------------------------
*/
Route::get('/{lang?}', [PageController::class, 'home'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| Diğer Sayfalar
|--------------------------------------------------------------------------
*/
Route::get('/{lang}/{slug}', [PageController::class, 'show'])
    ->name('page.show');

