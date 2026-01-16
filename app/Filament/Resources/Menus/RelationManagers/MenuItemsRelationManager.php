<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\Language;
use App\Models\MenuItem;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;

// YENİ MİMARİDE (FILAMENT 4) TÜM AKSİYONLAR BURADADIR:
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Forms\Components\Toggle as FormToggle;

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

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order') 
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('label')->label('Etiket')->state(fn($record) => $record->translations()->first()?->label ?? '—'),
                TextColumn::make('url')->label('URL'),
                TextColumn::make('target')->label('Hedef'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Yeni Menü Öğesi'),
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}