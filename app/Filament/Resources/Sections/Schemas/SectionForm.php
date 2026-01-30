<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Group;

use Filament\Forms\Components\FileUpload;

use Illuminate\Support\Str;

use Filament\Schemas\Schema; // Filament v4+ Schema yapısı

use Filament\Actions\Action;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // BÖLÜM 1: GENEL TANIMLAR VE GÖRSELLER (Tüm diller için ortak)
            Section::make('Bölüm Genel Ayarları')
                ->description('Bölümün tipini, anahtarını ve görsellerini buradan yönetin.')
                ->schema([
                    Grid::make()->schema([
                        TextInput::make('admin_title')
                            ->label('Admin Başlığı')
                            ->placeholder('Örn: Ana Sayfa Hakkımızda')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set("key", Str::slug($state)))
                            ->required(),
                            
                        TextInput::make('key')
                            ->label('Benzersiz Anahtar')
                            ->placeholder('Örn: home_about')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Select::make('type')
                            ->label('Bölüm Tipi')
                            ->options([
                                'default' => 'Genel İçerik)',
                                'about' => 'Tekrarlayan Görsel/Metin İçeriği (Hakkımızda Benzeri)',
                                'stats' => 'İstatistikler (Rakamlar)',
                                'faq' => 'Sıkça Sorulan Sorular',
                                'why_about' => 'Neden Biz (İkonlu Listeler)',
                                'our_partners' => 'Çözüm Ortaklarımız',
                            ])
                            ->required()
                            ->live(), // Tip değiştiğinde formu anlık günceller
                    ])->columnSpanFull(),

                    FileUpload::make('images')
                        ->label('Bölüm Görselleri')
                        ->multiple() // Çoklu görsel yükleme (Senin 3'lü görsel yapın için)
                        ->image()
                        ->reorderable()
                        ->directory('images')
                        ->columnSpanFull()
                        ->helperText('Bölüm için görselleri buradan yönetin. Görselleri sürükle bırak ile sıralamasını değiştirebilirsiniz.'),
                ]),
            
            // DİL SEKMELERİ
            Tabs::make('İçerik Dilleri')
            ->tabs(function () {
                return \App\Models\Language::where('active', true)->get()->map(function ($lang) {
                    return Tabs\Tab::make($lang->name)
                        ->schema([
                            // 1. SABİT ALANLAR (Her bölümde olur)
                            TextInput::make("translations.{$lang->id}.title")
                                ->label('Bölüm Başlığı'),
                            TextInput::make("translations.{$lang->id}.subtitle")
                                ->label('Bölüm Alt Başlığı'),
                            RichEditor::make("translations.{$lang->id}.description")
                                ->label('Bölüm Açıklaması')
                                ->extraInputAttributes(['style' => 'min-height: 300px;'])
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'bulletList',
                                    'orderedList',
                                    'table',
                                    'attachFiles',
                                    'link',
                                    'undo',
                                    'redo',
                                ]),
                                // 3. Butonlar (JSON olarak kaydedilir)
                                Repeater::make("translations.{$lang->id}.buttons")
                                    ->label('İşlem Butonları')
                                    ->schema([
                                        TextInput::make('text')->label('Buton Metni'),
                                        TextInput::make('url')->label('URL / Link'),
                                        Select::make('style')
                                            ->options([
                                                'primary' => 'Ana Renk',
                                                'outline' => 'Çerçeveli',
                                            ])->label('Stil'),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->itemLabel(fn ($state) => $state['text'] ?? 'Yeni Buton'),

                            // 2. Dinamik Alanlar (Tipe Göre Değişen İçerik)
                                // Burası 'content' JSON kolonuna yazar.
                                Section::make('Bölüm Tipine Göre Bölüm İçeriği')
                                    ->compact()
                                    ->hidden(fn($get) => $get('type') === 'default')
                                    ->schema(function ($get) use ($lang) {
                                        $type = $get('type');
                                        $basePath = "translations.{$lang->id}.content";
                                        
                                        return match ($type) {
                                            'about' => AboutSchema::schema($basePath),
                                            'stats' => StatsSchema::schema($basePath),
                                            'faq' => FaqSchema::schema($basePath),
                                            'our_partners' => OurPartnersSchema::schema($basePath),
                                            'why_about' => WhyAboutSchema::schema($basePath),
                                            default => [],
                                        };
                                    }),

                            Repeater::make("translations.{$lang->id}.extra_fields")
                                ->label('Extra Alanlar')
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
                                                                'statePath' => $component->getStatePath(),
                                                            ]))
                                                    ),
                                    TextInput::make('title')->label('Başlık')->placeholder('Örn: 7/24 Kesintisiz Hizmet')->required(),
                                    TextInput::make('description')->label('Açıklama')->placeholder('Örn: 7/24 Kesintisiz Hizmet')->required(),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni Extra Alan')
                                ->collapsible()
                                ->addActionLabel('Yeni Extra Alan Ekle')
                                ->columns(1),

                        ]);
                })->toArray();
            })->columnSpanFull()
            
        ]);
    }
}