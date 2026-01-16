<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\RelationManagers\MenuItemsRelationManager;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use App\Models\Menu;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use BackedEnum;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menüler';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Menü Adı')->sortable()->searchable(),
            TextColumn::make('location')->label('Konum'),
            IconColumn::make('active')->label('Aktif')->boolean(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
