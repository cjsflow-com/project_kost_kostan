<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Tenant;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.name')
                    ->label('Nama Penghuni'),
                TextEntry::make('room.number')
                    ->label('Nomor Kamar'),
                TextEntry::make('start_date')
                    ->label('Tanggal Masuk')
                    ->date('d M Y'),
                TextEntry::make('reservation.reservation_code')
                    ->label('Kode Reservasi')
                    ->formatStateUsing(fn ($state): string => 'Reservasi #' . $state),
                TextEntry::make('end_date')
                    ->label('Tanggal Keluar')
                    ->date('d M Y')
                    ->placeholder('-'),
                TextEntry::make('status_id')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Tenant::STATUS[$state] ?? 'Unknown')
                    ->color(fn ($state): string => match ((int) $state) {
                        Tenant::STATUS_ACTIVE => 'success',
                        Tenant::STATUS_INACTIVE => 'gray',
                        default => 'warning',
                    }),
            ]);
    }
}
