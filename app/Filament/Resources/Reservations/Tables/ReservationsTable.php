<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Nama Pelanggan')
                     ->searchable()
                    ->sortable(),
                TextColumn::make('room.title')
                    ->label('Nama Kamar')
                     ->searchable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reservation_code')
                    ->label('Kode Pemesanan')
                     ->searchable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai' )
                    ->date()
                    ->sortable(),
                TextColumn::make('duration_month')
                    ->label('Durasi (Bulan)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room_price')
                    ->label('Harga Kamar')
                    ->money()
                    ->sortable(),
                TextColumn::make('admin_fee')
                    ->label('Biaya Admin')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deposit')
                    ->label('Deposit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Reservation::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Reservation::STATUS_PENDING => 'warning',
                        Reservation::STATUS_WAITING_PAYMENT => 'info',
                        Reservation::STATUS_PAYMENT_UPLOADED => 'primary',
                        Reservation::STATUS_APPROVED => 'success',
                        Reservation::STATUS_REJECTED => 'danger',
                        Reservation::STATUS_CANCELLED => 'gray',
                        Reservation::STATUS_EXPIRED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
