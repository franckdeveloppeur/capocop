<?php

namespace App\Filament\MyAccount\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderReturn;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();
        
        return $schema
            ->components([
                // Section Informations Générales
                Section::make('Informations Générales')
                    ->icon('heroicon-o-information-circle')
                    ->description('Informations de base de la commande')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('address_id')
                                    ->label('Adresse de Livraison')
                                    ->relationship('address', 'label', fn ($query) => $query->where('user_id', $user->id))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Sélectionnez une adresse')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->label . ' - ' . $record->city)
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->helperText(fn ($record) => $record && $record->exists ? 'L\'adresse ne peut pas être modifiée après la création de la commande' : null),
                                    
                                Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'pending' => 'En attente',
                                        'processing' => 'En traitement',
                                        'paid' => 'Payée',
                                        'shipped' => 'Expédiée',
                                        'delivered' => 'Livrée',
                                        'cancelled' => 'Annulée',
                                        'refunded' => 'Remboursée',
                                    ])
                                    ->required()
                                    ->default('pending')
                                    ->native(false)
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->helperText(fn ($record) => $record && $record->exists ? 'Le statut est géré par l\'administration' : null),
                            ]),
                    ]),

                // Section Détails Financiers
                Section::make('Détails Financiers')
                    ->icon('heroicon-o-currency-dollar')
                    ->description('Montants et méthodes de paiement')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_amount')
                                    ->label('Montant Total')
                                    ->required()
                                    ->numeric()
                                    ->prefix('FCFA')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->helperText(fn ($record) => $record && $record->exists ? 'Le montant total est calculé automatiquement' : null),
                                    
                                TextInput::make('shipping_amount')
                                    ->label('Frais de Livraison')
                                    ->required()
                                    ->numeric()
                                    ->prefix('FCFA')
                                    ->default(0)
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->helperText(fn ($record) => $record && $record->exists ? 'Les frais de livraison sont définis par le système' : null),
                                    
                                TextInput::make('discount_amount')
                                    ->label('Remise')
                                    ->numeric()
                                    ->prefix('FCFA')
                                    ->default(0)
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->helperText(fn ($record) => $record && $record->exists ? 'Les remises sont appliquées automatiquement' : null),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                Select::make('payment_method')
                                    ->label('Méthode de Paiement')
                                    ->options([
                                        'cash' => 'Espèces',
                                        'card' => 'Carte bancaire',
                                        'mobile_money' => 'Mobile Money',
                                        'bank_transfer' => 'Virement bancaire',
                                    ])
                                    ->placeholder('Sélectionnez une méthode')
                                    ->native(false)
                                    ->disabled(fn ($record) => $record !== null && $record->exists && $record->payment_method !== null)
                                    ->helperText(fn ($record) => $record && $record->exists && $record->payment_method ? 'La méthode de paiement ne peut pas être modifiée après définition' : null),
                                    
                                Toggle::make('is_installment')
                                    ->label('Paiement Échelonné')
                                    ->default(false)
                                    ->helperText('Activer le paiement en plusieurs fois')
                                    ->live()
                                    ->disabled(fn ($record) => $record !== null && $record->exists)
                                    ->dehydrated(fn ($record) => $record === null || !$record->exists ? true : $record->is_installment),
                            ]),
                    ]),

                // Section Retour de Produits
                Section::make('Retour de Produits')
                    ->icon('heroicon-o-arrow-path')
                    ->description('Demander le retour d\'un ou plusieurs produits de cette commande')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record && in_array($record->status, ['delivered', 'shipped', 'paid']))
                    ->schema([
                        Repeater::make('returns')
                            ->label('Articles à retourner')
                            ->schema([
                                Select::make('order_item_id')
                                    ->label('Article')
                                    ->options(function ($record) {
                                        if (!$record) {
                                            return [];
                                        }
                                        
                                        try {
                                            $items = $record->items()->with('product')->get();
                                            $options = [];
                                            
                                            foreach ($items as $item) {
                                                $productName = $item->product->title ?? 'Produit supprimé';
                                                $variantInfo = $item->variant ? " ({$item->variant->sku})" : '';
                                                
                                                // Vérifier si un retour existe déjà
                                                $hasReturn = OrderReturn::where('order_item_id', $item->id)
                                                    ->whereIn('status', ['pending', 'approved', 'processing'])
                                                    ->exists();
                                                
                                                if (!$hasReturn) {
                                                    $options[$item->id] = "{$productName}{$variantInfo} - Quantité: {$item->quantity}";
                                                }
                                            }
                                            
                                            return $options;
                                        } catch (\Exception $e) {
                                            \Log::error('Error loading order items for return: ' . $e->getMessage());
                                            return [];
                                        }
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->disabled(fn ($record) => !$record)
                                    ->helperText('Sélectionnez l\'article que vous souhaitez retourner'),
                                    
                                Select::make('reason')
                                    ->label('Raison du retour')
                                    ->options([
                                        'defective' => 'Produit défectueux',
                                        'wrong_item' => 'Mauvais article reçu',
                                        'not_as_described' => 'Ne correspond pas à la description',
                                        'damaged' => 'Article endommagé',
                                        'size_issue' => 'Problème de taille',
                                        'color_issue' => 'Problème de couleur',
                                        'other' => 'Autre raison',
                                    ])
                                    ->required()
                                    ->native(false),
                                    
                                Textarea::make('description')
                                    ->label('Description détaillée')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Décrivez en détail la raison de votre retour (max 500 caractères)')
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un article à retourner')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['order_item_id'] ?? null
                                    ? \App\Models\OrderItem::with('product')->find($state['order_item_id'])?->product->title ?? 'Article'
                                    : 'Nouvel article'
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
