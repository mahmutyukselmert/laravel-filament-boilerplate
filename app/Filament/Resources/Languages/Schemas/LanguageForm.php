<?php

namespace App\Filament\Resources\Languages\Schemas;

use App\Models\Language;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dil Bilgileri')
                ->schema([
                    TextInput::make('name')
                        ->label('Dil Adı')
                        ->placeholder('Örn: Türkçe')
                        ->required(),

                    TextInput::make('code')
                        ->label('Dil Kodu')
                        ->helperText('ISO kodlarını kullanın: tr, en, de, ru, fr')
                        ->required()
                        ->maxLength(5)
                        ->unique(ignoreRecord: true),

                    Toggle::make('active')
                        ->label('Aktif')
                        ->default(true),

                    Toggle::make('is_default')
                        ->label('Varsayılan Dil')
                        ->helperText('Sistemin ana dili olarak kabul edilir.'),

                    TextInput::make('sort_order')
                        ->label('Sıralama')
                        ->numeric()
                        ->required()
                        ->default(fn () => Language::count() + 1),
                ])->columns(2)->columnSpanFull(),
        ]);
    }
}