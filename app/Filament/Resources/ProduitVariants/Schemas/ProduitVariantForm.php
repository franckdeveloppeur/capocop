<?php

namespace App\Filament\Resources\ProduitVariants\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;

class ProduitVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Général')
                    ->description('Informations de base de la variante')
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'title')
                            ->label('Produit')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ex: PROD-RED-L'),
                    ])
                    ->collapsible(),

                Section::make('Tarification & Poids')
                    ->schema([
                        TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->required(),

                        TextInput::make('weight')
                            ->label('Poids (kg)')
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('0.00'),
                    ])
                    ->collapsible(),

                Section::make('Inventaire')
                    ->schema([
                        TextInput::make('stock')
                            ->label('Quantité en stock')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])
                    ->collapsible(),

                Section::make('Attributs')
                    ->description('Attributs dynamiques (couleur, taille...)')
                    ->schema([
                        // Color picker stored in attributes.color
                        ColorPicker::make('attributes.color')
                            ->label('Couleur')
                            ->hint('Sélectionnez la couleur principale de la variante')
                            ->columnSpan(1),

                        KeyValue::make('attributes')
                            ->label('Attributs')
                            ->keyLabel('Nom')
                            ->valueLabel('Valeur')
                            ->keyPlaceholder('taille')
                            ->valuePlaceholder('L')
                            ->columnSpanFull(),

                        FileUpload::make('images')
                            ->label('Images')
                            ->multiple()
                            ->image()
                            ->directory('variants')
                            ->maxSize(2048)
                            ->reorderable()
                            ->hint('Images optionnelles pour la variante'),
                    ])
                    ->collapsible(),
            ]);
    }
}
