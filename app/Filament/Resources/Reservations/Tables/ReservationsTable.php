<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
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
                     ->numeric()
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
                TextColumn::make('admin_fee')
                    ->label('Biaya Admin')
                    ->numeric()
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
                TextColumn::make('deposit')
                    ->label('Deposit')
                     ->numeric()
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total Harga')
                     ->numeric()
                    ->formatStateUsing(
                        fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Reservation::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Reservation::STATUS_PENDING => 'warning',
                        Reservation::STATUS_WAITING_PAYMENT => 'info',
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
                 Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pemesanan')
                    ->modalDescription('Apakah kamu yakin ingin mengkonfirmasi pemesanan ini?')
                    ->modalSubmitActionLabel('Ya, Konfirmasi')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status' => 'waiting_payment',
                        ]);

                        $record->statusHistories()->create([
                            'reservation_id' => $record->id,
                            'status' => 'waiting_payment',
                            'title' => 'Menunggu Pembayaran',
                            'description' => 'Reservasi menunggu pembayaran dari customer',
                        ]);

                        Notification::make()
                            ->title('Reservasi berhasil dikonfirmasi, menunggu pembayaran')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Reservation $record) => in_array($record->status, ['pending'])),

                Action::make('batal')
                    ->label('Batal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pemesanan')
                    ->modalDescription('Apakah kamu yakin ingin membatalkan pemesanan ini?')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->action(function (Reservation $record) {
                        $record->update([
                            'status' => 'rejected',
                        ]);

                        $record->statusHistories()->create([
                            'reservation_id' => $record->id,
                            'status' => 'rejected',
                            'title' => 'Reservasi Ditolak',
                            'description' => 'Reservasi telah ditolak oleh admin',
                        ]);

                        Notification::make()
                            ->title('Pemesanan berhasil dibatalkan')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Reservation $record) => in_array($record->status, ['pending', 'waiting_payment'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
