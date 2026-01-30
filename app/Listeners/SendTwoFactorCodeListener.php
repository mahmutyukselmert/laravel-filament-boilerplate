<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Login;

use App\Notifications\SendTwoFactorCode;

class SendTwoFactorCodeListener
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        // Sadece admin paneline giren kullanıcılar için kod üret
        if ($user->two_factor_code) {
            $user->generateTwoFactorCode();
            $user->notify(new SendTwoFactorCode($user));
        }
    }
}