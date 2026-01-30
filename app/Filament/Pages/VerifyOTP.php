<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form; // Önemli: Örneğindeki Schema yerine standart Form v4'te de geçerlidir
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class VerifyOTP extends Page implements HasForms
{
    use InteractsWithForms;

    // Örneğindeki gibi v4.4'e tam uyumlu tip tanımları
    protected static ?int $navigationSort = null;

    protected static ?string $title = 'Güvenlik Doğrulaması';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static bool $shouldRegisterNavigation = false;

    // view static olmamalı (üst sınıfta static değil)
    protected string $view = 'filament.pages.verify-otp';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->two_factor_code) {
            $this->redirect(filament()->getUrl());
            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                TextInput::make('code')
                    ->label('Doğrulama Kodu')
                    ->placeholder('6 haneli kodu giriniz')
                    ->required()
                    ->numeric()
                    ->length(6)
                    ->autofocus()
                    ->extraInputAttributes(['class' => 'text-center text-2xl tracking-widest']),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('verify')
                ->label('Kodu Doğrula')
                ->color('primary')
                ->submit('verify'),
        ];
    }

    public function verify(): void
    {
        $user = Auth::user();
        $state = $this->form->getState();

        if ((string) $state['code'] === (string) $user->two_factor_code) {
            $user->update([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ]);

            Notification::make()
                ->title('Giriş Başarılı')
                ->success()
                ->send();
            
            $this->redirectIntended(filament()->getUrl());
        } else {
            Notification::make()
                ->title('Hatalı Kod')
                ->body('Lütfen e-postanızı kontrol edip tekrar deneyin.')
                ->danger()
                ->send();
        }
    }
}