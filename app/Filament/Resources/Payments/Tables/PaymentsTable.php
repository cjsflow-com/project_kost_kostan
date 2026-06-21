<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use App\Models\Payment;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation.reservation_code')
                    ->label('Kode Reservasi')
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label('ID Metode Pembayaran')
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Apakah kamu yakin ingin mengkonfirmasi pembayaran ini?')
                    ->modalSubmitActionLabel('Ya, Konfirmasi')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                        ]);

                        $record->reservation()->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);

                        $record->reservation->room()->update([
                            'status_id' => 2,
                        ]);

                        $record->reservation->statusHistories()->create([
                            'reservation_id' => $record->reservation->id,
                            'status' => 'approved',
                            'title' => 'Disetujui',
                            'description' => 'Pembayaran telah disetujui, reservasi disetujui',
                        ]);

                        Notification::make()
                            ->title('Pembayaran berhasil dikonfirmasi')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Payment $record) => ($record->status === 'uploaded' || $record->paymentMethod?->type === 'cash') && $record->reservation?->status !== 'approved'),

                Action::make('batal')
                    ->label('Batal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pembayaran')
                    ->modalDescription('Apakah kamu yakin ingin membatalkan pembayaran ini?')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => 'rejected',
                            'rejected_at' => now(),
                        ]);

                        $record->reservation()->update([
                            'status' => 'rejected',
                            'cancelled_at' => now(),
                        ]);

                        $record->reservation->statusHistories()->create([
                            'reservation_id' => $record->reservation->id,
                            'status' => 'rejected',
                            'title' => 'Ditolak',
                            'description' => 'Pembayaran telah ditolak, reservasi dibatalkan',
                        ]);

                        Notification::make()
                            ->title('Pembayaran berhasil dibatalkan')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Payment $record) => ($record->status === 'uploaded' || $record->paymentMethod?->type === 'cash') && $record->reservation?->status !== 'approved'),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
