<?php

namespace App\Filament\MyAccount\Resources\Orders\Schemas;

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
                // Section Statut de la Commande
                Section::make('Statut de la Commande')
                    ->icon('heroicon-o-shopping-bag')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Numéro de Commande')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->icon('heroicon-o-hashtag')
                            ->size('lg')
                            ->weight('bold'),
                            
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
                            })
                            ->icon(fn (string $state): string => match ($state) {
                                'pending' => 'heroicon-o-clock',
                                'processing' => 'heroicon-o-arrow-path',
                                'paid' => 'heroicon-o-banknotes',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check-circle',
                                'cancelled' => 'heroicon-o-x-circle',
                                'refunded' => 'heroicon-o-arrow-path',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->size('lg')
                            ->weight('bold'),
                    ])
                    ->columns(2),

                // Section Informations de Livraison
                Section::make('Informations de Livraison')
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('address.full_name')
                            ->label('Nom complet')
                            ->icon('heroicon-o-user')
                            ->placeholder('Non renseigné'),
                            
                        TextEntry::make('address.phone')
                            ->label('Téléphone')
                            ->icon('heroicon-o-phone')
                            ->placeholder('Non renseigné')
                            ->copyable(),
                            
                        TextEntry::make('address.line1')
                            ->label('Adresse')
                            ->icon('heroicon-o-building-office')
                            ->placeholder('Non renseigné')
                            ->columnSpanFull(),
                            
                        TextEntry::make('address.line2')
                            ->label('Complément d\'adresse')
                            ->placeholder('Non renseigné')
                            ->columnSpanFull(),
                            
                        TextEntry::make('address.city')
                            ->label('Ville')
                            ->icon('heroicon-o-map')
                            ->placeholder('Non renseigné'),
                            
                        TextEntry::make('address.postal_code')
                            ->label('Code postal')
                            ->icon('heroicon-o-envelope')
                            ->placeholder('Non renseigné'),
                            
                        TextEntry::make('address.country')
                            ->label('Pays')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('Non renseigné'),
                    ])
                    ->columns(2),

                // Section Détails Financiers
                Section::make('Détails Financiers')
                    ->icon('heroicon-o-currency-dollar')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Montant Total')
                            ->money('XAF')
                            ->size('xl')
                            ->weight('bold')
                            ->color('success')
                            ->icon('heroicon-o-banknotes'),
                            
                        TextEntry::make('shipping_amount')
                            ->label('Frais de Livraison')
                            ->money('XAF')
                            ->icon('heroicon-o-truck'),
                            
                        TextEntry::make('discount_amount')
                            ->label('Remise')
                            ->money('XAF')
                            ->icon('heroicon-o-tag')
                            ->color('success')
                            ->visible(fn ($record) => $record->discount_amount > 0),
                            
                        TextEntry::make('payment_method')
                            ->label('Méthode de Paiement')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-credit-card')
                            ->placeholder('Non spécifiée')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'cash' => 'Espèces',
                                'card' => 'Carte bancaire',
                                'mobile_money' => 'Mobile Money',
                                'bank_transfer' => 'Virement bancaire',
                                default => $state ?? 'Non spécifiée',
                            }),
                            
                        IconEntry::make('is_installment')
                            ->label('Paiement Échelonné')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ])
                    ->columns(2),

                // Section Suivi du Paiement Échelonné
                Section::make('Suivi du Paiement Échelonné')
                    ->icon('heroicon-o-calendar-days')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->is_installment && $record->installmentPlan)
                    ->schema([
                        TextEntry::make('installmentPlan.total_amount')
                            ->label('Montant Total du Plan')
                            ->money('XAF')
                            ->size('lg')
                            ->weight('bold')
                            ->icon('heroicon-o-banknotes'),
                            
                        TextEntry::make('installmentPlan.deposit_amount')
                            ->label('Acompte Payé')
                            ->money('XAF')
                            ->icon('heroicon-o-currency-dollar'),
                            
                        TextEntry::make('installmentPlan.number_of_installments')
                            ->label('Nombre d\'Échéances')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-calendar'),
                            
                        TextEntry::make('installmentPlan.status')
                            ->label('Statut du Plan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Actif',
                                'completed' => 'Terminé',
                                'cancelled' => 'Annulé',
                                default => $state,
                            }),
                            
                        TextEntry::make('installmentPlan.installments')
                            ->label('Progression')
                            ->getStateUsing(function ($record) {
                                if (!$record->installmentPlan) {
                                    return null;
                                }
                                $paid = $record->installmentPlan->installments()->where('status', 'paid')->count();
                                $total = $record->installmentPlan->number_of_installments;
                                $remaining = $total - $paid;
                                
                                return "{$paid}/{$total} échéances payées ({$remaining} restantes)";
                            })
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-chart-bar'),
                            
                        TextEntry::make('installmentPlan.installments')
                            ->label('Montant Restant')
                            ->getStateUsing(function ($record) {
                                if (!$record->installmentPlan) {
                                    return null;
                                }
                                $paidAmount = $record->installmentPlan->installments()
                                    ->where('status', 'paid')
                                    ->sum('amount');
                                $remaining = $record->installmentPlan->total_amount - $record->installmentPlan->deposit_amount - $paidAmount;
                                
                                return number_format($remaining, 0, ',', ' ') . ' FCFA';
                            })
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->visible(fn ($record) => $record->installmentPlan && $record->installmentPlan->status === 'active'),
                    ])
                    ->columns(2),

                // Section Articles de la Commande
                Section::make('Articles de la Commande')
                    ->icon('heroicon-o-shopping-cart')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('items')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                try {
                                    $items = $record->items()->with(['product.media', 'variant'])->get();
                                    if ($items->isEmpty()) {
                                        return 'Aucun article';
                                    }
                                    
                                    $html = '<div class="space-y-4">';
                                    foreach ($items as $item) {
                                        $product = $item->product;
                                        $productName = $product->title ?? 'Produit supprimé';
                                        $variantInfo = $item->variant ? " ({$item->variant->sku})" : '';
                                        $quantity = $item->quantity;
                                        $unitPrice = number_format($item->unit_price, 0, ',', ' ') . ' FCFA';
                                        $totalPrice = number_format($item->total_price, 0, ',', ' ') . ' FCFA';
                                        
                                        // Récupérer l'image du produit
                                        $imageUrl = null;
                                        if ($product) {
                                            try {
                                                $firstMedia = $product->media()->first();
                                                if ($firstMedia) {
                                                    $path = $firstMedia->custom_properties['full_path'] ?? ('products/' . $firstMedia->file_name);
                                                    $imageUrl = asset('storage/' . $path);
                                                }
                                            } catch (\Exception $e) {
                                                // Ignorer les erreurs d'image
                                            }
                                        }
                                        
                                        if (!$imageUrl) {
                                            $imageUrl = asset('images/no-image.png');
                                        }
                                        
                                        // Vérifier si un retour existe pour cet article
                                        $hasReturn = \App\Models\OrderReturn::where('order_item_id', $item->id)
                                            ->whereIn('status', ['pending', 'approved', 'processing'])
                                            ->exists();
                                        
                                        $returnBadge = $hasReturn ? '<span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200">Retour en cours</span>' : '';
                                        
                                        $html .= "<div class='flex gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700'>
                                            <div class='flex-shrink-0'>
                                                <img src='{$imageUrl}' alt='{$productName}' class='w-20 h-20 object-cover rounded-lg' onerror=\"this.src='" . asset('images/no-image.png') . "'\" />
                                            </div>
                                            <div class='flex-1 flex justify-between items-start'>
                                                <div class='flex-1'>
                                                    <div class='font-semibold text-gray-900 dark:text-white flex items-center'>
                                                        {$productName}{$variantInfo}
                                                        {$returnBadge}
                                                    </div>
                                                    <div class='text-sm text-gray-600 dark:text-gray-400 mt-1'>
                                                        Quantité: {$quantity} × {$unitPrice}
                                                    </div>
                                                </div>
                                                <div class='font-bold text-gray-900 dark:text-white ml-4'>
                                                    {$totalPrice}
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    $html .= '</div>';
                                    
                                    return new HtmlString($html);
                                } catch (\Exception $e) {
                                    \Log::error('Error loading order items: ' . $e->getMessage());
                                    return new HtmlString('<div class="text-red-600 dark:text-red-400">Erreur lors du chargement des articles</div>');
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                // Section Dates
                Section::make('Dates')
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Date de Création')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('Non renseigné'),
                            
                        TextEntry::make('updated_at')
                            ->label('Dernière Mise à Jour')
                            ->dateTime('d/m/Y à H:i')
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('Non renseigné'),
                    ])
                    ->columns(2),
            ]);
    }
}

