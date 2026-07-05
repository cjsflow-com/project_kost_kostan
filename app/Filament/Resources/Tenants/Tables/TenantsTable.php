<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Models\Tenant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.room_number')
                    ->label('Nomor Kamar')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('room.title')
                    ->label('Nama Kamar')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->searchable()
                    ->label('Penghuni Aktif'),
                TextColumn::make('customer.reservations.reservation_code')
                    ->searchable()
                    ->placeholder('-')
                    ->label('Kode Reservasi'),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d M Y'),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d M Y'),
                TextColumn::make('status_id')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => \App\Models\Tenant::STATUS[$state] ?? 'Unknown')
                    ->color(fn ($state): string => match ((int) $state) {
                        Tenant::STATUS_ACTIVE => 'success',
                        Tenant::STATUS_INACTIVE => 'gray',
                        default => 'warning',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                //  EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
