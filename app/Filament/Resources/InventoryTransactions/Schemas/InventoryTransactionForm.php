<?php

namespace App\Filament\Resources\InventoryTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InventoryTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('variant_id')
                    ->relationship('variant', 'id')
                    ->required(),
                TextInput::make('change')
                    ->required()
                    ->numeric(),
                TextInput::make('reason')
                    ->required(),
                Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }
}
