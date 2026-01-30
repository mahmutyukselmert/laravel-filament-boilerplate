<?php

namespace App\Filament\Resources\StaticTranslations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Columns\TextColumn;

use App\Models\Language;

class StaticTranslationsTable
{
    public static function configure(Table $table): Table
    {
        // 1. Sütunları dışarıda bir dizi olarak hazırlıyoruz
        $columns = [
            TextColumn::make('key')
                ->label('Sistem Anahtarı')
                ->searchable()
                ->sortable()
                ->copyable()
                ->description(fn ($record) => '_translate("' . $record->key . '")'),
        ];

        // 2. Aktif dilleri çekip diziye ekliyoruz
        // Not: Burada try-catch veya kontrol eklemek sağlıklı olabilir 
        // çünkü migration sırasında henüz tablo oluşmamış olabilir.
        try {
            $languages = Language::where('active', true)->get();
            foreach ($languages as $lang) {
                if ($lang->code == 'tr' || $lang->code == 'en') {
                    $columns[] = TextColumn::make("text.{$lang->code}")
                        ->label($lang->title . " Karşılığı")
                        ->searchable();
                } 
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa hata vermemesi için
        }

        $columns[] = TextColumn::make('updated_at')
            ->label('Son Güncelleme')
            ->dateTime('d/m/Y H:i')
            ->toggleable(isToggledHiddenByDefault: true);

        // 3. Hazırladığımız diziyi columns() içine veriyoruz
        return $table
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}