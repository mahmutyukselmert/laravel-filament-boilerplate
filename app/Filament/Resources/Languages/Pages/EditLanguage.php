<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Filament\Resources\Languages\LanguageResource;
use Filament\Actions\BackAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

use Filament\Actions\Action;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Geri')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => static::$resource::getUrl('index')),
            //ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
