<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reservation_id')
                    ->numeric(),
                TextEntry::make('payment_method_id')
                    ->numeric(),
                TextEntry::make('payment_proof')
                    ->placeholder('-'),
                TextEntry::make('payment_code')
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('uploaded_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('rejected_at')
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
            ]);
    }
}
