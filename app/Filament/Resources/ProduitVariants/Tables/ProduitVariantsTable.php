<?php

namespace App\Filament\Resources\ProduitVariants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProduitVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('product.title')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-shopping-bag')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->product?->title),

                TextColumn::make('price')
                    ->label('Prix')
                    ->money('XAF')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
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

                TextColumn::make('weight')
                    ->label('Poids')
                    ->suffix(' kg')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Produit')
                    ->relationship('product', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('stock')
                    ->label('État du Stock')
                    ->options([
                        'out' => 'Rupture de stock',
                        'low' => 'Stock faible (< 10)',
                        'available' => 'En stock',
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['value'] === 'out',
                                fn ($query) => $query->where('stock', 0)
                            )
                            ->when(
                                $data['value'] === 'low',
                                fn ($query) => $query->where('stock', '>', 0)->where('stock', '<', 10)
                            )
                            ->when(
                                $data['value'] === 'available',
                                fn ($query) => $query->where('stock', '>=', 10)
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
