<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\SiteSetting;

abstract class Controller
{
    public function settings()
    {
        return SiteSetting::first();
    }

    public function footer_menus()
    {
        return Menu::where('location', 'footer')
            ->where('active', true)
            ->with(['items.translations'])
            ->orderBy('sort_order') // menü sırasına göre
            ->get();
    }

    public function __construct()
    {
       
    }
}
