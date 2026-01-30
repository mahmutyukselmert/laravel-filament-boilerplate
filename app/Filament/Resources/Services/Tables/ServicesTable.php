<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\Action;
use App\Models\Language;

class ServicesTable
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

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Ana Sayfada Göster')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('view')
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->url(fn ($record): ?string =>
                    ($slug = $record->translations()
                        ->where('language_id', $defaultLangId)
                        ->value('slug'))
                        ? '/' . ltrim($slug, '/')
                        : null
                )
                ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make()
                ->requiresConfirmation(),
            ])
            ->actionsColumnLabel('İşlemler')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                    ->requiresConfirmation(),
                    ForceDeleteBulkAction::make()
                    ->requiresConfirmation(),
                    RestoreBulkAction::make()
                    ->requiresConfirmation(),
                ]),
            ]);
    }
}
