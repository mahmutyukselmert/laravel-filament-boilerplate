<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\SaveAction;
use Filament\Resources\Pages\EditRecord;

use App\Services\Translation\TranslatorInterface;
use Filament\Actions\Action;

use Illuminate\Support\Str;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('translate')
            ->label('Diğer Dillere Çevir')
            ->icon('heroicon-o-language')
            ->requiresConfirmation()
            ->action(function () {
                $this->translatePageContent();
            }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Ana tablo güncelle
        $record->fill(Arr::only($data, ['image', 'is_active', 'template', 'sort_order']));
        $record->save();

        // Çeviriler güncelle
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $langId => $fields) {

                if (empty($fields['title'])) { //title verisi olmayan dili pas geçiyorum.
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

    //Çevirme fonksiyonu
    protected function translatePageContent(): void
    {
        $translator = app(TranslatorInterface::class);

        $defaultLang = Language::where('is_default', true)->first();
        $targetLangs = Language::where('id', '!=', $defaultLang->id)
            ->where('active', true)
            ->get();

        // Mevcut form verilerini al (Türkçe ve diğer her şey burada)
        $formData = $this->form->getState();
        $translations = $formData['translations'] ?? [];

        $source = $translations[$defaultLang->id] ?? null;

        if (! $source || empty($source['title'])) {
            \Filament\Notifications\Notification::make()
                ->title('Önce Türkçe içeriği (en azından başlığı) doldurun!')
                ->warning()
                ->send();
            return;
        }

        // Yeni verileri biriktireceğimiz bir dizi oluşturalım
        $newTranslations = $translations;

        foreach ($targetLangs as $lang) {
            // Çeviri işlemleri
            $translatedTitle = $translator->translate($source['title'] ?? '', $defaultLang->code, $lang->code) ?: ($source['title'] ?? '');
            $translatedSubtitle = $translator->translate($source['subtitle'] ?? '', $defaultLang->code, $lang->code) ?: ($source['subtitle'] ?? '');
            $translatedShortDesc = $translator->translate($source['short_description'] ?? '', $defaultLang->code, $lang->code) ?: ($source['short_description'] ?? '');
            
            // JSON içerikler için kontrol
            $rawContent = !empty($source['content']) ? json_encode($source['content']) : '';
            $translatedContent = $rawContent ? ($translator->translate($rawContent, $defaultLang->code, $lang->code) ?: $rawContent) : '';
            
            $rawSections = !empty($source['sections']) ? json_encode($source['sections']) : '[]';
            $translatedSections = $translator->translate($rawSections, $defaultLang->code, $lang->code) ?: $rawSections;

            // SADECE ilgili dilin altına veriyi yaz (Türkçe'ye dokunma)
            $newTranslations[$lang->id] = array_merge($newTranslations[$lang->id] ?? [], [
                'title' => $translatedTitle,
                'slug' => Str::slug($translatedTitle),
                'subtitle' => $translatedSubtitle,
                'short_description' => $translatedShortDesc,
                'content' => json_decode($translatedContent, true),
                'sections' => json_decode($translatedSections, true),
            ]);
        }

        // TÜM veriyi (Türkçe + Yeni Çeviriler) tek seferde forma geri yükle
        $this->form->fill([
            ...$formData,
            'translations' => $newTranslations
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Tüm diller için çeviri tamamlandı!')
            ->success()
            ->send();
    }
}
