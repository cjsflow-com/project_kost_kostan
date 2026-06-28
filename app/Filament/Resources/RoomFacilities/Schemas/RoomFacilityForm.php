<?php

namespace App\Filament\Resources\RoomFacilities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoomFacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Fasilitas Kamar')
                    ->required(),
                FileUpload::make('icon')
                    ->label('Ikon')
                    ->required()
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/svg+xml',
                    ])
                    ->maxSize(1024)
                    ->directory('room-facilities')
                    ->disk('public')
                    ->imagePreviewHeight('100')
                    ->default(null),
            ]);
    }
}
