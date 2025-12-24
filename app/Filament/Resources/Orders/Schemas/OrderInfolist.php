<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: STATUT DE LA COMMANDE ===
                Section::make('Statut de la Commande')
                    ->description('Informations sur l\'état actuel de la commande')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Numéro de commande')
                            ->copyable()
                            ->copyMessage('ID copié !')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('status')
                            ->label('Statut')
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

                        TextEntry::make('payment_method')
                            ->label('Méthode de paiement')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Portefeuille',
                                'installment' => 'Paiement échelonné',
                                default => 'Non spécifié',
                            })
                            ->placeholder('-'),

                        IconEntry::make('is_installment')
                            ->label('Paiement échelonné')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),

                // === SECTION 2: INFORMATIONS CLIENT ===
                Section::make('Informations Client')
                    ->description('Détails du client et de la livraison')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Client')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('user.email')
                            ->label('Email')
                            ->placeholder('-')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('shop.name')
                            ->label('Boutique')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-building-storefront'),

                        TextEntry::make('address.label')
                            ->label('Label de l\'adresse')
                            ->placeholder('-')
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('address.full_name')
                            ->label('Nom complet')
                            ->placeholder('-'),

                        TextEntry::make('address.phone')
                            ->label('Téléphone')
                            ->placeholder('-')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('address.line1')
                            ->label('Adresse')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('address.city')
                            ->label('Ville')
                            ->placeholder('-'),

                        TextEntry::make('address.postal_code')
                            ->label('Code postal')
                            ->placeholder('-'),

                        TextEntry::make('address.country')
                            ->label('Pays')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                // === SECTION 3: MONTANTS ===
                Section::make('Détails Financiers')
                    ->description('Récapitulatif des montants de la commande')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Montant total')
                            ->money('XOF')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success'),

                        TextEntry::make('shipping_amount')
                            ->label('Frais de livraison')
                            ->money('XOF')
                            ->placeholder('0.00'),

                        TextEntry::make('discount_amount')
                            ->label('Réduction')
                            ->money('XOF')
                            ->placeholder('0.00')
                            ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                        TextEntry::make('items_count')
                            ->label('Nombre d\'articles')
                            ->getStateUsing(fn ($record) => $record->items->count())
                            ->badge()
                            ->color('info'),

                        TextEntry::make('payments_sum_amount')
                            ->label('Montant payé')
                            ->money('XOF')
                            ->getStateUsing(fn ($record) => $record->payments->sum('amount'))
                            ->placeholder('0.00')
                            ->color('info'),
                    ])
                    ->columns(2),

                // === SECTION 4: DATES ===
                Section::make('Dates')
                    ->description('Horodatage de la commande')
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
