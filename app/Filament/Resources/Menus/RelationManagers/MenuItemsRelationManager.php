<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\Language;
use App\Models\MenuItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;
// YENİ MİMARİDE (FILAMENT 4) TÜM AKSİYONLAR BURADADIR:
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Forms\Components\Toggle as FormToggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Components\Tree;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Menü Öğeleri';
    // Aksiyonların modal başlıklarında hata almamak için bir sütun adı belirtin
    protected static ?string $recordTitleAttribute = 'url';

    public function form(Schema $schema): Schema
    {
        $languages = Language::where('active', true)->orderBy('sort_order')->get();

        return $schema->components([
            Section::make('Menü Öğesi Bilgileri')
                ->columnSpanFull()
                ->schema([
                    FormSelect::make('parent_id')
                        ->label('Üst Öğesi')
                        ->relationship('parent', 'id')
                        ->searchable()
                        ->placeholder('Ana öğe'),
                    FormTextInput::make('url')
                        ->label('Manuel URL'),
                    FormSelect::make('target')
                        ->label('Hedef')
                        ->options([
                            '_self' => 'Aynı Sayfa',
                            '_blank' => 'Yeni Sekme',
                        ])
                        ->default('_self'),
                    FormToggle::make('active')
                        ->label('Aktif')
                        ->default(true),
                    Tabs::make('Etiketler')
                        ->tabs(
                            $languages->map(function ($lang) {
                                return Tabs\Tab::make($lang->name)
                                    ->schema([
                                        FormTextInput::make("translations.{$lang->id}.label")
                                            ->label('Etiket')
                                            ->required($lang->is_default),
                                    ]);
                            })->toArray()
                        )
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    /*
     * public function table(Table $table): Table
     * {
     *     return $table
     *         ->reorderable('sort_order')
     *         ->defaultSort('sort_order', 'asc')
     *         /*
     *          * ->columns([
     *          *     TextColumn::make('id')->label('ID'),
     *          *     TextColumn::make('label')->label('Etiket')->state(fn($record) => $record->translations()->first()?->label ?? '—'),
     *          *     TextColumn::make('url')->label('URL'),
     *          *     TextColumn::make('target')->label('Hedef'),
     *          * ])
     *         ->columns([
     *             TextColumn::make('id')->label('ID'),
     *             TextColumn::make('translations.label')  // Çevirideki etiketi gösterelim
     *                 ->label('Menü Öğesi')
     *                 ->formatStateUsing(function (MenuItem $record) {
     *                     $depth = 0;
     *                     $parent = $record->parent;
     *                     while ($parent) {
     *                         $depth++;
     *                         $parent = $parent->parent;
     *                     }
     *
     *                     $padding = str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $depth);
     *                     $prefix = $depth > 0 ? '↳ ' : '';
     *
     *                     $label = $record->translations->first()?->label ?? $record->url;
     *
     *                     return $padding . $prefix . $label;
     *                 })
     *                 ->html(),
     *             TextColumn::make('url')->label('URL')->color('gray'),
     *             TextColumn::make('target')->label('Hedef')->color('gray'),
     *         ])
     *         ->headerActions([
     *             CreateAction::make()
     *                 ->label('Yeni Menü Öğesi'),
     *         ])
     *         ->actions([
     *             EditAction::make()
     *                 ->mutateRecordDataUsing(function (MenuItem $record, array $data): array {
     *                     // Düzenleme formu açıldığında mevcut çevirileri yükle
     *                     $translations = $record->translations->pluck('label', 'language_id')->toArray();
     *                     foreach ($translations as $langId => $label) {
     *                         $data['translations'][$langId]['label'] = $label;
     *                     }
     *                     return $data;
     *                 })
     *                 ->after(function (MenuItem $record, array $data): void {
     *                     // Güncelleme sonrası çevirileri senkronize et
     *                     if (isset($data['translations'])) {
     *                         foreach ($data['translations'] as $langId => $translation) {
     *                             $record->translations()->updateOrCreate(
     *                                 ['language_id' => $langId],
     *                                 ['label' => $translation['label']]
     *                             );
     *                         }
     *                     }
     *                 }),
     *             DeleteAction::make(),
     *         ])
     *         ->bulkActions([
     *             BulkActionGroup::make([
     *                 DeleteBulkAction::make(),
     *             ]),
     *         ]);
     * }
     */

    public function table(Table $table): Table
    {
        return $table
            // Sorguyu modifiye etmek yerine doğrudan kayıtları hiyerarşik diziyoruz
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->query(function () {
                $ownerRecord = $this->getOwnerRecord();

                // Eğer ana kayıt yoksa boş sorgu dön
                if (!$ownerRecord) {
                    return MenuItem::query()->whereRaw('1 = 0');
                }

                // Tüm öğeleri al
                $allItems = $ownerRecord->items()->get();

                // PHP tarafında hiyerarşik sırala (ID listesi döner)
                $sortedIds = $this->sortItemsRecursive($allItems);

                // Veritabanından bu ID'lere sahip kayıtları çek
                // SQLite uyumlu sıralama: CASE WHEN id=1 THEN 0 WHEN id=2 THEN 1 ... END
                $query = MenuItem::query()->whereIn('id', $sortedIds);

                if (!empty($sortedIds)) {
                    $cases = [];
                    foreach ($sortedIds as $index => $id) {
                        $cases[] = "WHEN id = {$id} THEN {$index}";
                    }
                    $orderBy = 'CASE ' . implode(' ', $cases) . ' END';
                    $query->orderByRaw($orderBy);
                }

                return $query;
            })
            ->paginated(false)
            ->columns([
                TextColumn::make('translations.label')
                    ->label('Menü Yapısı')
                    ->formatStateUsing(function (MenuItem $record) {
                        $depth = 0;
                        $parent = $record->parent;
                        while ($parent) {
                            $depth++;
                            $parent = $parent->parent;
                        }

                        // Görsel hiyerarşi
                        $padding = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
                        $icon = $depth > 0 ? '<span class="text-gray-400">↳</span> ' : '';
                        $label = $record->translations->first()?->label ?? $record->url ?? '-';

                        return $padding . $icon . $label;
                    })
                    ->html(),
                TextColumn::make('url')
                    ->label('URL')
                    ->color('gray'),
                TextColumn::make('active')
                    ->label('Durum')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'Aktif' : 'Pasif'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Yeni Öğe Ekle')
                    ->after(fn(MenuItem $record, array $data) => $this->saveTranslations($record, $data)),
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (MenuItem $record, array $data): array {
                        // Düzenleme formu açıldığında mevcut çevirileri yükle
                        $translations = $record->translations->pluck('label', 'language_id')->toArray();
                        foreach ($translations as $langId => $label) {
                            $data['translations'][$langId]['label'] = $label;
                        }
                        return $data;
                    })
                    ->after(function (MenuItem $record, array $data): void {
                        // Güncelleme sonrası çevirileri senkronize et
                        if (isset($data['translations'])) {
                            foreach ($data['translations'] as $langId => $translation) {
                                $record->translations()->updateOrCreate(
                                    ['language_id' => $langId],
                                    ['label' => $translation['label']]
                                );
                            }
                        }
                    }),
                DeleteAction::make(),
            ]);
    }

    /**
     * Düz listeyi alır, Parent > Child > Grandchild sırasına göre ID dizisi döndürür.
     */
    protected function sortItemsRecursive($items, $parentId = null): array
    {
        $sortedIds = [];

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                // Önce anayı ekle
                $sortedIds[] = $item->id;

                // Sonra çocuklarını bul ve ekle (Recursive)
                $childrenIds = $this->sortItemsRecursive($items, $item->id);
                $sortedIds = array_merge($sortedIds, $childrenIds);
            }
        }

        return $sortedIds;
    }
}
