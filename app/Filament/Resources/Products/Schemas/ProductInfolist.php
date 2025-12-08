<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                    ImageEntry::make('media')
                        ->label('Image')
                        ->disk('public')
                        ->getStateUsing(fn ($record) =>
                            (function ($record) {
                                try {
                                    $first = data_get($record, 'media.0');
                                    if ($first) {
                                        return data_get($first, 'custom_properties.full_path') ?? ('products/' . data_get($first, 'file_name'));
                                    }
                                } catch (\Throwable $e) {
                                    // ignore
                                }

                                return null;
                            })($record)
                        )
                        ->columnSpanFull(),
                TextEntry::make('shop.name')
                    ->label('Shop'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('base_price')
                    ->money(),
                TextEntry::make('price_promo')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status'),
                IconEntry::make('stock_manage')
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
