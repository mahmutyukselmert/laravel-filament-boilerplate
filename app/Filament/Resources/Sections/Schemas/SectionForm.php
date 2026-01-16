<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Schemas\Schema; // Filament v4+ Schema yapısı



class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // 1. ADIM: GENEL AYARLAR (Dilden bağımsız)
            FormSection::make('Bölüm Tanımları')
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
                ])->columns(3),

            // 2. ADIM: SONSUZ DİL SEKMELERİ
            Tabs::make('İçerik Dilleri')
                ->tabs(function () {
                    // Veri tabanındaki tüm aktif dilleri çekip her biri için bir sekme oluşturuyoruz
                    return \App\Models\Language::where('active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->map(function ($lang) {
                            return Tabs\Tab::make($lang->name)
                                ->icon($lang->code === 'tr' ? 'heroicon-m-flag' : 'heroicon-m-language') // Örnek ikon
                                ->schema([
                                    // İlişkisel kaydetme için dile ait ID'yi gizli tutuyoruz
                                    // Not: Bu yapı 'Section' modelindeki 'translations' ilişkisiyle eşleşir
                                    TextInput::make("translations.{$lang->id}.title")
                                        ->label('Bölüm Başlığı (Sitede Görünür)')
                                        ->placeholder('Hizmetlerimiz...'),

                                    // SENİN ESKİ MANTIĞIN: Tip seçimine göre değişen şemalar
                                    Group::make()
                                        ->schema(fn ($get) => match ($get('type')) {
                                            'stats' => StatsSchema::schema("translations.{$lang->id}.content"),
                                            'partners' => PartnersSchema::schema("translations.{$lang->id}.content"),
                                            'hero' => HeroSchema::schema("translations.{$lang->id}.content"),
                                            'faq' => [
                                                // Örnek FAQ şeması
                                                \Filament\Forms\Components\Repeater::make("translations.{$lang->id}.content")
                                                    ->schema([
                                                        TextInput::make('question')->label('Soru'),
                                                        \Filament\Forms\Components\Textarea::make('answer')->label('Cevap'),
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