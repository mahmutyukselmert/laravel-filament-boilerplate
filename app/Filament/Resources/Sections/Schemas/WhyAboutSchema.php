<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\ViewField;
use Filament\Actions\Action;

class WhyAboutSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName)
                ->label('Ekstra Özellikler')
                ->schema([
                    TextInput::make('icon')
                        ->label('İkon')
                        ->placeholder('İkon seçin veya yazın...')
                        ->id('icon-input')
                        // Sağ tarafa buton ekliyoruz
                        ->suffixAction(
                            Action::make('open_icon_picker')
                                ->icon('heroicon-m-magnifying-glass') // Büyüteç ikonu
                                ->label('Göz At')
                                ->color('info')
                                ->modalHeading('İkon Kütüphanesi')
                                ->modalSubmitAction(false) // Alt butonları gizle
                                ->modalContent(fn ($component) => view('filament.forms.icon-picker-modal', [
                'statePath' => $component->getStatePath(), // Tam yolu gönderiyoruz
            ]))
                        ),

                    TextInput::make('title')->label('Kısa Başlık'),
                    Textarea::make('text')->label('Açıklama')->rows(2),
                ])->itemLabel(fn ($state) => $state['title'] ?? 'Yeni Özellik')
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->cloneable(),
        ];
    }
}
