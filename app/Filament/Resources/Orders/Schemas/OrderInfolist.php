<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Numéro de commande')
                            ->copyable()
                            ->copyMessage('ID copié !')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('gray')
                            ->columnSpanFull(),

                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->icon(fn (string $state): string => match ($state) {
                                'pending' => 'heroicon-o-clock',
                                'processing' => 'heroicon-o-arrow-path',
                                'paid' => 'heroicon-o-check-circle',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check-badge',
                                'cancelled' => 'heroicon-o-x-circle',
                                'refunded' => 'heroicon-o-arrow-uturn-left',
                                default => 'heroicon-o-question-mark-circle',
                            })
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
                            ->icon(fn (?string $state): string => match ($state) {
                                'mobile_money' => 'heroicon-o-device-phone-mobile',
                                'card' => 'heroicon-o-credit-card',
                                'wallet' => 'heroicon-o-wallet',
                                'installment' => 'heroicon-o-calendar-days',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color('info')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'mobile_money' => 'Mobile Money',
                                'card' => 'Carte bancaire',
                                'wallet' => 'Portefeuille',
                                'installment' => 'Paiement échelonné',
                                default => 'Non spécifié',
                            })
                            ->placeholder('Non spécifié'),

                        IconEntry::make('is_installment')
                            ->label('Paiement échelonné')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ]),

                // === SECTION 2: WORKFLOW DES ÉCHÉANCES (si applicable) ===
                Section::make('Workflow des Échéances')
                    ->description('Visualisation du plan de paiement échelonné sous forme de workflow')
                    ->icon('heroicon-o-arrow-path')
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->is_installment && $record->installmentPlan)
                    ->schema([
                        TextEntry::make('installments_workflow')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $plan = $record->installmentPlan;
                                if (!$plan || $plan->installments->isEmpty()) {
                                    return new HtmlString('<div style="padding: 1rem; text-align: center; color: #6b7280; background: #f9fafb; border-radius: 0.5rem;">
                                                <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p style="font-weight: 500;">Aucune échéance définie</p>
                                            </div>');
                                }
                                return new HtmlString(view('components.installments-workflow', [
                                    'installments' => $plan->installments,
                                    'plan' => $plan,
                                ])->render());
                            })
                            ->columnSpanFull()
                            ->html()
                            ->hiddenLabel(),
                    ]),

                // === SECTION 3: PAIEMENT ÉCHELONNÉ (si applicable) ===
                Section::make('Détails du Paiement Échelonné')
                    ->description('Informations détaillées du plan de paiement échelonné')
                    ->icon('heroicon-o-credit-card')
                    ->collapsible()
                    ->visible(fn ($record) => $record->is_installment && $record->installmentPlan)
                    ->schema([
                        TextEntry::make('installment_plan_summary')
                            ->label('Résumé du Plan')
                            ->getStateUsing(function ($record) {
                                $plan = $record->installmentPlan;
                                if (!$plan) {
                                    return 'Aucun plan de paiement échelonné';
                                }

                                $totalPaid = $record->payments->sum('amount');
                                $remaining = max(0, $plan->total_amount - $totalPaid);
                                $paidCount = $plan->installments->where('status', 'paid')->count();
                                $totalCount = $plan->installments->count();
                                $progress = $totalCount > 0 ? round(($paidCount / $totalCount) * 100) : 0;

                                return sprintf(
                                    'Montant total: %s • Déjà payé: %s • Restant: %s • Échéances: %d/%d (%d%%)',
                                    number_format($plan->total_amount, 0, ',', ' ') . ' XOF',
                                    number_format($totalPaid, 0, ',', ' ') . ' XOF',
                                    number_format($remaining, 0, ',', ' ') . ' XOF',
                                    $paidCount,
                                    $totalCount,
                                    $progress
                                );
                            })
                            ->columnSpanFull()
                            ->weight('bold')
                            ->size('lg')
                            ->color('info')
                            ->icon('heroicon-o-information-circle'),

                        TextEntry::make('installment_total_amount')
                            ->label('Montant total du plan')
                            ->money('XOF')
                            ->getStateUsing(fn ($record) => $record->installmentPlan?->total_amount ?? 0)
                            ->icon('heroicon-o-currency-dollar')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success'),

                        TextEntry::make('installment_paid_amount')
                            ->label('Montant déjà payé')
                            ->money('XOF')
                            ->getStateUsing(fn ($record) => $record->payments->sum('amount'))
                            ->icon('heroicon-o-check-circle')
                            ->weight('bold')
                            ->size('lg')
                            ->color('info'),

                        TextEntry::make('installment_remaining_amount')
                            ->label('Montant restant')
                            ->money('XOF')
                            ->getStateUsing(function ($record) {
                                $plan = $record->installmentPlan;
                                if (!$plan) {
                                    return 0;
                                }
                                $totalPaid = $record->payments->sum('amount');
                                return max(0, $plan->total_amount - $totalPaid);
                            })
                            ->icon('heroicon-o-exclamation-triangle')
                            ->weight('bold')
                            ->size('lg')
                            ->color('warning'),

                        TextEntry::make('installment_progress')
                            ->label('Progression')
                            ->getStateUsing(function ($record) {
                                $plan = $record->installmentPlan;
                                if (!$plan) {
                                    return '0/0 (0%)';
                                }
                                $paidCount = $plan->installments->where('status', 'paid')->count();
                                $totalCount = $plan->installments->count();
                                $progress = $totalCount > 0 ? round(($paidCount / $totalCount) * 100) : 0;
                                return sprintf('%d/%d échéances payées (%d%%)', $paidCount, $totalCount, $progress);
                            })
                            ->badge()
                            ->icon('heroicon-o-chart-bar')
                            ->color(function ($record) {
                                $plan = $record->installmentPlan;
                                if (!$plan) {
                                    return 'gray';
                                }
                                $paidCount = $plan->installments->where('status', 'paid')->count();
                                $totalCount = $plan->installments->count();
                                $progress = $totalCount > 0 ? round(($paidCount / $totalCount) * 100) : 0;
                                return match (true) {
                                    $progress === 100 => 'success',
                                    $progress >= 50 => 'info',
                                    $progress > 0 => 'warning',
                                    default => 'gray',
                                };
                            })
                            ->columnSpanFull(),

                        TextEntry::make('installment_deposit')
                            ->label('Acompte initial')
                            ->money('XOF')
                            ->getStateUsing(fn ($record) => $record->installmentPlan?->deposit_amount ?? 0)
                            ->icon('heroicon-o-banknotes')
                            ->placeholder('0.00')
                            ->color('info'),

                        TextEntry::make('installment_count')
                            ->label('Nombre d\'échéances')
                            ->getStateUsing(fn ($record) => $record->installmentPlan?->number_of_installments ?? 0)
                            ->badge()
                            ->icon('heroicon-o-calendar-days')
                            ->color('gray')
                            ->suffix(' échéance(s)'),

                        TextEntry::make('installment_interval')
                            ->label('Intervalle entre échéances')
                            ->getStateUsing(function ($record) {
                                $interval = $record->installmentPlan?->interval_days ?? 0;
                                if ($interval == 0) {
                                    return null;
                                }
                                return $interval;
                            })
                            ->icon('heroicon-o-clock')
                            ->suffix(fn ($state) => $state ? ' jour' . ($state > 1 ? 's' : '') : '')
                            ->placeholder('Non défini'),
                    ]),

                // === SECTION 4: INFORMATIONS CLIENT ===
                Section::make('Informations Client')
                    ->description('Détails du client et de la livraison')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Client')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-user')
                            ->weight('medium')
                            ->size('base'),

                        TextEntry::make('user.email')
                            ->label('Email')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copié !')
                            ->url(fn ($state) => $state ? 'mailto:' . $state : null)
                            ->openUrlInNewTab(),

                        TextEntry::make('shop.name')
                            ->label('Boutique')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-building-storefront')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('address.label')
                            ->label('Label de l\'adresse')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-map-pin')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('address.full_name')
                            ->label('Nom complet')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-user-circle')
                            ->weight('medium'),

                        TextEntry::make('address.phone')
                            ->label('Téléphone')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->copyMessage('Téléphone copié !')
                            ->url(fn ($state) => $state ? 'tel:' . $state : null),

                        TextEntry::make('address.line1')
                            ->label('Adresse complète')
                            ->placeholder('Non spécifiée')
                            ->icon('heroicon-o-map')
                            ->columnSpanFull()
                            ->weight('medium'),

                        TextEntry::make('address.city')
                            ->label('Ville')
                            ->placeholder('Non spécifiée')
                            ->icon('heroicon-o-building-office-2')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('address.postal_code')
                            ->label('Code postal')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-envelope')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('address.country')
                            ->label('Pays')
                            ->placeholder('Non spécifié')
                            ->icon('heroicon-o-globe-alt')
                            ->badge()
                            ->color('primary'),
                    ]),

                // === SECTION 5: MONTANTS ===
                Section::make('Détails Financiers')
                    ->description('Récapitulatif des montants de la commande')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Montant total')
                            ->money('XOF')
                            ->weight('bold')
                            ->size('xl')
                            ->color('success')
                            ->icon('heroicon-o-banknotes')
                            ->columnSpanFull(),

                        TextEntry::make('shipping_amount')
                            ->label('Frais de livraison')
                            ->money('XOF')
                            ->icon('heroicon-o-truck')
                            ->placeholder('0.00')
                            ->color('gray'),

                        TextEntry::make('discount_amount')
                            ->label('Réduction')
                            ->money('XOF')
                            ->icon('heroicon-o-tag')
                            ->placeholder('0.00')
                            ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                            ->visible(fn ($state) => $state > 0),

                        TextEntry::make('items_count')
                            ->label('Nombre d\'articles')
                            ->getStateUsing(fn ($record) => $record->items->count())
                            ->badge()
                            ->icon('heroicon-o-shopping-bag')
                            ->color('info')
                            ->suffix(' article(s)'),

                        TextEntry::make('payments_sum_amount')
                            ->label('Montant payé')
                            ->money('XOF')
                            ->getStateUsing(fn ($record) => $record->payments->sum('amount'))
                            ->icon('heroicon-o-check-circle')
                            ->placeholder('0.00')
                            ->color('info')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('remaining_amount')
                            ->label('Montant restant')
                            ->money('XOF')
                            ->getStateUsing(function ($record) {
                                $totalPaid = $record->payments->sum('amount');
                                return max(0, $record->total_amount - $totalPaid);
                            })
                            ->icon('heroicon-o-exclamation-triangle')
                            ->placeholder('0.00')
                            ->color('warning')
                            ->weight('bold')
                            ->size('lg')
                            ->visible(fn ($record) => $record->payments->sum('amount') < $record->total_amount),
                    ]),

                // === SECTION 6: DATES ===
                Section::make('Dates')
                    ->description('Horodatage de la commande')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Date de création')
                            ->dateTime('d/m/Y à H:i')
                            ->placeholder('Non définie')
                            ->icon('heroicon-o-calendar')
                            ->badge()
                            ->color('gray')
                            ->tooltip(fn ($state) => $state ? $state->format('d/m/Y à H:i:s') : null),

                        TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y à H:i')
                            ->placeholder('Jamais modifiée')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('gray')
                            ->tooltip(fn ($state) => $state ? $state->format('d/m/Y à H:i:s') : null),
                    ]),
            ]);
    }
}
