<?php

namespace App\Filament\Resources\Menus\Schemas;

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

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menü Bilgileri')
                    ->schema([
                        TextInput::make('name')
                            ->label('Menü Adı')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('key', \Illuminate\Support\Str::slug($state)); 
                            }),
                        TextInput::make('key')
                            ->label('Menü Anahtarı')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatik olarak oluşturulur, isterseniz değiştirebilirsiniz.'),
                        Select::make('location')->label('Konum')->options([
                            'header' => 'Header',
                            'footer' => 'Footer',
                            'custom' => 'Özel',
                        ])->required(),
                        Toggle::make('active')->label('Aktif')->default(true),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ]);
    }
}
