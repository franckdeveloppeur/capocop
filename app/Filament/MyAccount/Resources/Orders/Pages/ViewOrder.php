<?php

namespace App\Filament\MyAccount\Resources\Orders\Pages;

use App\Filament\MyAccount\Resources\Orders\OrderResource;
use App\Models\OrderReturn;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadInvoice')
                ->label('Télécharger la facture')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('orders.invoice', $this->record))
                ->openUrlInNewTab(),
            Action::make('returnProduct')
                ->label('Retourner un produit')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['delivered', 'shipped', 'paid']))
                ->form([
                    \Filament\Forms\Components\Select::make('order_item_id')
                        ->label('Article à retourner')
                        ->options(function () {
                            try {
                                $items = $this->record->items()->with(['product', 'variant'])->get();
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
                        ->helperText('Sélectionnez l\'article que vous souhaitez retourner'),
                        
                    \Filament\Forms\Components\Select::make('reason')
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
                        
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Description détaillée')
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('Décrivez en détail la raison de votre retour (max 500 caractères)')
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        // Vérifier que l'utilisateur peut retourner ce produit
                        $orderItem = $this->record->items()->find($data['order_item_id']);
                        
                        if (!$orderItem) {
                            throw new \Exception('Article introuvable dans cette commande.');
                        }
                        
                        // Vérifier qu'un retour n'existe pas déjà
                        $existingReturn = OrderReturn::where('order_item_id', $data['order_item_id'])
                            ->whereIn('status', ['pending', 'approved', 'processing'])
                            ->first();
                            
                        if ($existingReturn) {
                            throw new \Exception('Un retour est déjà en cours pour cet article.');
                        }
                        
                        // Vérifier le statut de la commande
                        if (!in_array($this->record->status, ['delivered', 'shipped', 'paid'])) {
                            throw new \Exception('Les retours ne sont possibles que pour les commandes livrées, expédiées ou payées.');
                        }
                        
                        // Calculer le montant de remboursement
                        $refundAmount = $orderItem->total_price;
                        
                        // Créer le retour
                        OrderReturn::create([
                            'order_id' => $this->record->id,
                            'order_item_id' => $data['order_item_id'],
                            'user_id' => auth()->id(),
                            'reason' => $data['reason'],
                            'description' => $data['description'],
                            'status' => 'pending',
                            'refund_amount' => $refundAmount,
                        ]);
                        
                        Notification::make()
                            ->title('Demande de retour créée')
                            ->body('Votre demande de retour a été enregistrée et sera traitée par notre équipe.')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        \Log::error('Error creating order return: ' . $e->getMessage());
                        
                        Notification::make()
                            ->title('Erreur')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                            
                        throw $e;
                    }
                }),
            EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // S'assurer que l'utilisateur ne peut voir que ses propres commandes
        if ($this->record->user_id !== auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }
        
        // Charger les items avec leurs relations pour l'affichage
        $this->record->load(['items.product.media', 'items.variant', 'returns']);
        
        return $data;
    }
}

