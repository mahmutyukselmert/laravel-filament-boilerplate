<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;  // v4'te buraya taşındı
use Filament\Schemas\Schema;

class HomePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Ana Sayfa';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.home-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(config('homepage.settings', [
            'slider' => []
        ]));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Slider İçerikleri')
                    ->schema([
                        Repeater::make('slider')
                            ->label('Slaytlar')
                            ->schema([
                                // 1. Slide Tipi Seçimi
                                Select::make('slide_type')
                                    ->label('Slide Tipi')
                                    ->options([
                                        'image' => 'Görsel',
                                        'video' => 'Video / Embed',
                                    ])
                                    ->default('image')
                                    ->live() // v4'te reactive() yerine live() daha stabildir
                                    ->columnSpanFull(),

                                // 2. Görsel Alanı (Sadece 'image' seçiliyse görünür)
                                FileUpload::make('image')
                                    ->label('Öne Çıkan Görsel')
                                    ->image()
                                    ->disk('public')
                                    ->directory('slider')
                                    ->imageEditor()
                                    ->hidden(fn (callable $get) => $get('slide_type') !== 'image')
                                    ->saveUploadedFileUsing(function ($file) {
                                        $manager = new ImageManager(new Driver());
                                        $name = Str::random(10).'_'.time().'.webp';
                                        
                                        // Directory kontrolü
                                        if (!file_exists(storage_path('app/public/slider'))) {
                                            mkdir(storage_path('app/public/slider'), 0755, true);
                                        }

                                        $path = storage_path('app/public/slider/' . $name);
                                        $img = $manager->read($file);
                                        $img->toWebp(90)->save($path);
                                        
                                        return 'slider/' . $name;
                                    }),

                                // VİDEO YÜKLEME (PC'den mp4)
                                FileUpload::make('video_path')
                                    ->label('Slayt Videosu (.mp4)')
                                    ->disk('public')
                                    ->directory('slider/videos')
                                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/ogg'])
                                    ->maxSize(51200) // 50MB Sınırı (php.ini ayarlarınıza dikkat edin)
                                    ->preserveFilenames()
                                    ->visible(fn (callable $get) => $get('slide_type') === 'video'),

                                // 3. Video Alanı (Sadece 'video' seçiliyse görünür)
                                TextInput::make('video_url')
                                    ->label('Video URL veya Iframe')
                                    ->placeholder('https://youtube.com/...')
                                    ->hidden(fn (callable $get) => $get('slide_type') !== 'video'),

                                // 4. Ortak Metin Alanları
                                Grid::make(2)->schema([
                                    TextInput::make('title')->label('Başlık'),
                                    TextInput::make('subtitle')->label('Alt Başlık'),
                                    Textarea::make('content')->label('İçerik Metni')->columnSpanFull(),
                                ]),
                                
                                Repeater::make('buttons')
                                    ->label('Butonlar')
                                    ->itemLabel(fn (array $state): ?string => $state['button_text'] ?? 'Yeni Buton')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('button_link')->label('Buton Linki'),
                                            TextInput::make('button_text')->label('Buton Metni'),
                                        ]),
                                    ])
                                    ->cloneable()
                                    ->collapsible()
                                    ->defaultItems(1)
                                    ->maxItems(2),

                                // 5. Link ve Aktiflik
                                Grid::make(1)->schema([
                                    Toggle::make('active')->label('Aktif')->default(true)->inline(false),
                                ]),
                            ])
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni Slayt'),
                    ])
            ]);
    }

    public function save(): void
    {
        // Kaydetme mantığını buraya ekleyebilirsin
        Notification::make()->title('Başarıyla kaydedildi')->success()->send();
    }
}