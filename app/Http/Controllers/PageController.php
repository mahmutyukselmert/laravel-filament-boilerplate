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
        return $this->renderPageBySlug('home');
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
        $languageId = current_language_id();

        $translation = PageTranslation::query()
            ->where('slug', $slug)
            ->where('language_id', $languageId)
            ->whereHas('page', fn ($q) => $q->where('is_active', 1))
            ->with('page')
            ->firstOrFail();

        $page = $translation->page;

        // Template -> View eşleşmesi
        $view = match ($page->template) {
            'home'    => 'frontend.home.index',
            'about'   => 'frontend.about.index',
            'contact' => 'frontend.contact.index',
            'services'=> 'frontend.services.index',
            'projects'=> 'frontend.projects.index',
            'products'=> 'frontend.products.index',
            default   => 'frontend.pages.default',
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
