<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Filament\Resources\MenuItems\Schemas\MenuItemForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'label';

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('translations.label')
                    ->label('Menü Adı')
                    ->formatStateUsing(fn ($record) => $record->translations->first()?->label ?? '—'),
                TextColumn::make('resolveUrl')
                    ->label('URL'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->form(MenuItemForm::components()),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(MenuItemForm::components()),
                DeleteAction::make(),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('İlk Menü Öğesini Oluştur')
                    ->form(MenuItemForm::components()),
            ]);
    }
}
