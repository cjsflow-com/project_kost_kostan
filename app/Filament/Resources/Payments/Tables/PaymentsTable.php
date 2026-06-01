<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation_id')
                    ->label('ID Reservasi')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_method_id')
                    ->label('ID Metode Pembayaran')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->searchable(),
                TextColumn::make('payment_code')
                    ->label('Kode Pembayaran')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => \App\Models\Payment::STATUS[$state] ?? 'Belum Ada Status')
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'uploaded' => 'primary',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->badge(),
                TextColumn::make('paid_at')
                    ->label('Tanggal Dibayar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('uploaded_at')
                    ->label('Tanggal Diunggah')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label('Tanggal Diverifikasi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('rejected_at')
                    ->label('Tanggal Ditolak')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
