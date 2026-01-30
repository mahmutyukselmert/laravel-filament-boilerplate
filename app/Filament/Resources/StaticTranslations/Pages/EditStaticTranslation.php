<?php

namespace App\Filament\Resources\StaticTranslations\Pages;

use App\Filament\Resources\StaticTranslations\StaticTranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaticTranslation extends EditRecord
{
    protected static string $resource = StaticTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // EditStaticTranslation.php ve CreateStaticTranslation.php içine:
    protected function afterSave(): void
    {
        // Kayıt güncellendiğinde 'static_translations' isimli cache'i sil
        cache()->forget('static_translations');
    }

    protected function afterCreate(): void
    {
        // Yeni kayıt eklendiğinde de cache'i sil
        cache()->forget('static_translations');
    }

}
