<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('code'),
                TextEntry::make('type'),
                TextEntry::make('value')
                    ->numeric(),
                TextEntry::make('min_order_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('usage_limit')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('used_count')
                    ->numeric(),
                TextEntry::make('valid_from')
                    ->dateTime(),
                TextEntry::make('valid_until')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
