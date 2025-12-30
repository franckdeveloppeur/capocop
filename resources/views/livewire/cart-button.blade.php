<?php

use Livewire\Volt\Component;
use App\Services\CartService;

new class extends Component {
    public string $productId;
    public int $quantity = 1;
    public bool $isAdding = false;

    public function mount(string $productId, int $quantity = 1)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    public function addToCart()
    {
        // Optimisation: feedback visuel immédiat
        $this->isAdding = true;
        
        try {
            // Utiliser defer pour ne pas bloquer l'UI
            $this->dispatch('cart:adding');
            
            if (CartService::addProduct($this->productId, $this->quantity)) {
                // Dispatch event to update cart counter in navigation
                $this->dispatch('cart:updated', count: CartService::getTotalQuantity());

                // Show success notification via JavaScript (plus rapide)
                $this->dispatch('toast', 
                    type: 'success',
                    title: 'Produit ajouté',
                    message: 'Le produit a été ajouté à votre panier avec succès.'
                );
            } else {
                throw new \Exception('Erreur lors de l\'ajout au panier');
            }
        } catch (\Throwable $e) {
            $this->dispatch('toast', 
                type: 'error',
                title: 'Erreur',
                message: 'Une erreur s\'est produite lors de l\'ajout au panier.'
            );
        } finally {
            $this->isAdding = false;
        }
    }
};

?>

<div class="relative">
    <button 
        wire:click="addToCart"
        wire:loading.attr="disabled"
        class="bg-purple-500 hover:bg-purple-600 active:scale-95 text-white p-3 rounded-full transition-all duration-200 relative overflow-hidden group"
        title="Ajouter au panier"
        :disabled="$wire.isAdding"
    >
        <!-- Icône panier -->
        <svg 
            wire:loading.remove
            wire:target="addToCart"
            xmlns="http://www.w3.org/2000/svg" 
            width="20" 
            height="20" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round"
            class="transition-transform duration-200 group-hover:scale-110"
        >
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        
        <!-- Spinner de chargement -->
        <svg 
            wire:loading
            wire:target="addToCart"
            class="animate-spin h-5 w-5 text-white" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        
        <!-- Effet de ripple au clic -->
        <span class="absolute inset-0 rounded-full bg-white opacity-0 group-active:opacity-20 transition-opacity duration-150"></span>
    </button>
    
    <!-- Indicateur de succès animé -->
    <div 
        x-data="{ show: false }"
        x-on:cart:updated.window="show = true; setTimeout(() => show = false, 1000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-0"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-0"
        class="absolute -top-2 -right-2 bg-green-500 rounded-full p-1 shadow-lg"
        style="display: none;"
    >
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        if (window.showToast) {
            window.showToast(data[0].type, data[0].title, data[0].message);
        }
    });
});
</script>
