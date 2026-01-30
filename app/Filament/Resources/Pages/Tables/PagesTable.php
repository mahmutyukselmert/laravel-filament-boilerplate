<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Language;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        $defaultLangId = Language::query()
            ->where('is_default', true)
            ->value('id');

        return $table
            ->filters([
                TrashedFilter::make(),
            ])
            ->reorderable('sort_order')
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
                Action::make('view')
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => url($record->translations()->where('language_id', $defaultLangId)->first()?->slug ?? $record->slug))
                ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->actionsColumnLabel('İşlemler')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])->defaultSort('sort_order', 'asc');
    }
}
