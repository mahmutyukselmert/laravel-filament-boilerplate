<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Models\Language;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;


use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {

        $activeLanguages = Language::where('active', true)
            ->orderBy('sort_order')
            ->get();

        return $schema
            ->components([
                Section::make('Sayfa Ayarları')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->directory('pages')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->saveUploadedFileUsing(function ($file) {
                                $manager = new ImageManager(new Driver());
                                $name = Str::random(40) . '.webp';
                                $path = storage_path('app/public/pages/' . $name);
                                $img = $manager->read($file);
                                $img->toWebp(80)->save($path);
                                return 'pages/' . $name;
                            }),
                        Toggle::make('is_active')
                            ->label('Aktif mi?')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(1),
                Tabs::make('Languages')
                    ->tabs(
                        $activeLanguages->map(function ($lang) {
                            return static::generateLanguageTab($lang->code, $lang->name);
                        })->toArray()
                    )
                    ->columnSpanFull(),
            ]);
    }

    protected static function generateLanguageTab(string $locale, string $label): Tabs\Tab
    {
        return Tabs\Tab::make($label)
            ->statePath($locale)
            ->schema([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->label('URL (Slug)')
                    ->required(),
                TextInput::make('subtitle')
                    ->label('Alt Başlık'),
                Textarea::make('short_description')
                    ->label('Kısa Açıklama'),
                RichEditor::make('content')
                    ->label('İçerik'),
                Builder::make('sections')
                    ->label('Esnek Sayfa Bölümleri')
                    ->blocks([
                        static::getHeroBlock(),
                        static::getStatsBlock(),
                    ])
                    ->collapsible()
                    ->cloneable(),
                Section::make('SEO Ayarları')
                    ->schema([
                        TextInput::make('meta_title')->label('Meta Başlık'),
                        Textarea::make('meta_description')->label('Meta Açıklama'),
                        TextInput::make('meta_keywords')->label('Anahtar Kelimeler'),
                    ])
                    ->collapsed(),
            ]);
    }

    protected static function getHeroBlock(): Builder\Block
    {
        // v4 Builder yapısı
        return Builder\Block::make('hero_section')
            ->label('Hero Bölümü')
            ->icon('heroicon-o-sparkles')
            ->schema([
                TextInput::make('title')->label('Hero Başlığı'),
                TextInput::make('sub_title')->label('Hero Alt Başlığı'),
            ]);
    }

    protected static function getStatsBlock(): Builder\Block
    {
        return Builder\Block::make('stats_section')
            ->label('İstatistikler (Sayaç)')
            ->icon('heroicon-o-chart-bar')
            ->schema([
                Repeater::make('items')
                    ->label('Öğeler')
                    ->schema([
                        TextInput::make('count')->label('Sayı (Örn: 100+)'),
                        TextInput::make('label')->label('Etiket (Örn: Çalışan)'),
                    ])
                    ->columns(2)
            ]);
    }
}
