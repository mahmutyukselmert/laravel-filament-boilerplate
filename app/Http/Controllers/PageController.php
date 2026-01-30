<?php

namespace App\Http\Controllers;

use App\Models\PageTranslation;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Ana sayfa
    public function home(Request $request, string $lang = null)
    {
        $this->setLanguage($lang);
        $langId = session('language_id');

        $translation = \App\Models\PageTranslation::where('slug', 'home')
            ->where('language_id', $langId)
            ->first();

        $sliders = \App\Models\Slider::where('active', true)
        ->where('category', 'home-slider')
        ->with(['translations' => function($query) use ($langId) {
            $query->where('language_id', $langId);
        }])
        ->orderBy('sort_order')->get();

        return view('frontend.home.index', compact('translation', 'sliders'));
    }

    public function showNoLang(Request $request, string $slug)
    {
        // Varsayılan dil 'tr' olsun
        return $this->show($request, 'tr', $slug);
    }

    // Diğer sayfalar
    public function show(Request $request, string $lang, string $slug)
    {
        $this->setLanguage($lang);
        return $this->renderPageBySlug($slug);
    }

    // Ortak render
    protected function renderPageBySlug(string $slug)
    {
        // current_language_id() helper'ın bazen session gecikmesi yaşayabilir, 
        // garantiye almak için doğrudan session'a bakalım:
        $languageId = session('language_id', 1);

        $translation = PageTranslation::query()
            ->where('slug', $slug)
            ->where('language_id', $languageId)
            ->whereHas('page', fn ($q) => $q->where('is_active', 1))
            ->with('page')
            ->first();

        if (!$translation) {
            abort(404, 'Sayfa bulunamadı.');
        }

        $page = $translation->page;

        // View klasör yolun 'frontend.pages.about' mu yoksa 'frontend.about.index' mi?
        // Buna göre burayı güncelle:
        $view = match ($page->template) {
            'home'     => 'frontend.home.index',
            'about'    => 'frontend.about.index',
            'contact'  => 'frontend.contact.index',
            'services' => 'frontend.services.index',
            default    => 'frontend.pages.default',
        };

        return view($view, [
            'page'        => $page,
            'translation' => $translation,
        ]);
    }

    // URL’den dil ayarlama ve session/set current_language_id
    protected function setLanguage(?string $lang)
    {
        $lang = $lang ?: 'tr'; // default TR

        $languageId = match($lang) {
            'tr' => 1,
            'en' => 2,
            // başka diller eklenecekse buraya
            default => 1,
        };

        // global helper veya session set
        session(['language_id' => $languageId]);
    }
}
