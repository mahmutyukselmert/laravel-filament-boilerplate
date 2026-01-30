<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use App\Services\Translation\TranslatorInterface;
use Filament\Notifications\Notification;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Çeviri Butonu
            Action::make('translate')
                ->label('Diğer Dillere Çevir')
                ->icon('heroicon-o-language')
                ->requiresConfirmation()
                ->color('warning')
                ->action(function () {
                    $this->translateServiceContent();
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    // Veriyi form açıldığında veritabanından çekip form yapısına oturtur
    protected function mutateFormDataBeforeFill(array $data): array
    {
        unset($data['translations']);

        $languages = Language::where('active', true)->orderBy('sort_order')->get();

        foreach ($languages as $lang) {
            $translation = $this->record->translations()
                ->where('language_id', $lang->id)
                ->first();

            $data['translations'][$lang->id] = $translation
                ? Arr::only($translation->toArray(), [
                    'title', 'slug', 'subtitle', 'short_description',
                    'content', 'sections', 'meta_title', 'meta_description', 'meta_keywords'
                ])
                : [];
        }

        return $data;
    }

    // Kaydet butonuna basıldığında ana tablo ve çeviri tablosunu günceller
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Ana tabloyu güncelle (Görsel, aktiflik vb.)
        $record->fill(Arr::only($data, ['image', 'is_active', 'sort_order', 'icon']));
        $record->save();

        // Çevirileri güncelle
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $langId => $fields) {
                if (empty($fields['title'])) {
                    continue;
                }

                $record->translations()->updateOrCreate(
                    ['language_id' => $langId],
                    $fields
                );
            }
        }

        return $record;
    }

    // Yapay Zeka ile Çeviri Fonksiyonu
    protected function translateServiceContent(): void
    {
        $translator = app(TranslatorInterface::class);

        $defaultLang = Language::where('is_default', true)->first();
        $targetLangs = Language::where('id', '!=', $defaultLang->id)
            ->where('active', true)
            ->get();

        $formData = $this->form->getState();
        $translations = $formData['translations'] ?? [];
        $source = $translations[$defaultLang->id] ?? null;

        if (! $source || empty($source['title'])) {
            Notification::make()
                ->title('Önce ana dildeki içeriği (Başlık) doldurun!')
                ->warning()
                ->send();
            return;
        }

        $newTranslations = $translations;

        foreach ($targetLangs as $lang) {
            // Başlık ve Alt Başlıklar
            $translatedTitle = $translator->translate($source['title'] ?? '', $defaultLang->code, $lang->code) ?: ($source['title'] ?? '');
            $translatedSubtitle = $translator->translate($source['subtitle'] ?? '', $defaultLang->code, $lang->code) ?: ($source['subtitle'] ?? '');
            $translatedShortDesc = $translator->translate($source['short_description'] ?? '', $defaultLang->code, $lang->code) ?: ($source['short_description'] ?? '');
            
            // Meta Alanları (SEO için otomatik çeviri)
            $translatedMetaTitle = $translator->translate($source['meta_title'] ?? '', $defaultLang->code, $lang->code);
            $translatedMetaDesc = $translator->translate($source['meta_description'] ?? '', $defaultLang->code, $lang->code);

            // İçerik ve Bölümler (JSON)
            $rawContent = !empty($source['content']) ? json_encode($source['content']) : '';
            $translatedContent = $rawContent ? ($translator->translate($rawContent, $defaultLang->code, $lang->code) ?: $rawContent) : '';
            
            $rawSections = !empty($source['sections']) ? json_encode($source['sections']) : '[]';
            $translatedSections = $translator->translate($rawSections, $defaultLang->code, $lang->code) ?: $rawSections;

            $newTranslations[$lang->id] = array_merge($newTranslations[$lang->id] ?? [], [
                'title' => $translatedTitle,
                'slug' => Str::slug($translatedTitle),
                'subtitle' => $translatedSubtitle,
                'short_description' => $translatedShortDesc,
                'meta_title' => $translatedMetaTitle,
                'meta_description' => $translatedMetaDesc,
                'content' => json_decode($translatedContent, true),
                'sections' => json_decode($translatedSections, true),
            ]);
        }

        // Formu yeni verilerle güncelle
        $this->form->fill([
            ...$formData,
            'translations' => $newTranslations
        ]);

        Notification::make()
            ->title('Hizmet içerikleri tüm dillere çevrildi!')
            ->success()
            ->send();
    }
}