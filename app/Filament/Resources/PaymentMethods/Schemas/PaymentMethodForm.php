<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Metode Pembayaran')
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options(['bank_transfer' => 'Bank transfer', 'e_wallet' => 'E wallet', 'cash' => 'Cash'])
                    ->required(),
                TextInput::make('provider')
                    ->label('Penyedia')
                    ->default(null),
                TextInput::make('account_number')
                    ->label('Nomor Rekening')
                    ->default(null),
                TextInput::make('account_name')
                    ->label('Nama Rekening')
                    ->default(null),
                Toggle::make('is_active')
                    ->label('Status')
                    ->required(),
            ]);
    }
}
