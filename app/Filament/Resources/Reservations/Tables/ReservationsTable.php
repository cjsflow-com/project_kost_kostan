<?php

namespace App\Filament\Resources\Reservations\Tables;

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
                TextColumn::make('user_id')
                    ->label('Nama Pelanggan')
                     ->searchable()
                    ->sortable(),
                TextColumn::make('room_id')
                    ->label('Nama Kamar')
                     ->searchable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reservation_code')
                    ->label('Kode Pemesanan')
                     ->searchable()
                    ->searchable(),
                TextColumn::make('customer_ktp_card')
                    ->label('KTP Pelanggan')
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
                    ->badge(),
                TextColumn::make('payment_due_at')
                    ->label('Tanggal Jatuh Tempo Pembayaran')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('Tanggal Disetujui')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('rejected_at')
                    ->label('Tanggal Ditolak')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('cancelled_at')
                    ->label('Tanggal Dibatalkan')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expired_at')
                    ->label('Tanggal Kedaluwarsa')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
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
