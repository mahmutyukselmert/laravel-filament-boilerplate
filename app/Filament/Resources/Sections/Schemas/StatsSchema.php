<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

use Filament\Actions\Action;

class StatsSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName) // translations.{id}.content
                ->label('İstatistik Kartları')
                ->schema([
                    Grid::make()
                        ->columns([
                            'default' => 1,
                            'md' => 2,
                        ])
                        ->schema([
                            TextInput::make('number')->label('Rakam / Değer')->placeholder('Örn: 1500')->required(),
                            TextInput::make('symbol')->label('Sembol (Örn: +, %)')->placeholder('Örn: +')->maxLength(2),
                        ]),
                    TextInput::make('label')->label('Açıklama/Etiket')->placeholder('Örn: Mutlu Müşteri')->required(),
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
                                                'statePath' => $component->getStatePath(),
                                            ]))
                                    ),
                ])
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Yeni İstatistik')
                ->grid(3)
                ->collapsible()
                ->addActionLabel('Yeni İstatistik Ekle'),
        ];
    }
}
