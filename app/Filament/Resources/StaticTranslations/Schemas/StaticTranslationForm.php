<?php

namespace App\Filament\Resources\StaticTranslations\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StaticTranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Üst Kısım: Anahtar Bilgisi (Değiştirilemez)
                Section::make('Sistem Bilgisi')
                    ->description('Bu anahtar kod içerisinde kullanılır, lütfen değiştirmeyin.')
                    ->schema([
                        TextInput::make('key')
                            ->label('Sistem Anahtarı (Key)')
                            ->disabled(fn ($record) => $record !== null) // Koddan otomatik geldiği için manuel müdahaleyi kapattık
                            ->dehydrated() // Form gönderilirken bu değerin de gitmesini sağlar
                            ->required()
                            ->columnSpanFull(),

                        // 2. Alt Kısım: Dil Karşılıkları
                        Section::make('Çeviriler')
                            ->description('Her dil için metin karşılığını aşağıya yazınız.')
                            ->schema([
                                Grid::make(2)->schema(function () {
                                    // Aktif dilleri çekiyoruz
                                    $languages = \App\Models\Language::where('active', true)->get();
                                    $inputs = [];

                                    foreach ($languages as $lang) {
                                        $inputs[] = TextInput::make("text.{$lang->code}")
                                            ->label($lang->name . " ({$lang->code})")
                                            ->placeholder($lang->name . " çevirisini girin...")
                                            ->required();
                                    }
                                    
                                    return $inputs;
                                }),
                            ])->columnSpanFull(),
                    ]),
            ]);
    }
}
