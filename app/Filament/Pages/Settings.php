<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater; // Ekledik
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms; // CRITICAL
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

// HasForms eklemeyi unutma!
class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Site Ayarları';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Veritabanındaki ilk kaydı çek, yoksa boş bir model döndür
        $record = SiteSetting::first();
        
        if ($record) {
            // Formu veritabanındaki verilerle doldur
            $this->form->fill($record->toArray());
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Genel Ayarlar')
                    ->schema([
                        TextInput::make('site_name')->label('Site Adı'),
                        TextInput::make('email')->email()->label('E-Posta'),
                        TextInput::make('phone')->label('Telefon'),
                    ]),

                Section::make('İletişim Kanalları (Contacts)')
                    ->schema([
                        // Veritabanındaki JSON verisini yönetmek için Repeater şart
                        Repeater::make('contacts')
                            ->schema([
                                TextInput::make('type')->label('Tip (phone, mobile vb.)'),
                                TextInput::make('label')->label('Etiket'),
                                TextInput::make('display')->label('Görünür Değer'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columns(3),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        
        // Tek bir satırı (ID: 1) güncelle veya yoksa oluştur
        SiteSetting::updateOrCreate(
            ['id' => 1], // Arama kriteri
            $state       // Kaydedilecek veri
        );

        Notification::make()
            ->title('Ayarlar Başarıyla Güncellendi')
            ->success()
            ->send();
    }
}