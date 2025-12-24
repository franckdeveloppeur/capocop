<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->description('Détails de base du paiement')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('order_id')
                            ->label('Commande')
                            ->relationship('order', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                'Commande #' . substr($record->id, 0, 8) . ' - ' . 
                                ($record->user?->name ?? 'Client inconnu') . ' - ' . 
                                number_format($record->total_amount, 0, ',', ' ') . ' XOF'
                            )
                            ->searchable(['id'])
                            ->preload()
                            ->required()
                            ->placeholder('Sélectionnez une commande')
                            ->hint('La commande associée à ce paiement'),

                        TextInput::make('amount')
                            ->label('Montant')
                            ->required()
                            ->numeric()
                            ->prefix('XOF')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->hint('Montant du paiement'),

                        Select::make('method')
                            ->label('Méthode de paiement')
                            ->required()
                            ->options([
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Portefeuille',
                            ])
                            ->native(false)
                            ->placeholder('Sélectionnez une méthode')
                            ->hint('Méthode utilisée pour le paiement'),

                        Select::make('status')
                            ->label('Statut')
                            ->required()
                            ->default('pending')
                            ->options([
                                'pending' => 'En attente',
                                'success' => 'Réussi',
                                'failed' => 'Échoué',
                                'refunded' => 'Remboursé',
                            ])
                            ->native(false)
                            ->hint('État actuel du paiement'),
                    ])
                    ->columns(2),

                // === SECTION 2: RÉFÉRENCE ET MÉTADONNÉES ===
                Section::make('Référence et Métadonnées')
                    ->description('Informations techniques du paiement')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('transaction_ref')
                            ->label('Référence de transaction')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ex: TXN-2024-001234')
                            ->hint('Identifiant unique de la transaction')
                            ->columnSpanFull(),

                        Textarea::make('meta')
                            ->label('Métadonnées')
                            ->rows(4)
                            ->placeholder('Informations supplémentaires au format JSON')
                            ->hint('Données supplémentaires sur le paiement (optionnel)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

