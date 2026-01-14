<?php

namespace App\Filament\Resources\MenuItems\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order') // Drag & Drop
            ->columns([
                TextColumn::make('translations.label')
                    ->label('Menü Adı')
                    ->formatStateUsing(fn ($record) => $record->translations->first()?->label ?? '—')
                    ->searchable(),
                TextColumn::make('url')->label('URL'),
                IconColumn::make('active')->boolean()->label('Aktif'),
            ])
            ->recordActions([
            ]);
    }
}
