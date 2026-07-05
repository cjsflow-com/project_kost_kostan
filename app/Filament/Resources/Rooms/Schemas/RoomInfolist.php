<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Models\Room;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)
                    ->schema([
                        Section::make('Informasi Kamar')
                            ->schema([
                                TextEntry::make('room_number')
                                    ->label('Nomor Ruangan'),

                                TextEntry::make('title')
                                    ->label('Judul'),

                                TextEntry::make('price_per_month')
                                    ->label('Harga Per Bulan')
                                    ->formatStateUsing(
                                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                                    ),

                                TextEntry::make('room_size')
                                    ->label('Ukuran Ruangan'),

                                TextEntry::make('floor')
                                    ->label('Lantai'),

                                TextEntry::make('capacity')
                                    ->label('Kapasitas'),

                                TextEntry::make('status_id')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => Room::STATUS[$state] ?? 'Unknown')
                                    ->color(fn ($state) => match ((int) $state) {
                                        0 => 'danger',
                                        1 => 'success',
                                        2 => 'warning',
                                        3 => 'info',
                                        default => 'gray',
                                    }),
                                TextEntry::make('activeTenant.customer.name')
                                    ->label('Nama Penghuni')
                                    ->placeholder('-'),
                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 5,
                            ]),

                        Section::make('Fasilitas Kamar')
                            ->schema([
                                RepeatableEntry::make('facilities')
                                    ->label(false)
                                    ->schema([
                                        ImageEntry::make('icon')
                                            ->label("Ikon")
                                            ->disk('public')
                                            ->imageHeight(32),

                                        TextEntry::make('name')
                                            ->label("Nama"),

                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'default' => 12,
                                'lg' => 3,
                            ]),

                        Section::make('Informasi Gambar Kamar')
                            ->schema([
                                RepeatableEntry::make('images')
                                    ->label("Gambar Kamar")
                                    ->schema([
                                        ImageEntry::make('image')
                                            ->label("Gambar")
                                            ->disk('public')
                                            ->height(160),
                                    ])
                                    ->columns(1)
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