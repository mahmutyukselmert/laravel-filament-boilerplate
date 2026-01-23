<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Group;

use Filament\Schemas\Schema; // Filament v4+ Schema yapısı

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // 1. ADIM: GENEL AYARLAR (Dilden bağımsız)
            Section::make('Bölüm Tanımları')
                ->schema([
                    TextInput::make('admin_title')
                        ->label('Admin Başlığı (Panelde Görünür)')
                        ->placeholder('Örn: Ana Sayfa FAQ')
                        ->required(),

                    TextInput::make('key')
                        ->label('Benzersiz Anahtar')
                        ->placeholder('home_faq')
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Select::make('type')
                        ->label('Bölüm Tipi')
                        ->options([
                            'hero' => 'Hero Alanı',
                            'stats' => 'İstatistikler',
                            'partners' => 'Çözüm Ortakları',
                            'faq' => 'Sıkça Sorulan Sorular',
                        ])
                        ->required()
                        ->reactive(),
                ])->columnSpanFull(),

            // 2. ADIM: SONSUZ DİL SEKMELERİ
            Tabs::make('İçerik Dilleri')
                ->tabs(function () {
                    // Veri tabanındaki tüm aktif dilleri çekip her biri için bir sekme oluşturuyoruz
                    return \App\Models\Language::where('active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->map(function ($lang) {
                            return Tabs\Tab::make($lang->name)
                                ->icon('heroicon-m-language') // Örnek ikon
                                ->schema([
                                    // İlişkisel kaydetme için dile ait ID'yi gizli tutuyoruz
                                    // Not: Bu yapı 'Section' modelindeki 'translations' ilişkisiyle eşleşir
                                    TextInput::make("translations.{$lang->id}.title")
                                        ->label('Bölüm Başlığı (Sitede Görünür)')
                                        ->placeholder('Hizmetlerimiz...'),

                                    Section::make("translations.{$lang->id}.content")
                                        ->schema(fn ($get) => match ($get('type')) {
                                            'stats' => StatsSchema::schema("translations.{$lang->id}.content"),
                                            'partners' => PartnersSchema::schema("translations.{$lang->id}.content"),
                                            'hero' => HeroSchema::schema("translations.{$lang->id}.content"),
                                            'faq' => [
                                                Repeater::make("translations.{$lang->id}.content")
                                                    ->schema([
                                                        TextInput::make('question')->label('Soru'),
                                                        Textarea::make('answer')->label('Cevap'),
                                                    ])->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                                            ],
                                            default => [],
                                        }),
                                ]);
                        })->toArray();
                })->columnSpanFull()
        ]);
    }
}