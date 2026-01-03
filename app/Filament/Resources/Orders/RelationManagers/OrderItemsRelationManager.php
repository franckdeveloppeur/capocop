<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles de la Commande';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('product.slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('quantity')
                    ->label('Quantité')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('unit_price')
                    ->label('Prix Unitaire')
                    ->money('XAF', locale: 'fr')
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Prix Total')
                    ->money('XAF', locale: 'fr')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('variant.name')
                    ->label('Variante')
                    ->placeholder('Aucune variante')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucun article')
            ->emptyStateDescription('Cette commande ne contient aucun article.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}

