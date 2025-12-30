<?php

namespace App\Filament\MyAccount\Resources\Orders\Pages;

use App\Filament\MyAccount\Resources\Orders\OrderResource;
use App\Models\OrderReturn;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // S'assurer que l'utilisateur ne peut modifier que ses propres commandes
        if ($this->record->user_id !== auth()->id()) {
            abort(403, 'Vous n\'avez pas accès à cette commande.');
        }
        
        // Charger les items avec leurs relations pour le formulaire
        $this->record->load(['items.product.media', 'items.variant']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Empêcher la modification des champs en lecture seule
        // Ces champs sont désactivés dans le formulaire mais doivent être protégés côté serveur
        $protectedFields = ['total_amount', 'shipping_amount', 'discount_amount', 'status', 'payment_method', 'is_installment', 'address_id'];
        
        foreach ($protectedFields as $field) {
            // Restaurer la valeur originale de la base de données pour les champs protégés
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->record->getAttribute($field);
            }
        }
        
        // Gérer les retours de produits si présents dans le formulaire
        if (isset($data['returns']) && is_array($data['returns'])) {
            try {
                foreach ($data['returns'] as $returnData) {
                    if (empty($returnData['order_item_id']) || empty($returnData['reason']) || empty($returnData['description'])) {
                        continue;
                    }
                    
                    // Vérifier que l'utilisateur peut retourner ce produit
                    $orderItem = $this->record->items()->find($returnData['order_item_id']);
                    
                    if (!$orderItem) {
                        throw new \Exception('Article introuvable dans cette commande.');
                    }
                    
                    // Vérifier qu'un retour n'existe pas déjà
                    $existingReturn = OrderReturn::where('order_item_id', $returnData['order_item_id'])
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
                        'order_item_id' => $returnData['order_item_id'],
                        'user_id' => auth()->id(),
                        'reason' => $returnData['reason'],
                        'description' => $returnData['description'],
                        'status' => 'pending',
                        'refund_amount' => $refundAmount,
                    ]);
                }
                
                // Retirer les retours des données du formulaire car ils ne sont pas des champs de Order
                unset($data['returns']);
                
                Notification::make()
                    ->title('Demande(s) de retour créée(s)')
                    ->body('Vos demandes de retour ont été enregistrées et seront traitées par notre équipe.')
                    ->success()
                    ->send();
                    
            } catch (\Exception $e) {
                \Log::error('Error creating order returns: ' . $e->getMessage());
                
                Notification::make()
                    ->title('Erreur lors de la création du retour')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
                    
                throw $e;
            }
        }
        
        return $data;
    }
}
