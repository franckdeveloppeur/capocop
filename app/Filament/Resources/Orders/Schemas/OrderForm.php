<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('shop_id')
                    ->relationship('shop', 'name'),
                Select::make('address_id')
                    ->relationship('address', 'id')
                    ->required(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('payment_method'),
                Toggle::make('is_installment')
                    ->required(),
            ]);
    }
}
