<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reservation_id')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method_id')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_proof')
                    ->default(null),
                TextInput::make('payment_code')
                    ->default(null),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'uploaded' => 'Uploaded',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
        ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('paid_at'),
                DateTimePicker::make('uploaded_at'),
                DateTimePicker::make('verified_at'),
                DateTimePicker::make('rejected_at'),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
