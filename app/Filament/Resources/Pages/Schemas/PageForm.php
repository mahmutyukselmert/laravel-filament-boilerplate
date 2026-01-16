<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Models\Language;
use App\Models\Section as GlobalSectionModel;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeLanguages = Language::where('active', true)
            ->orderBy('sort_order')
            ->get();

        return $schema->components([
            Grid::make(12)
                ->schema([
                    Section::make('İçerik')
                        ->schema([
                            // DİL BAZLI ALANLAR (Tamamen ID Bazlı)
                            Tabs::make('Languages')
                                ->tabs(
                                    $activeLanguages->map(function ($lang) {
                                        return Tabs\Tab::make($lang->name)
                                            // State path'i zorunlu olarak ID yapıyoruz ki translations tablosuyla ID üzerinden eşleşsin
                                            ->schema([
                                                TextInput::make("translations.{$lang->id}.title")
                                                    ->label('Sayfa Başlığı')
                                                    ->required($lang->is_default)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn($state, $set) => $set("translations.{$lang->id}.slug", Str::slug($state))),
                                                TextInput::make("translations.{$lang->id}.slug")
                                                    ->label('URL Uzantısı (Slug)')
                                                    ->required($lang->is_default),
                                                TextInput::make("translations.{$lang->id}.subtitle")
                                                    ->label('Alt Başlık'),
                                                Textarea::make("translations.{$lang->id}.short_description")
                                                    ->label('Kısa Açıklama'),
                                                RichEditor::make("translations.{$lang->id}.content")
                                                    ->label('Ana İçerik')
                                                    ->extraAttributes([
                                                        'style' => 'min-height: 350px;',
                                                    ]),
                                                Builder::make("translations.{$lang->id}.sections")
                                                    ->label('Esnek Sayfa Bölümleri')
                                                    ->blocks([
                                                        static::getHeroBlock(),
                                                        static::getStatsBlock(),
                                                        static::getGlobalSectionBlock(),
                                                    ])
                                                    ->collapsible()
                                                    ->cloneable(),
                                                // SEO (ID bazlı korundu)
                                                Section::make('SEO Ayarları')
                                                    ->schema([
                                                        TextInput::make("translations.{$lang->id}.meta_title")
                                                            ->label('Meta Başlık'),
                                                        Textarea::make("translations.{$lang->id}.meta_description")
                                                            ->label('Meta Açıklama'),
                                                        TextInput::make("translations.{$lang->id}.meta_keywords")
                                                            ->label('Anahtar Kelimeler'),
                                                    ])
                                                    ->collapsed(false),
                                            ]);
                                    })->toArray()
                                )
                                ->columnSpanFull(),
                        ])
                        ->columnSpan(9),
                    // GENEL AYARLAR
                    Section::make('Sayfa Ayarları')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Öne Çıkan Görseli')
                                ->image()
                                ->disk('public')
                                ->directory('pages')
                                ->visibility('public')
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
                                    $name = Str::random(10).'_'.time().'.webp';
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
                        ->columnSpan(3),
                ])
                ->columnSpanFull(),
        ]);
    }

    protected static function getHeroBlock(): Builder\Block
    {
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
                        TextInput::make('count')->label('Sayı'),
                        TextInput::make('label')->label('Etiket'),
                    ])
                    ->columns(2)
            ]);
    }

    protected static function getGlobalSectionBlock(): Builder\Block
    {
        return Builder\Block::make('global_section_ref')
            ->label('Hazır Bölüm (Global)')
            ->icon('heroicon-o-arrow-path')
            ->schema([
                Select::make('section_id')
                    ->label('Bölüm Seçin')
                    ->options(GlobalSectionModel::pluck('admin_title', 'id'))
                    ->required(),
            ]);
    }
}
