<?php
namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater; // Ekledik
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms; // CRITICAL
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;   
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;

use App\Helpers\ContactHelper;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Site Ayarları';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected string $view = 'filament.pages.settings';

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public function mount(): void
    {
        $record = SiteSetting::first();
        
        if ($record) {
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
                        
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->formatStateUsing(fn ($state) =>
                                $state ? ContactHelper::format($state, 'phone') : null
                            )
                            ->dehydrateStateUsing(fn ($state) => ContactHelper::normalize($state)),

                        TextInput::make('phone_gsm')
                            ->label('Telefon (GSM)')
                            ->formatStateUsing(fn ($state) =>
                                $state ? ContactHelper::format($state, 'phone_gsm') : null
                            )
                            ->dehydrateStateUsing(fn ($state) => ContactHelper::normalize($state)),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->formatStateUsing(fn ($state) =>
                                $state ? ContactHelper::format($state, 'whatsapp') : null
                            )
                            ->dehydrateStateUsing(fn ($state) => ContactHelper::normalize($state)),

                        TextInput::make('fax')->label('Fax'),
                        Textarea::make('address')->label('Adres'),
                        Textarea::make('map')->label('Google Harita')->rows(5),
                    ]),

                Section::make('Logo Ayarları')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                        ->collection('logos')
                        ->disk('public')
                        ->image(),
                        SpatieMediaLibraryFileUpload::make('scrolled_logo')
                        ->collection('logos')
                        ->disk('public')
                        ->image(),
                        SpatieMediaLibraryFileUpload::make('footer_logo')
                        ->collection('logos')
                        ->disk('public')
                        ->image(),
                    ]),

                Section::make('İletişim Bilgileri')
                    ->schema([
                        // Veritabanındaki JSON verisini yönetmek için Repeater şart
                        Repeater::make('contacts')
                            ->label('İletişim Bilgisi')
                            ->schema([
                                TextInput::make('type')->label('Tip (phone, mobile vb.)'),
                                TextInput::make('label')->label('Etiket'),
                                TextInput::make('icon')
                                    ->label('İkon')
                                    ->placeholder('İkon seçin veya yazın...')
                                    ->id('icon-input')
                                    ->suffixAction(
                                        Action::make('open_icon_picker')
                                            ->icon('heroicon-m-magnifying-glass')
                                            ->label('Göz At')
                                            ->color('info')
                                            ->modalHeading('İkon Kütüphanesi')
                                            ->modalSubmitAction(false) 
                                            ->modalContent(fn ($component) => view('filament.forms.icon-picker-modal', [
                                                'statePath' => $component->getStatePath(),
                                            ]))
                                    ),  
                                TextInput::make('display')
                                    ->label('Telefon')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $digits = preg_replace('/\D+/', '', $state); // sadece rakam

                                        // 0 ile başlıyorsa → TR format
                                        if (str_starts_with($digits, '0')) {
                                            $digits = '9' . $digits;
                                        }

                                        // +90 yazılmışsa zaten 90 ile başlar
                                        if (str_starts_with($digits, '90') === false) {
                                            // güvenlik: 10 haneliyse başına 90 ekle
                                            if (strlen($digits) === 10) {
                                                $digits = '90' . $digits;
                                            }
                                        }

                                        $set('value', $digits);
                                    }),
                                Hidden::make('value'),

                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->columns(2),
                    ])->collapsible()
                    ->collapsed(),

                Section::make('Sosyal Medya')
                    ->schema([
                        // Sabit kolonlar
                        TextInput::make('facebook')->label('Facebook'),
                        TextInput::make('instagram')->label('Instagram'),
                        TextInput::make('linkedin')->label('LinkedIn'),
                        TextInput::make('x_twitter')->label('X / Twitter'),
                        TextInput::make('youtube')->label('YouTube'),

                        // Dinamik eklemeler
                        Repeater::make('social_extra')
                            ->label('Diğer Sosyal Medya')
                            ->schema([
                                Select::make('type')->label('Tip')->options([
                                    'tiktok' => 'TikTok',
                                    'telegram' => 'Telegram',
                                    'pinterest' => 'Pinterest',
                                    'diğer' => 'Diğer',
                                ]),
                                TextInput::make('link')->label('Link'),
                                TextInput::make('icon')
                                    ->label('İkon')
                                    ->placeholder('İkon seçin veya yazın...')
                                    ->id('icon-input')
                                    // Sağ tarafa buton ekliyoruz
                                    ->suffixAction(
                                        Action::make('open_icon_picker')
                                            ->icon('heroicon-m-magnifying-glass') // Büyüteç ikonu
                                            ->label('Göz At')
                                            ->color('info')
                                            ->modalHeading('İkon Kütüphanesi')
                                            ->modalSubmitAction(false) // Alt butonları gizle
                                            ->modalContent(fn ($component) => view('filament.forms.icon-picker-modal', [
                                                'statePath' => $component->getStatePath(),
                                            ]))
                                    ),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['type'] ?? null)
                            ->columns(2),
                    ])->collapsible()
                    ->collapsed(),      
                    
                Section::make('Bakım Modu')
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->label('Bakım Modu')
                            ->helperText('Bakım Modu aktif olduğunda site bakım modunda olur.')
                            ->default(Cache::get('site_maintenance_mode', false))
                            ->inline(),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        
        SiteSetting::updateOrCreate(
            ['id' => 1],
            $state
        );

        if ($state['maintenance_mode']) {
            Artisan::call('down', [
                '--secret' => 'login-just-eliz',
            ]);

            SiteSetting::updateOrCreate(
                ['id' => 1],
                ['maintenance_mode' => true]
            );

            Notification::make()
                ->title('Site Bakım Moduna Alındı')
                ->warning()
                ->send();

            $this->redirect('/login-just-eliz');
        } else {
            Artisan::call('up');

            SiteSetting::updateOrCreate(
                ['id' => 1],
                ['maintenance_mode' => false]
            );

            Notification::make()
                ->title('Site Yayına Alındı')
                ->success()
                ->send();
        }

        Notification::make()
            ->title('Ayarlar Başarıyla Güncellendi')
            ->success()
            ->send();
    }
}