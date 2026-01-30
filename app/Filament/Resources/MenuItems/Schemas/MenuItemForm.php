<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class MenuItemForm
{
    public static function components(): array
    {
        return [
            Section::make('Menü Öğesi Bilgileri')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('parent_id')
                            ->label('Üst Öğe')
                            ->placeholder('Ana öğe')
                            ->options(function () {
                                return MenuItem::query()
                                    ->where('menu_id', request('menu'))
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->nullable(),

                        TextInput::make('url')
                            ->label('Manuel URL')
                            ->placeholder('https://...')
                            ->visible(fn ($get) => $get('linkable_type') === 'custom')
                            ->required(fn ($get) => $get('linkable_type') === 'custom'),
                        
                        // BAĞLANTI TÜRÜ BURADA - Grid içinde 2. sütun
                        Select::make('linkable_type')
                            ->label('Bağlantı Türü')
                            ->options([
                                Page::class => 'Sayfa',
                                \App\Models\Service::class => 'Hizmet',
                                'custom' => 'Özel URL',
                            ])
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(fn ($set) => $set('linkable_id', null)),

                        // BAĞLANTI SEÇİMİ BURADA
                        Select::make('linkable_id')
                            ->label('Bağlantı Seçin')
                            ->searchable()
                            ->required()
                            ->visible(fn ($get) => in_array($get($get('linkable_type')), [Page::class, \App\Models\Service::class]))
                            ->getSearchResultsUsing(function (string $search, $get) {
                                $type = $get('linkable_type');
                                if ($type === Page::class) {
                                    return Page::where('title', 'like', "%{$search}%")->limit(20)->pluck('title', 'id');
                                }
                                if ($type === \App\Models\Service::class) {
                                    return \App\Models\Service::whereHas('translations', fn($q) => $q->where('title', 'like', "%{$search}%"))
                                        ->get()->mapWithKeys(fn($i) => [$i->id => $i->active_translation?->title])->toArray();
                                }
                                return [];
                            })
                            ->getOptionLabelUsing(fn ($value, $get) => match($get('linkable_type')) {
                                Page::class => Page::find($value)?->title,
                                \App\Models\Service::class => \App\Models\Service::find($value)?->active_translation?->title,
                                default => null,
                            }),
                    ]),

                    Grid::make(2)->schema([
                        Select::make('target')
                            ->label('Hedef')
                            ->options(['_self' => 'Aynı Sayfa', '_blank' => 'Yeni Sekme'])
                            ->default('_self'),

                        Toggle::make('active')
                            ->label('Aktif')
                            ->default(true),
                    ]),

                    // DİL TABLARI (Menü başlığı her dilde farklı olabilir)
                    Tabs::make('Translations')
                        ->tabs([
                            Tabs\Tab::make('Türkçe')
                                ->schema([
                                    TextInput::make('title') // Veritabanında title sütunu MenuItem'da ise
                                        ->label('Etiket')
                                        ->required(),
                                ]),
                            Tabs\Tab::make('English')
                                ->schema([
                                    TextInput::make('title_en') // Eğer ayrı sütun kullanıyorsan
                                        ->label('Label (EN)'),
                                ]),
                        ]),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::components());
    }
}