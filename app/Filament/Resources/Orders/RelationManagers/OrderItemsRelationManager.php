<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\OrderItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'product.title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Produit')
                    ->relationship('product', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = \App\Models\Product::find($state);
                            if ($product) {
                                $set('unit_price', $product->getPrice());
                            }
                        }
                    }),

                Select::make('variant_id')
                    ->label('Variante')
                    ->relationship('variant', 'sku', fn ($query, $get) => 
                        $query->where('product_id', $get('product_id'))
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        $record->sku . ' - ' . json_encode($record->attributes ?? [], JSON_UNESCAPED_UNICODE)
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->visible(fn ($get) => $get('product_id') !== null),

                TextInput::make('quantity')
                    ->label('Quantité')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $unitPrice = $get('unit_price') ?? 0;
                        $set('total_price', $unitPrice * $state);
                    }),

                TextInput::make('unit_price')
                    ->label('Prix unitaire')
                    ->required()
                    ->numeric()
                    ->prefix('XOF')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $quantity = $get('quantity') ?? 1;
                        $set('total_price', $state * $quantity);
                    }),

                TextInput::make('total_price')
                    ->label('Prix total')
                    ->required()
                    ->numeric()
                    ->prefix('XOF')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')
                    ->label('Produit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('variant.sku')
                    ->label('Variante')
                    ->formatStateUsing(fn ($state, $record) => 
                        $state ? ($state . ' - ' . json_encode($record->variant?->attributes ?? [], JSON_UNESCAPED_UNICODE)) : '-'
                    )
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Quantité')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('unit_price')
                    ->label('Prix unitaire')
                    ->money('XOF')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('total_price')
                    ->label('Prix total')
                    ->money('XOF')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un article'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('Aucun article dans cette commande')
            ->emptyStateDescription('Ajoutez des articles à cette commande en cliquant sur le bouton ci-dessus.')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}

