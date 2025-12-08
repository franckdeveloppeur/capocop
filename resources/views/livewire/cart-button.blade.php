<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Filament\Notifications\Notification;

new class extends Component {
    public string $productId;
    public int $quantity = 1;

    public function mount(string $productId, int $quantity = 1)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function addToCart()
    {
        try {
            if (CartService::addProduct($this->productId, $this->quantity)) {
                // Dispatch event to update cart counter in navigation
                $this->dispatch('cart:updated', count: CartService::getTotalQuantity());

                // Show success notification
                Notification::make()
                    ->title('Produit ajouté')
                    ->body('Le produit a été ajouté à votre panier avec succès.')
                    ->success()
                    ->send();
            } else {
                throw new \Exception('Erreur lors de l\'ajout au panier');
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Une erreur s\'est produite lors de l\'ajout au panier.')
                ->danger()
                ->send();
        }
    }
};

?>

<button 
    wire:click="addToCart"
    class="bg-purple-500 hover:bg-purple-600 text-white p-3 rounded-full transition duration-200"
    title="Ajouter au panier"
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
>
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
    </svg>
</button>
