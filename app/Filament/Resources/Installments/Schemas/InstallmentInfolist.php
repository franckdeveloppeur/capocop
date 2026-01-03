<?php

namespace App\Filament\Resources\Installments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InstallmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === SECTION 1: INFORMATIONS GÉNÉRALES ===
                Section::make('Informations Générales')
                    ->description('Détails de l\'échéance')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                TextEntry::make('id')
                            ->label(__('ID Échéance'))
                            ->copyable()
                            ->copyMessage(__('ID copié !'))
                            ->badge()
                            ->color('gray')
                            ->icon('heroicon-o-hashtag')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        TextEntry::make('status')
                            ->label(__('Statut'))
                            ->badge()
                            ->icon(fn (string $state): string => match ($state) {
                                'paid' => 'heroicon-o-check-circle',
                                'overdue' => 'heroicon-o-exclamation-triangle',
                                'pending' => 'heroicon-o-clock',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'overdue' => 'danger',
                                'pending' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'paid' => __('Payée'),
                                'overdue' => __('En retard'),
                                'pending' => __('En attente'),
                                default => $state,
                            }),

                TextEntry::make('due_date')
                            ->label(__('Date d\'Échéance'))
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar')
                            ->color(fn ($record) => match ($record->status) {
                                'overdue' => 'danger',
                                'paid' => 'success',
                                default => 'warning',
                            })
                            ->badge(fn ($record) => $record->status === 'overdue')
                            ->weight('bold'),

                TextEntry::make('amount')
                            ->label(__('Montant'))
                            ->money('XAF', locale: 'fr')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->icon('heroicon-o-currency-dollar'),
                    ]),

                // === SECTION 2: PLAN DE PAIEMENT ===
                Section::make('Plan de Paiement')
                    ->description('Informations sur le plan de paiement échelonné')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('plan.id')
                            ->label(__('ID Plan'))
                            ->copyable()
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-hashtag'),

                        TextEntry::make('plan.order_id')
                            ->label(__('Commande Associée'))
                            ->copyable()
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-shopping-bag')
                            ->url(fn ($record) => $record->plan?->order 
                                ? \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record->plan->order])
                                : null)
                            ->openUrlInNewTab(),

                        TextEntry::make('plan.total_amount')
                            ->label(__('Montant Total du Plan'))
                            ->money('XAF', locale: 'fr')
                            ->icon('heroicon-o-banknotes')
                            ->weight('bold')
                            ->color('success'),

                        TextEntry::make('plan.number_of_installments')
                            ->label(__('Nombre d\'Échéances'))
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-calendar-days')
                            ->suffix(' échéance(s)'),

                        TextEntry::make('plan.interval_days')
                            ->label(__('Intervalle'))
                            ->icon('heroicon-o-clock')
                            ->suffix(fn ($state) => $state ? ' jour' . ($state > 1 ? 's' : '') : '')
                            ->placeholder(__('Non défini')),

                        TextEntry::make('plan.status')
                            ->label(__('Statut du Plan'))
                            ->badge()
                            ->icon(fn (string $state): string => match ($state) {
                                'active' => 'heroicon-o-check-circle',
                                'completed' => 'heroicon-o-check-badge',
                                'defaulted' => 'heroicon-o-exclamation-triangle',
                                'cancelled' => 'heroicon-o-x-circle',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'completed' => 'info',
                                'defaulted' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => __('Actif'),
                                'completed' => __('Terminé'),
                                'defaulted' => __('En défaut'),
                                'cancelled' => __('Annulé'),
                                default => $state,
                            }),
                    ]),

                // === SECTION 3: PAIEMENT ===
                Section::make('Paiement Associé')
                    ->description('Détails du paiement lié à cette échéance')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record->payment !== null)
                    ->schema([
                        TextEntry::make('payment.id')
                            ->label(__('ID Paiement'))
                            ->copyable()
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-hashtag'),

                        TextEntry::make('payment.transaction_ref')
                            ->label(__('Référence Transaction'))
                            ->copyable()
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-credit-card')
                            ->placeholder(__('Non spécifiée')),

                        TextEntry::make('payment.amount')
                            ->label(__('Montant Payé'))
                            ->money('XAF', locale: 'fr')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->icon('heroicon-o-currency-dollar'),

                        TextEntry::make('payment.method')
                            ->label(__('Méthode de Paiement'))
                            ->badge()
                            ->icon(fn (?string $state): string => match ($state) {
                                'mobile_money' => 'heroicon-o-device-phone-mobile',
                                'card' => 'heroicon-o-credit-card',
                                'wallet' => 'heroicon-o-wallet',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color('info')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'mobile_money' => __('Mobile Money'),
                                'card' => __('Carte bancaire'),
                                'wallet' => __('Portefeuille'),
                                default => __('Non spécifié'),
                            })
                            ->placeholder(__('Non spécifié')),

                        TextEntry::make('payment.status')
                            ->label(__('Statut du Paiement'))
                            ->badge()
                            ->icon(fn (?string $state): string => match ($state) {
                                'completed' => 'heroicon-o-check-circle',
                                'pending' => 'heroicon-o-clock',
                                'failed' => 'heroicon-o-x-circle',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'completed' => __('Complété'),
                                'pending' => __('En attente'),
                                'failed' => __('Échoué'),
                                default => __('Non spécifié'),
                            })
                            ->placeholder(__('Non spécifié')),

                        TextEntry::make('payment.order.id')
                            ->label(__('Commande'))
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-shopping-bag')
                            ->url(fn ($record) => $record->payment?->order 
                                ? \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $record->payment->order])
                                : null)
                            ->openUrlInNewTab()
                            ->placeholder(__('Non associée')),
                    ]),

                // === SECTION 4: DATES ===
                Section::make('Horodatage')
                    ->description('Dates de création et de modification')
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                TextEntry::make('paid_at')
                            ->label(__('Date de Paiement'))
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-check-circle')
                            ->badge()
                            ->color('success')
                            ->placeholder(__('Non payée'))
                            ->visible(fn ($record) => $record->paid_at !== null),

                TextEntry::make('created_at')
                            ->label(__('Créé le'))
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-calendar')
                            ->badge()
                            ->color('gray')
                            ->placeholder(__('Non définie')),

                TextEntry::make('updated_at')
                            ->label(__('Modifié le'))
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('gray')
                            ->placeholder(__('Jamais modifiée')),
                    ]),
            ]);
    }
}
