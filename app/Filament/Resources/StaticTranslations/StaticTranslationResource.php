<?php

namespace App\Filament\Resources\StaticTranslations;

use App\Filament\Resources\StaticTranslations\Pages\CreateStaticTranslation;
use App\Filament\Resources\StaticTranslations\Pages\EditStaticTranslation;
use App\Filament\Resources\StaticTranslations\Pages\ListStaticTranslations;
use App\Filament\Resources\StaticTranslations\Schemas\StaticTranslationForm;
use App\Filament\Resources\StaticTranslations\Tables\StaticTranslationsTable;
use App\Models\StaticTranslation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StaticTranslationResource extends Resource
{
    protected static ?string $model = StaticTranslation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Dil Sözlüğü';
    protected static ?string $pluralModelLabel = 'Dil Sözlüğü';
    protected static ?string $modelLabel = 'Kelime Çevirisi';

    protected static ?string $recordTitleAttribute = 'key';

    public static function getNavigationGroup(): ?string
    {
        return 'Dil Ayarları';
    }

    public static function form(Schema $schema): Schema
    {
        return StaticTranslationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaticTranslationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaticTranslations::route('/'),
            'create' => CreateStaticTranslation::route('/create'),
            'edit' => EditStaticTranslation::route('/{record}/edit'),
        ];
    }
}
