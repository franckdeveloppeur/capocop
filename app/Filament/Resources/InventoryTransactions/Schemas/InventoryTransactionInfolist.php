<?php

namespace App\Filament\Resources\InventoryTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('variant.id')
                    ->label('Variant'),
                TextEntry::make('change')
                    ->numeric(),
                TextEntry::make('reason'),
                TextEntry::make('meta')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
