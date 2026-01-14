<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Models\MenuItem;
use App\Models\Page;
use Filament\Schemas\Components\Section;


class MenuItemForm
{
    public static function components(): array
    {
        return [
            Section::make('Temel Bilgiler')
                ->schema([
                    Select::make('parent_id')
                        ->label('Üst Menü Öğesi')
                        ->options(function () {
                            return MenuItem::query()
                                ->where('menu_id', request('menu'))
                                ->pluck('title', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TextInput::make('title')
                        ->label('Menü Başlığı')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('sort_order')
                        ->label('Sıralama')
                        ->numeric()
                        ->default(0),
                ]),

            Section::make('Bağlantı Ayarları')
                ->schema([
                    Select::make('linkable_type')
                        ->label('Bağlantı Türü')
                        ->options([
                            Page::class => 'Sayfa',
                            'custom' => 'Özel URL',
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn ($set) => $set('linkable_id', null)),

                    Select::make('linkable_id')
                        ->label('Bağlantı')
                        ->options(function (callable $get) {
                            $type = $get('linkable_type');

                            if ($type === Page::class) {
                                return Page::pluck('title', 'id');
                            }

                            return [];
                        })
                        ->searchable()
                        ->visible(fn (callable $get) => $get('linkable_type') !== 'custom'),

                    TextInput::make('url')
                        ->label('Özel URL')
                        ->visible(fn (callable $get) => $get('linkable_type') === 'custom')
                        ->required(fn (callable $get) => $get('linkable_type') === 'custom'),

                    Select::make('target')
                        ->label('Hedef')
                        ->options([
                            '_self' => 'Aynı Sekmede Aç',
                            '_blank' => 'Yeni Sekmede Aç',
                        ])
                        ->default('_self'),

                    Toggle::make('active')
                        ->label('Aktif')
                        ->default(true)
                        ->inline(false),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::components());
    }
}