<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
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
                        TextEntry::make('id')
                            ->label('ID')
                            ->copyable()
                            ->placeholder('N/A'),

                        TextEntry::make('order.id')
                            ->label('Commande')
                            ->copyable()
                            ->placeholder('N/A')
                            ->url(fn ($record) => $record->order 
                                ? \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record->order])
                                : null),

                        TextEntry::make('amount')
                            ->label('Montant')
                            ->money('XAF', locale: 'fr')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->placeholder('0.00 XAF'),

                        TextEntry::make('method')
                            ->label('Méthode de Paiement')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Capocop Pay',
                                default => $state,
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'wallet' => 'success',
                                'card' => 'info',
                                'mobile_money' => 'warning',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label('Statut')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'En attente',
                                'success' => 'Réussi',
                                'failed' => 'Échoué',
                                'refunded' => 'Remboursé',
                                default => $state,
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'success' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('transaction_ref')
                            ->label('Référence de Transaction')
                            ->copyable()
                            ->placeholder('N/A'),
                    ]),

                // === SECTION 2: INFORMATIONS COMMANDE ===
                Section::make('Informations Commande')
                    ->description('Détails de la commande associée')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('order.user.name')
                            ->label('Client')
                            ->placeholder('N/A'),

                        TextEntry::make('order.user.email')
                            ->label('Email Client')
                            ->placeholder('N/A'),

                        TextEntry::make('order.total_amount')
                            ->label('Montant Total Commande')
                            ->money('XAF', locale: 'fr')
                            ->placeholder('N/A'),

                        TextEntry::make('order.status')
                            ->label('Statut Commande')
                            ->badge()
                            ->placeholder('N/A'),
                    ]),

                // === SECTION 3: MÉTADONNÉES ===
                Section::make('Métadonnées')
                    ->description('Informations supplémentaires')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('meta')
                            ->label('Métadonnées')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null)
                            ->placeholder('Aucune métadonnée')
                            ->columnSpanFull()
                            ->copyable(),
                    ]),

                // === SECTION 4: DATES ===
                Section::make('Dates')
                    ->description('Horodatage')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('N/A'),

                        TextEntry::make('updated_at')
                            ->label('Modifié le')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('N/A'),
                    ]),
            ]);
    }
}

