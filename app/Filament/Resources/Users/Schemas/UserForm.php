<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required(),
                TextInput::make('email')
                    ->label('E-Posta')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')->label('E-Posta Doğrulama Tarihi'),

                TextInput::make('password')
                    ->label('Şifre')
                    ->password(false) // Yazılanı gizler
                    ->revealable(false) // Göz ikonuna basınca şifreyi gösterir
                    ->required()
                    ->suffixAction(
                        Action::make('generatePassword')
                            ->icon(Heroicon::OutlinedKey)
                            ->color('warning')
                            ->tooltip('Rastgele Şifre Üret')
                            ->action(function ($set) {
                                // 12 karakterli güvenli şifre üret
                                $randomPassword = Str::password(12);
                                
                                // Formdaki password alanını doldur
                                $set('password', $randomPassword);
                            }),
                    ),

                Select::make('role')
                ->label('Yetki Rolü')
                ->options([
                    'super_admin' => 'Süper Admin (Tam Yetki)',
                    'admin' => 'Admin (Oluşturabilir/Düzenleyebilir)',
                    'editor' => 'Editör (Sadece Düzenleyebilir)',
                ])
                ->required(),

                Toggle::make('is_active')
                ->label('Hesap Durumu')
                ->onColor('success')
                ->offColor('danger')
                ->helperText('Pasif kullanıcılar panele giriş yapamaz.')
                ->default(true),
            ]);
    }
}
