<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Language;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        $defaultLangId = Language::query()
            ->where('is_default', true)
            ->value('id');

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->state(fn($record) => $record->translations()
                        ->where('language_id', $defaultLangId)
                        ->first()?->title ?? '—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->state(fn($record) => $record->translations()
                        ->where('language_id', $defaultLangId)
                        ->first()?->slug ?? '—')
                    ->toggleable()
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->actionsColumnLabel('İşlemler')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
