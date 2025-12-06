<?php

namespace App\Filament\Resources\ProduitVariants\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ProduitVariantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sku')
                    ->label('SKU')
                    ->content(fn ($record) => $record->sku ?? '-'),

                TextEntry::make('product')
                    ->label('Produit')
                    ->content(fn ($record) => $record->product?->title ?? '-'),

                TextEntry::make('price')
                    ->label('Prix')
                    ->content(fn ($record) => isset($record->price) ? "$" . number_format($record->price, 2) : '-'),

                TextEntry::make('stock')
                    ->label('Stock')
                    ->content(fn ($record) => $record->stock ?? 0),

                KeyValueEntry::make('attributes')
                    ->label('Attributs')
                    ->content(fn ($record) => $record->attributes ?? []),
            ]);
    }
}
