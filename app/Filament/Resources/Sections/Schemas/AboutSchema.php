<?php 

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Forms\Components\FileUpload;

class AboutSchema
{
    public static function schema(string $baseName): array
    {
        return [
            Repeater::make($baseName)
                ->label('Ekstra Özellikler')
                ->schema([
                    TextInput::make('title')->label('Başlık'),
                    TextInput::make('subtitle')->label('Alt Başlık'),
                    Textarea::make('text')->label('Açıklama'),
                    Repeater::make("images")
                                    ->label('Görseller')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Görsel')
                                            ->image()
                                            ->directory('sections')
                                            ->reorderable(),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->itemLabel(fn ($state) => $state['image'] ?? 'Yeni Görsel'),
                ])->itemLabel(fn ($state) => $state['title'] ?? 'Yeni Özellik')
        ];
    }
}