<?php

namespace App\Filament\Pages;

use App\Models\Language;
use App\Models\Slider;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;  // v4'te buraya taşındı
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

use App\Models\SliderTranslation;

class HomePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Ana Sayfa';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected string $view = 'filament.pages.home-page';

    public ?array $data = [];

    public function mount(): void
    {
        // 1. Sliderları ve onlara ait çevirileri beraber çekiyoruz
        $sliders = \App\Models\Slider::where('category', 'home-slider')
        ->with('translations')
        ->orderBy('sort_order')
        ->get()
        ->map(function ($slider) {
            $data = $slider->toArray();
            $data['translations'] = $slider->translations
                ->pluck(null, 'language_id')
                ->toArray();
            return $data;
        })
        ->toArray();

        // 2. Sayfa SEO verileri (mevcut kodun)
        $page = \App\Models\Page::where('template', 'home')->first();
        $translations = [];

        if ($page) {
            $translations = $page->translations->pluck(null, 'language_id')->toArray();
        }

        // 3. Formu doldur
        $this->form->fill([
            'slider' => $sliders,
            'translations' => $translations, 
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $activeLanguages = Language::where('active', true)
            ->orderBy('sort_order')
            ->get();

        return $schema
            ->statePath('data')
            ->components([
                // HomePage.php içine eklenecek SEO alanları
                Section::make('Sayfa SEO ve Kimlik')
                    ->schema([
                        Tabs::make('SEO')
                            ->tabs($activeLanguages->map(function ($lang) {
                                return Tabs\Tab::make($lang->name)
                                    ->schema([
                                        TextInput::make("translations.{$lang->id}.meta_title")
                                            ->label('Sayfa Başlığı')
                                            ->placeholder('Anahtar Kelimeniz - Site Adınız')
                                            ->default(function (callable $get) {
                                                return config('app.name');
                                            }),
                                        Textarea::make("translations.{$lang->id}.meta_description")
                                            ->label('Meta Açıklama')
                                            ->placeholder('Sayfa Açıklaması')
                                            ->default(function (callable $get) {
                                                return config('app.name');
                                            }),

                                        TextInput::make("translations.{$lang->id}.meta_keywords")
                                            ->label('Meta Kelimeleri')
                                            ->placeholder('Meta Kelimeleri')
                                            ->default(function (callable $get) {
                                                return config('app.name');
                                            }),
                                    ]);
                            })->toArray()),
                    ]),

                Section::make('Slider İçerikleri')
                    ->schema([
                        Repeater::make('slider')
                            ->label('Slaytlar')
                            ->itemLabel(function (array $state): ?string {
        // 1 numara genelde Türkçe ID'sidir, eğer yapıya göre farklıysa değiştirilebilir.
        // Önce Türkçe başlığa bak, yoksa herhangi bir dildeki ilk başlığı al, o da yoksa varsayılan metni dön.
        return $state['translations'][1]['title'] 
            ?? collect($state['translations'] ?? [])->first()['title'] 
            ?? 'Yeni Slayt';
    })
                            ->schema([
                                // 1. Slide Tipi Seçimi
                                Select::make('slide_type')
                                    ->label('Slide Tipi')
                                    ->options([
                                        'image' => 'Görsel',
                                        'video' => 'Video (MP4)',
                                        'video_url' => 'Video URL (Youtube/Vimeo)',
                                    ])
                                    ->default('image')
                                    ->live()
                                    ->reactive()
                                    ->columnSpanFull(),
                                
                                Tabs::make('desktop_and_mobile_media')
                                    ->label('Masaüstü ve Mobil Media Ayarları')
                                    ->tabs([
                                        Tabs\Tab::make('Desktop')->label('Masaüstü')
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Masaüstü Görsel')
                                                    ->image()->disk('public')->directory('slider')->visible(fn ($get) => $get('slide_type') === 'image'),
                                                
                                                FileUpload::make('video_path')
                                                    ->label('Masaüstü Video (.mp4)')
                                                    ->disk('public')->directory('slider/videos')
                                                    ->visible(fn ($get) => $get('slide_type') === 'video'),
                                                    
                                                TextInput::make('video_url')
                                                    ->label('Masaüstü Video URL')
                                                    ->placeholder('https://youtube.com/...')
                                                    ->visible(fn ($get) => $get('slide_type') === 'video_url'),
                                                ]),
                                        Tabs\Tab::make('Mobile')
                                            ->label('Mobil')
                                            ->schema([
                                                FileUpload::make('mobile_image')
                                                    ->label('Mobil Görsel (Opsiyonel)')
                                                    ->image()->disk('public')->directory('slider/mobile')
                                                    ->visible(fn ($get) => $get('slide_type') === 'image')
                                                    ->reactive(),
                                                
                                                FileUpload::make('mobile_video_path')
                                                    ->label('Mobil Video (.mp4)')
                                                    ->disk('public')->directory('slider/videos/mobile')
                                                    ->visible(fn ($get) => $get('slide_type') === 'video')  
                                                    ->reactive(),
                                                
                                                TextInput::make('mobile_video_url')
                                                    ->label('Mobil Video URL')
                                                    ->placeholder('https://youtube.com/...')
                                                    ->visible(fn ($get) => $get('slide_type') === 'video_url'),
                                            ])
                                        ]),                                        

                                // 3. Çoklu Dil İçerik Alanları
                                // Her dil için ayrı bir tab açarak karmaşayı önlüyoruz
                                Tabs::make('translations')
                                    ->label('Dil Bazlı İçerikler')
                                    ->tabs($activeLanguages->map(function ($lang) {
                                            return Tabs\Tab::make($lang->name)
                                                ->schema([
                                                    TextInput::make("translations.{$lang->id}.title")
                                                        ->label('Başlık')
                                                        ->placeholder('Başlık')
                                                        ->default(function (callable $get) {
                                                            return config('app.name');
                                                        }),
                                                    Textarea::make("translations.{$lang->id}.subtitle")
                                                        ->label('Alt Başlık')
                                                        ->placeholder('Alt Başlık')
                                                        ->default(function (callable $get) {
                                                            return config('app.name');
                                                        }),

                                                    TextInput::make("translations.{$lang->id}.content")
                                                        ->label('İçerik')
                                                        ->placeholder('İçerik')
                                                        ->default(function (callable $get) {
                                                            return config('app.name');
                                                        }),
                                                ]);
                                        })->toArray()),

                                // 4. Ortak Butonlar (Dile göre değişsin dersen yukarıdaki Tab içine taşıyabilirsin)
                                Repeater::make('buttons')
                                    ->label('Butonlar')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('button_text')->label('Buton Metni'),
                                            TextInput::make('button_link')->label('Buton Linki'),
                                        ]),
                                    ])->maxItems(3)
                                    ->collapsible()
                                    ->collapsed()
                                    ->addActionLabel('Yeni Buton Ekle')
                                    ->columnSpanFull(),

                                Toggle::make('active')->label('Aktif')->default(true),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->cloneable()
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->reorderableWithDragAndDrop(true)
                            ->addActionLabel('Yeni Slayt Ekle')
                ])
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $sliderData = $state['slider'] ?? [];

        // 1. Mevcut slider ID'lerini topla (Silinmesi gerekenleri tespit etmek için)
        $existingSliderIds = \App\Models\Slider::pluck('id')->toArray();
        $newSliderIds = [];

        foreach ($sliderData as $index => $slide) {
            // Çevirileri ana veriden ayır
            $translations = $slide['translations'] ?? [];
            unset($slide['translations']);

            // Slider'ı Güncelle veya Oluştur (ID varsa güncelle, yoksa ekle)
            $slider = \App\Models\Slider::updateOrCreate(
                ['id' => $slide['id'] ?? null], // Arama kriteri (ID)
                [
                    ...$slide,
                    'sort_order' => $index,
                    'category' => 'home-slider',
                ]
            );

            $newSliderIds[] = $slider->id;

            // 2. Slider Çevirilerini Güncelle/Oluştur
            foreach ($translations as $langId => $transData) {
                \App\Models\SliderTranslation::updateOrCreate(
                    [
                        'slider_id' => $slider->id,
                        'language_id' => $langId
                    ],
                    [
                        'title' => $transData['title'] ?? null,
                        'subtitle' => $transData['subtitle'] ?? null,
                        'content' => $transData['content'] ?? null,
                        'buttons' => $slide['buttons'] ?? null, // Butonlar dile göreyse buraya alabilirsin
                    ]
                );
            }
        }

        // 3. Formdan silinen sliderları veritabanından (SoftDelete) sil
        $idsToDelete = array_diff($existingSliderIds, $newSliderIds);
        if (!empty($idsToDelete)) {
            \App\Models\Slider::whereIn('id', $idsToDelete)->delete();
        }

        // --- Sayfa SEO Kayıtları ---
        $page = \App\Models\Page::updateOrCreate(['template' => 'home'], ['is_active' => true]);
        foreach ($state['translations'] as $langId => $data) {
            \App\Models\PageTranslation::updateOrCreate(
                ['page_id' => $page->id, 'language_id' => $langId],
                [
                    'meta_title' => $data['meta_title'] ?? config('app.name'),
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                    'title' => 'Ana Sayfa',
                    'slug' => '/',
                ]
            );
        }

        Notification::make()->title('Başarıyla güncellendi.')->success()->send();
    }
}