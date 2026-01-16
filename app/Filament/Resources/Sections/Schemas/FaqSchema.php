<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class FaqSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName) // translations.{id}.content
                ->label('Sorular ve Cevaplar')
                ->schema([
                    TextInput::make('question')
                        ->label('Soru')
                        ->required(),
                    Textarea::make('answer')
                        ->label('Cevap')
                        ->required()
                        ->rows(3),
                ])
                ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Yeni Soru')
                ->collapsible()
                ->addActionLabel('Yeni Soru Ekle')
                ->columns(1),
        ];
    }
}