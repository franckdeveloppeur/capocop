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
            ->columns(3)
            ->components([
                // Informations générales
                TextEntry::make('sku')
                    ->label('SKU')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->placeholder('-'),

                TextEntry::make('product.title')
                    ->label('Produit')
                    ->icon('heroicon-o-shopping-bag')
                    ->placeholder('-')
                    ->columnSpan(2),

                TextEntry::make('id')
                    ->label('ID Variante')
                    ->badge()
                    ->color('gray')
                    ->copyable()
                    ->columnSpanFull(),

                // Tarification et inventaire
                TextEntry::make('price')
                    ->label('Prix')
                    ->money('XAF')
                    ->size('lg')
                    ->weight('bold')
                    ->color('success')
                    ->placeholder('-'),

                TextEntry::make('stock')
                    ->label('Stock Disponible')
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state): string => match (true) {
                        $state === 0 => 'Rupture',
                        $state < 10 => $state . ' (Faible)',
                        default => $state . ' unités',
                    })
                    ->icon(fn ($state): string => match (true) {
                        $state === 0 => 'heroicon-o-x-circle',
                        $state < 10 => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    }),

                TextEntry::make('weight')
                    ->label('Poids')
                    ->suffix(' kg')
                    ->placeholder('Non spécifié')
                    ->icon('heroicon-o-scale'),

                // Attributs
                TextEntry::make('attributes.color')
                    ->label('Couleur')
                    ->placeholder('Non définie')
                    ->badge()
                    ->color(fn ($state) => $state ? 'primary' : 'gray'),

                KeyValueEntry::make('attributes')
                    ->label('Attributs')
                    ->placeholder('Aucun attribut')
                    ->columnSpan(2),

                // Dates
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y à H:i')
                    ->icon('heroicon-o-calendar'),

                TextEntry::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y à H:i')
                    ->icon('heroicon-o-calendar'),
            ]);
    }
}
