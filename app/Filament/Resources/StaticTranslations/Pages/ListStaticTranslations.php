<?php

namespace App\Filament\Resources\StaticTranslations\Pages;

use App\Filament\Resources\StaticTranslations\StaticTranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaticTranslations extends ListRecords
{
    protected static string $resource = StaticTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
