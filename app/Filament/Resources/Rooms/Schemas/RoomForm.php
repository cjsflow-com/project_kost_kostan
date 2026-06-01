<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Models\Room;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)
                    ->schema([
                        Section::make('Informasi Kamar')
                            ->schema([
                                TextInput::make('room_number')
                                    ->label('Nomor Ruangan')
                                    ->required(),

                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required(),

                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->default(null)
                                    ->columnSpanFull(),

                                TextInput::make('price_per_month')
                                    ->label('Harga Per Bulan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),

                                TextInput::make('room_size')
                                    ->label('Ukuran Ruangan')
                                    ->placeholder('Contoh: 4x5 m')
                                    ->default(null),

                                TextInput::make('floor')
                                    ->label('Lantai')
                                    ->numeric()
                                    ->default(null),

                                TextInput::make('capacity')
                                    ->label('Kapasitas')
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                Select::make('status_id')
                                    ->label('Status')
                                    ->options(Room::STATUS)
                                    ->required()
                                    ->native(false)
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 5,
                            ]),

                        Section::make('Fasilitas Kamar')
                            ->schema([
                                CheckboxList::make('facilities')
                                    ->label(false)
                                    ->relationship('facilities', 'name')
                                    ->columns(1)
                                    ->searchable(),
                            ])
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 3,
                            ]),

                        Section::make('Gambar Kamar')
                            ->schema([
                                Repeater::make('images')
                                    ->label(false)
                                    ->relationship('images')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Foto Kamar')
                                            ->image()
                                            ->imagePreviewHeight('160')
                                            ->acceptedFileTypes([
                                                'image/jpeg',
                                                'image/png',
                                                'image/webp',
                                            ])
                                            ->maxSize(1024)
                                            ->directory('rooms')
                                            ->disk('public')
                                            ->required(),
                                    ])
                                    ->addActionLabel('Tambah Gambar')
                                    ->reorderable()
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 4,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}