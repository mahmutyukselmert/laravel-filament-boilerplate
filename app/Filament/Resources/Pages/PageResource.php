<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Pages\ViewPage;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Schemas\PageInfolist;
use App\Filament\Resources\Pages\Tables\PagesTable;
use App\Models\Page;
use App\Models\PageTranslation;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    // ✅ Tip düzeltildi
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Sayfalar';

    protected static ?string $modelLabel = 'Sayfa';

    protected static ?string $pluralModelLabel = 'Sayfalar';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return \App\Filament\Resources\Pages\Schemas\PageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    // ✅ Custom Save Logic (Filament 4.4 uyumlu)
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $data['_translations'] = $translations;
        return $data;
    }

    public static function afterCreate(Page $record, array $data): void
    {
        if (!empty($data['_translations'])) {
            foreach ($data['_translations'] as $langId => $fields) {
                PageTranslation::create(array_merge($fields, [
                    'page_id' => $record->id,
                    'language_id' => $langId,
                ]));
            }
        }
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $data['_translations'] = $translations;
        return $data;
    }

    public static function afterSave(Page $record, array $data): void
    {
        if (!empty($data['_translations'])) {
            foreach ($data['_translations'] as $langId => $fields) {
                $record
                    ->translations()
                    ->updateOrCreate(['language_id' => $langId], $fields);
            }
        }
    }
}
