<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceTranslation;

class ServiceController extends Controller
{
    public function show($lang = null, $slug = null)
    {
        if (is_null($slug)) {
            $slug = $lang;
            $lang = app()->getLocale();
        }

        // 1. Dile göre Language modelini bul
        $language = Language::where('code', $lang)->where('active', 1)->firstOrFail();

        // 2. Bu dile ve slug'a ait çeviriyi bul
        $translation = ServiceTranslation::where('language_id', $language->id)
            ->where('slug', $slug)
            ->with([
                'service' => function($q) {
                    $q->where('is_active', 1);
                },
                'service.gallery'
            ])
            ->firstOrFail();

        $service = $translation->service;

        return view('frontend.services.show', compact('service', 'translation', 'language'));
    }

    /**
     * Dilsiz Hizmet Gösterimi (Varsayılan Dil - Örn: /hizmet/vip-transfer)
     */
    public function showNoLang($slug)
    {
        // Varsayılan dili bul (genelde ID: 1 veya is_default: true)
        $defaultLang = Language::where('is_default', true)->first() ?? Language::where('code', 'tr')->first();
        
        if (!$defaultLang) {
            abort(404);
        }

        return $this->show($defaultLang->code, $slug);
    }
}