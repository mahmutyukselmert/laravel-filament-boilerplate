<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class StatsSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName) // translations.{id}.content
                ->label('İstatistik Kartları')
                ->schema([
                    TextInput::make('number')
                        ->label('Rakam/Değer')
                        ->placeholder('Örn: 1500+')
                        ->required(),
                    TextInput::make('label')
                        ->label('Açıklama/Etiket')
                        ->placeholder('Örn: Mutlu Müşteri')
                        ->required(),
                    TextInput::make('icon')
                        ->label('İkon (Heroicon veya SVG)')
                        ->placeholder('heroicon-o-users'),
                ])
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Yeni İstatistik')
                ->grid(3) // Yanyana 3'lü dizilim
                ->addActionLabel('Yeni İstatistik Ekle'),
        ];
    }
}