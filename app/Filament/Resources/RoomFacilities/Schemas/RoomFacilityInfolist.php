<?php

namespace App\Filament\Resources\RoomFacilities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoomFacilityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('icon')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
