<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class OurPartnersSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName) // translations.{id}.content
                ->label('Çözüm Ortaklarımız')
                ->schema([
                    TextInput::make('title')
                        ->label('Marka Adı')
                        ->required(),
                    TextInput::make('url')
                        ->label('Marka URL')
                        ->default('#')
                        ->required(),
                    FileUpload::make('image')
                        ->label('Marka Logo')
                        ->image()
                        ->disk('public')
                        ->directory('our_partners')
                        ->required(),
                ])
                ->collapsible()
                            ->collapsed()
                            ->cloneable()
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->reorderableWithDragAndDrop(true)
                            ->addActionLabel('Yeni Çözüm Ortağı Ekle')
        ];
    }
}