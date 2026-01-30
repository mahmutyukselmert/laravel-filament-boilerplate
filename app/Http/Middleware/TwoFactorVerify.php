<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorVerify
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Giriş yapılmamışsa karışma
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // 2. Rota kontrolü (Burası kritik)
        // Eğer zaten doğrulama sayfasındaysak veya bir logout/auth işlemiyse engelleme
        if (
            $request->routeIs('filament.admin.pages.verify*') || 
            $request->routeIs('filament.admin.auth.*') ||
            $request->is('livewire/*')
        ) {
            return $next($request);
        }

        // 3. Kod var mı ve süresi geçerli mi?
        if ($user->two_factor_code) {
            
            if ($user->two_factor_expires_at && $user->two_factor_expires_at < now()) {
                $user->update([
                    'two_factor_code' => null, 
                    'two_factor_expires_at' => null
                ]);
                auth()->logout();
                return redirect()->route('filament.admin.auth.login')
                    ->withErrors(['login' => 'Kod süresi doldu.']);
            }

            // 4. Eğer buradaysak kullanıcı içeri sızmaya çalışıyor, geri fırlat!
            // URL'den direkt gidelim, rota ismi hatasını ekarte edelim
            return redirect('/admin/verify-o-t-p'); 
        }

        return $next($request);
    }
}