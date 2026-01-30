<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTwoFactorCode extends Notification
{
    use Queueable;

    public function __construct(public $user)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // HATA BURADAYDI: $code yerine $this->user->two_factor_code kullanmalısın
        return (new MailMessage)
            ->subject('Giriş Doğrulama Kodu')
            ->line('Hesabınıza erişmek için kullanmanız gereken doğrulama kodunuz:')
            ->line($this->user->two_factor_code) 
            ->line('Bu kod 10 dakika süreyle geçerlidir.')
            ->line('Eğer bu girişi siz yapmadıysanız lütfen şifrenizi değiştirin.');
    }
}