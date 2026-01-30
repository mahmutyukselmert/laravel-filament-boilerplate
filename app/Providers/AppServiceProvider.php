<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Translation\TranslatorInterface;
use App\Services\Translation\GeminiTranslator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Services\FooterService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslatorInterface::class, GeminiTranslator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('t', function ($expression) {
            return "<?php echo _translate($expression); ?>";
        });

        View::composer('frontend.includes.footer', function ($view) {
            $footerService = app(FooterService::class);
            $view->with('footer_menus', $footerService->menus());
        });

        Event::listen(
            Login::class,
            [\App\Listeners\SendTwoFactorCodeListener::class, 'handle']
        );
    }
}
