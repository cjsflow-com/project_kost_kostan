<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('room_id')
                    ->numeric(),
                TextEntry::make('reservation_code')
                    ->placeholder('-'),
                TextEntry::make('customer_ktp_card')
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->date(),
                TextEntry::make('duration_month')
                    ->numeric(),
                TextEntry::make('room_price')
                    ->money(),
                TextEntry::make('admin_fee')
                    ->numeric(),
                TextEntry::make('deposit')
                    ->numeric(),
                TextEntry::make('total_price')
                    ->money(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('payment_due_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('approved_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('rejected_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('expired_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('customer_id')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
