<?php

namespace App\Filament\Resources\Rooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room_number')
                    ->label("Nomor Ruangan")
                    ->searchable(),
                TextColumn::make('title')
                    ->label("Judul")
                    ->searchable(),
                TextColumn::make('price_per_month')
                    ->label("Harga Per Bulan")
                    ->numeric()
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    ),
                TextColumn::make('room_size')
                    ->label("Ukuran Ruangan")
                    ->searchable(),
                TextColumn::make('floor')
                    ->label("Lantai")
                    ->numeric(),
                TextColumn::make('capacity')
                    ->label("Kapasitas")
                    ->numeric(),
                TextColumn::make('status_id')
                    ->label("Status Kamar")
                    ->badge()
                    ->color(fn ($state) => match ((int) $state) {
                        0 => 'danger',   // Perbaikan
                        1 => 'success',  // Tersedia
                        2 => 'warning',  // Dipesan
                        3 => 'info',     // Sudah Ditempati
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => \App\Models\Room::STATUS[$state] ?? 'Unknown'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
