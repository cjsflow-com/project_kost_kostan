<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('room_id')
                    ->required()
                    ->numeric(),
                TextInput::make('reservation_code')
                    ->default(null),
                TextInput::make('customer_ktp_card')
                    ->default(null),
                DatePicker::make('start_date')
                    ->required(),
                TextInput::make('duration_month')
                    ->required()
                    ->numeric(),
                TextInput::make('room_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('admin_fee')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('deposit')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'waiting_payment' => 'Waiting payment',
            'payment_uploaded' => 'Payment uploaded',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('payment_due_at'),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('rejected_at'),
                DateTimePicker::make('cancelled_at'),
                DateTimePicker::make('expired_at'),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('customer_id')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
