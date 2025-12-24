<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: STATUT DU PAIEMENT ===
                Section::make('Statut du Paiement')
                    ->description('Informations sur l\'état actuel du paiement')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID du paiement')
                            ->copyable()
                            ->copyMessage('ID copié !')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'success' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'En attente',
                                'success' => 'Réussi',
                                'failed' => 'Échoué',
                                'refunded' => 'Remboursé',
                                default => $state,
                            }),

                        TextEntry::make('method')
                            ->label('Méthode de paiement')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Portefeuille',
                                default => 'Non spécifié',
                            }),
                    ])
                    ->columns(2),

                // === SECTION 2: INFORMATIONS COMMANDE ===
                Section::make('Informations Commande')
                    ->description('Détails de la commande associée')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('order.id')
                            ->label('Numéro de commande')
                            ->copyable()
                            ->weight('bold')
                            ->icon('heroicon-o-shopping-bag'),

                        TextEntry::make('order.user.name')
                            ->label('Client')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('order.user.email')
                            ->label('Email client')
                            ->placeholder('-')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('order.total_amount')
                            ->label('Montant total de la commande')
                            ->money('XOF')
                            ->weight('bold')
                            ->color('success'),

                        TextEntry::make('order.status')
                            ->label('Statut de la commande')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'paid' => 'success',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                'refunded' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'En attente',
                                'processing' => 'En traitement',
                                'paid' => 'Payée',
                                'shipped' => 'Expédiée',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée',
                                'refunded' => 'Remboursée',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                // === SECTION 3: DÉTAILS FINANCIERS ===
                Section::make('Détails Financiers')
                    ->description('Montants et références')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Montant payé')
                            ->money('XOF')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success'),

                        TextEntry::make('transaction_ref')
                            ->label('Référence de transaction')
                            ->copyable()
                            ->copyMessage('Référence copiée !')
                            ->weight('bold')
                            ->icon('heroicon-o-document-duplicate')
                            ->columnSpanFull(),

                        TextEntry::make('meta')
                            ->label('Métadonnées')
                            ->formatStateUsing(fn ($state) => 
                                is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($state ?? '-')
                            )
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->copyable()
                            ->extraAttributes(['class' => 'font-mono text-xs']),
                    ]),

                // === SECTION 4: DATES ===
                Section::make('Dates')
                    ->description('Horodatage du paiement')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Date de création')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
            ]);
    }
}

