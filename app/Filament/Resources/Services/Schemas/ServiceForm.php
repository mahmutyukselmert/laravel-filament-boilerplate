<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Language;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;

use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $activeLanguages = Language::where('active', true)
            ->orderBy('sort_order')
            ->get();

        return $schema->components([
            Grid::make(12)
                ->schema([
                    Section::make('Hizmet İçeriği')
                        ->schema([
                            Tabs::make('Languages')
                                ->tabs(
                                    $activeLanguages->map(function ($lang) {
                                        return Tabs\Tab::make($lang->name)
                                            ->schema([
                                                TextInput::make("translations.{$lang->id}.title")
                                                    ->label('Hizmet Adı')
                                                    ->required($lang->is_default)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) use ($lang) {
                                                        // slug
                                                        $set("translations.{$lang->id}.slug", Str::slug($state));

                                                        // caption boşsa otomatik title'dan doldur
                                                        if (blank($get("translations.{$lang->id}.caption"))) {
                                                            $set("translations.{$lang->id}.caption", $state ?: config('app.name'));
                                                        }
                                                    }),

                                                TextInput::make("translations.{$lang->id}.slug")
                                                    ->label('URL Uzantısı (Slug)')
                                                    ->required($lang->is_default),

                                                TextInput::make("translations.{$lang->id}.subtitle")
                                                    ->label('Kısa Spot Metin'),

                                                Textarea::make("translations.{$lang->id}.short_description")
                                                    ->label('Liste Özet Açıklaması'),

                                                RichEditor::make("translations.{$lang->id}.content")
                                                    ->label('Detaylı İçerik')
                                                    ->extraAttributes(['style' => 'min-height: 300px;']),
                                            ]);
                                    })->toArray()
                                )
                                ->columnSpanFull(),

                            Section::make('Hizmet Galerisi')
                            ->schema([
                                Repeater::make('gallery')
                                    ->label('Galeri')
                                    ->relationship('gallery')
                                    ->schema([
                                        FileUpload::make('file_path')
                                            ->label('Görsel')
                                            ->image()
                                            ->directory('galleries/services')
                                            ->required()
                                            ->columnSpanFull(),

                                        // RESİM ALT BİLGİSİ - ÇOK DİLLİ
                                        Grid::make(count($activeLanguages)) // Dilleri yan yana veya alt alta dizer
                                            ->schema(
                                                $activeLanguages->map(function ($lang) {
                                                    return TextInput::make("title.{$lang->id}") // title[1], title[2] gibi saklar
                                                        ->label("Resim Alt Yazısı ({$lang->name})")
                                                        ->reactive()
                                                        ->default(function (callable $get) use ($lang) {
                                                            return $get("translations.{$lang->id}.title")
                                                                ?: config('app.name');
                                                        })
                                                        ->placeholder('SEO için önemli...');
                                                })->toArray()
                                            ),

                                        Hidden::make('model_type')->default('service'),
                                    ])
                                    ->grid(2) // Görselleri 2'şerli gruplar (altındaki dillerle çok yer kaplamasın diye)
                                    ->reorderable('sort_order')
                                    ->itemLabel(fn (array $state): ?string => $state['file_path'] ?? 'Görsel'),
                            ])
                        ])
                        ->columnSpan(9),

                    Section::make('Hizmet Ayarları')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Kapak Görseli')
                                ->image()
                                ->directory('services')
                                ->saveUploadedFileUsing(function ($file) {
                                    $manager = new ImageManager(new Driver);
                                    $name = Str::random(10).'_'.time().'.webp';
                                    $path = storage_path('app/public/services/' . $name);
                                    $img = $manager->read($file);
                                    $img->toWebp(80)->save($path);

                                    return 'services/'.$name;
                                }),

                            TextInput::make('icon')
                                ->label('İkon Kodu (Lucide/Heroicon)')
                                ->placeholder('heroicon-o-wrench'),

                            Toggle::make('is_active')
                                ->label('Aktif mi?')
                                ->default(true),

                            Toggle::make('is_featured')
                                ->label('Ana Sayfada Göster')
                                ->default(false),

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
}
