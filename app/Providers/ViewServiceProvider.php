<?php 

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Service;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // '*' karakteri tüm view dosyaları demektir. 
        // İstersen sadece ['frontend.layouts.header', 'frontend.home.index'] diyerek kısıtlayabilirsin.
        View::composer('*', function ($view) {
            $langId = session('language_id', 1);

            $globalServices = Service::where('is_active', 1)
                ->where('is_featured', 1)
                ->with(['translations' => function($query) use ($langId) {
                    $query->where('language_id', $langId);
                }])
                ->orderBy('sort_order', 'asc')
                ->get();

            // Artık tüm blade dosyalarında $globalServices değişkeni otomatik hazır!
            $view->with('services', $globalServices);
        });
    }
}