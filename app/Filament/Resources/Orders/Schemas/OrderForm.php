<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: INFORMATIONS GÉNÉRALES ===
                Section::make('Informations Générales')
                    ->description('Informations de base de la commande')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id')
                            ->label('Client')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Sélectionnez un client')
                            ->hint('Le client qui a passé la commande'),

                        Select::make('shop_id')
                            ->label('Boutique')
                            ->relationship('shop', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Sélectionnez une boutique')
                            ->hint('La boutique concernée par cette commande'),

                        Select::make('address_id')
                            ->label('Adresse de livraison')
                            ->relationship('address', 'label', fn ($query) => 
                                $query->whereNotNull('label')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->label . ' - ' . $record->line1 . ', ' . $record->city
                            )
                            ->searchable(['label', 'line1', 'city', 'full_name'])
                            ->preload()
                            ->required()
                            ->placeholder('Sélectionnez une adresse de livraison')
                            ->hint('Adresse où la commande sera livrée'),
                    ])
                    ->columns(2),

                // === SECTION 2: MONTANTS ===
                Section::make('Montants')
                    ->description('Détails financiers de la commande')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Montant total')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->hint('Montant total de la commande'),

                        TextInput::make('shipping_amount')
                            ->label('Frais de livraison')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->hint('Coût de la livraison'),

                        TextInput::make('discount_amount')
                            ->label('Montant de réduction')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->hint('Réduction appliquée à la commande'),
                    ])
                    ->columns(3),

                // === SECTION 3: STATUT ET PAIEMENT ===
                Section::make('Statut et Paiement')
                    ->description('État de la commande et méthode de paiement')
                    ->icon('heroicon-o-credit-card')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->required()
                            ->default('pending')
                            ->options([
                                'pending' => 'En attente',
                                'processing' => 'En traitement',
                                'paid' => 'Payée',
                                'shipped' => 'Expédiée',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée',
                                'refunded' => 'Remboursée',
                            ])
                            ->native(false)
                            ->hint('État actuel de la commande'),

                        Select::make('payment_method')
                            ->label('Méthode de paiement')
                            ->options([
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Portefeuille',
                                'installment' => 'Paiement échelonné',
                            ])
                            ->native(false)
                            ->placeholder('Non spécifié')
                            ->hint('Méthode de paiement utilisée'),

                        Toggle::make('is_installment')
                            ->label('Paiement échelonné')
                            ->default(false)
                            ->required()
                            ->hint('La commande est-elle payée en plusieurs fois ?')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
