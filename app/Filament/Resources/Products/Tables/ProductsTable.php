<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('media')
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        try {
                            $first = data_get($record, 'media.0');
                            if ($first) {
                                $path = data_get($first, 'custom_properties.full_path') ?? ('products/' . data_get($first, 'file_name'));
                                    return asset('storage/' . $path);
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }

                        return null;
                    })
                    ->square(),
                TextColumn::make('shop.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('base_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('price_promo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('stock_manage')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
