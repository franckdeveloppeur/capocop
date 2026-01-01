<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: INFORMATIONS GÉNÉRALES ===
                Section::make('Informations Générales')
                    ->description('Détails du paiement')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        Select::make('order_id')
                            ->label('Commande')
                            ->relationship('order', 'id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Sélectionnez une commande')
                            ->hint('La commande associée à ce paiement'),

                        TextInput::make('amount')
                            ->label('Montant')
                            ->required()
                            ->numeric()
                            ->prefix('XAF')
                            ->placeholder('0.00')
                            ->hint('Montant du paiement en francs camerounais'),

                        Select::make('method')
                            ->label('Méthode de Paiement')
                            ->required()
                            ->options([
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Capocop Pay',
                            ])
                            ->placeholder('Sélectionnez une méthode')
                            ->hint('Méthode utilisée pour le paiement'),

                        Select::make('status')
                            ->label('Statut')
                            ->required()
                            ->options([
                                'pending' => 'En attente',
                                'success' => 'Réussi',
                                'failed' => 'Échoué',
                                'refunded' => 'Remboursé',
                            ])
                            ->default('pending')
                            ->placeholder('Sélectionnez un statut')
                            ->hint('Statut actuel du paiement'),

                        TextInput::make('transaction_ref')
                            ->label('Référence de Transaction')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('TXN-XXXXXXXXXX')
                            ->hint('Référence unique de la transaction'),
                    ]),

                // === SECTION 2: MÉTADONNÉES ===
                Section::make('Métadonnées')
                    ->description('Informations supplémentaires sur le paiement')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('meta')
                            ->label('Métadonnées (JSON)')
                            ->placeholder('{"key": "value"}')
                            ->hint('Données supplémentaires au format JSON')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

