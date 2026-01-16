<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('admin_title'),
                TextEntry::make('type'),
                TextEntry::make('data')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
