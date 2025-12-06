<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('shop.name')
                    ->label('Shop')
                    ->placeholder('-'),
                TextEntry::make('address.id')
                    ->label('Address'),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('shipping_amount')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                IconEntry::make('is_installment')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
