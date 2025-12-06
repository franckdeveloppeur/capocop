<?php

namespace App\Filament\Resources\ProduitVariants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
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
                    ->sortable(),

                TextColumn::make('product.title')
                    ->label('Produit')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Prix')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable(),

                BadgeColumn::make('stock')
                    ->label('État')
                    ->enum([
                        0 => 'Rupture',
                    ])
                    ->colors([
                        'danger' => fn($state): bool => $state === 0,
                        'success' => fn($state): bool => $state > 0,
                    ])
                    ->hidden(fn() => false),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Produit')
                    ->relationship('product', 'title'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
