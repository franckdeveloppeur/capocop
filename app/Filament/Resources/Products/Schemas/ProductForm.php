<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: INFORMATIONS GÉNÉRALES ===
                Section::make('Informations Générales')
                    ->description('Informations de base du produit')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Select::make('shop_id')
                            ->label('Boutique')
                            ->relationship('shop', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Sélectionnez une boutique'),

                        TextInput::make('title')
                            ->label('Titre du Produit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Chemise Classique Bleu')
                            ->hint('Le titre visible sur le site')
                            ->afterStateUpdated(fn ($state, \Closure $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Auto-généré à partir du titre')
                            ->hint('Généré automatiquement'),

                        RichEditor::make('description')
                            ->label('Description Détaillée')
                            ->placeholder('Décrivez le produit en détail, ses caractéristiques, avantages...')
                            ->columnSpanFull(),
                    ]),

                // === SECTION 2: TARIFICATION ===
                Section::make('Tarification')
                    ->description('Gestion des prix et promotions')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        TextInput::make('base_price')
                            ->label('Prix de Base')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->hint('Prix de vente standard'),

                        TextInput::make('price_promo')
                            ->label('Prix Promo')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->placeholder('Optionnel')
                            ->hint('Prix réduit pendant la promotion'),
                    ]),

                // === SECTION 3: CLASSIFICATION ===
                Section::make('Classification')
                    ->description('Catégorisation et étiquetage du produit')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->schema([
                        Select::make('categories')
                            ->label('Catégories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->placeholder('Sélectionnez les catégories')
                            ->hint('Sélectionnez une ou plusieurs catégories'),

                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->placeholder('Sélectionnez les tags')
                            ->hint('Ajoutez des tags pour une meilleure recherche'),
                    ]),

                // === SECTION 4: CONFIGURATION ===
                Section::make('Configuration')
                    ->description('Statut et gestion du stock')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->schema([
                        Select::make('status')
                            ->label('Statut de Publication')
                            ->options([
                                'draft' => 'Brouillon',
                                'published' => 'Publié',
                                'archived' => 'Archivé',
                            ])
                            ->required()
                            ->default('draft')
                            ->hint('Contrôlez la visibilité du produit'),

                        Toggle::make('stock_manage')
                            ->label('Gérer le Stock')
                            ->inline(false)
                            ->hint('Activez pour tracker l\'inventaire'),
                    ]),

                // === SECTION 5: IMAGES ET MÉDIA ===
                Section::make('Images et Média')
                    ->description('Téléchargez les images du produit')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('product_images')
                            ->label('Images du Produit')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->directory('products')
                            ->maxSize(2048)
                            ->reorderable()
                            ->appendFiles()
                            ->hint('Téléchargez les images du produit. Max 2MB par image.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
