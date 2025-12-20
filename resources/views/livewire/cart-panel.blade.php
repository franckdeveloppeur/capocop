<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use Filament\Notifications\Notification;

new class extends Component {
    public array $items = [];
    public bool $loading = false;

    public function mount()
    {
        $this->refreshItems();
    }

    private function refreshItems(): void
    {
        try {
            $content = CartService::getItems();
            $payload = [];

            foreach ($content as $item) {
                $payload[] = [
                    'id' => (string) $item->id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                    'image' => data_get($item, 'attributes.image') ?? null,
                ];
            }

            $this->items = $payload;
        } catch (\Throwable $e) {
            $this->items = [];
        }
    }

    #[\Livewire\Attributes\On('cart:updated')]
    public function onCartUpdated($count = null)
    {
        $this->refreshItems();
    }

    public function increment(string $productId)
    {
        try {
            $this->loading = true;
            // Find current quantity from server (best-effort)
            $items = collect(CartService::getItems());
            $item = $items->firstWhere('id', $productId);
            $newQty = ($item ? (int)$item->quantity : 0) + 1;

            CartService::updateQuantity($productId, $newQty);
            $this->dispatch('cart:updated', count: CartService::getTotalQuantity());
        } catch (\Throwable $e) {
            Notification::make()->title('Erreur')->body('Impossible de mettre à jour la quantité.')->danger()->send();
        } finally {
            $this->loading = false;
            $this->refreshItems();
        }
    }

    public function decrement(string $productId)
    {
        try {
            $this->loading = true;
            $items = collect(CartService::getItems());
            $item = $items->firstWhere('id', $productId);
            $current = $item ? (int)$item->quantity : 1;
            $newQty = max(1, $current - 1);

            CartService::updateQuantity($productId, $newQty);
            $this->dispatch('cart:updated', count: CartService::getTotalQuantity());
        } catch (\Throwable $e) {
            Notification::make()->title('Erreur')->body('Impossible de mettre à jour la quantité.')->danger()->send();
        } finally {
            $this->loading = false;
            $this->refreshItems();
        }
    }

    public function remove(string $productId)
    {
        try {
            $this->loading = true;
            CartService::removeProduct($productId);
            $this->dispatch('cart:updated', count: CartService::getTotalQuantity());
            Notification::make()->title('Supprimé')->body('L\'article a été supprimé du panier.')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Erreur')->body('Impossible de supprimer l\'article.')->danger()->send();
        } finally {
            $this->loading = false;
            $this->refreshItems();
        }
    }
};

?>

<div class="mb-6 py-4 overflow-x-auto">
    @if(empty($items))
        <p class="text-sm text-gray-500">Votre panier est vide.</p>
    @else
        <div class="flex w-full">
            <div class="w-96">
                <div class="w-full py-4 px-6 border-b border-coolGray-200">
                    <span class="text-rhino-800">Produits</span>
                </div>
                @foreach($items as $item)
                    <div class="w-full py-4 px-6 border-b border-coolGray-200 h-32 flex items-center">
                        <div class="flex items-center gap-4">
                            <div class="bg-gray-100 rounded-lg w-36 lg:w-24 h-24 flex items-center justify-center overflow-hidden">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <h2 class="text-rhino-800">{{ $item['name'] }}</h2>
                                <p class="text-rhino-300">&nbsp;</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="w-40">
                <div class="py-4 px-6 border-b border-coolGray-200">
                    <span class="text-rhino-800">Prix</span>
                </div>
                @foreach($items as $item)
                    <div class="py-4 px-6 border-b border-coolGray-200 h-32 flex items-center">
                        <p class="text-rhino-800">{{ number_format($item['price'], 2, '.', '') }} FCFA</p>
                    </div>
                @endforeach
            </div>

            <div class="w-40">
                <div class="py-4 px-6 border-b border-coolGray-200">
                    <span class="text-rhino-800">Quantité</span>
                </div>
                @foreach($items as $item)
                    <div class="py-4 px-6 border-b border-coolGray-200 h-32 flex items-center">
                        <div class="py-3 px-4 rounded-sm border border-coolGray-200 flex gap-4 items-center bg-white" x-data="{ quantity: {{ $item['quantity'] }} }">
                            <button x-on:click="quantity &gt; 1 ? quantity-- : quantity; $wire.decrement('{{ $item['id'] }}')" class="cursor-pointer text-coolGray-300 hover:text-coolGray-400 transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewbox="0 0 16 17" fill="none">
                                    <path d="M12.6667 7.49988H3.33341C3.1566 7.49988 2.98703 7.57012 2.86201 7.69514C2.73699 7.82016 2.66675 7.98973 2.66675 8.16654C2.66675 8.34336 2.73699 8.51292 2.86201 8.63795C2.98703 8.76297 3.1566 8.83321 3.33341 8.83321H12.6667C12.8436 8.83321 13.0131 8.76297 13.1382 8.63795C13.2632 8.51292 13.3334 8.34336 13.3334 8.16654C13.3334 7.98973 13.2632 7.82016 13.1382 7.69514C13.0131 7.57012 12.8436 7.49988 12.6667 7.49988Z" fill="currentColor"></path>
                                </svg>
                            </button>
                            <span x-text="quantity" class="text-rhino-800 text-sm">{{ $item['quantity'] }}</span>
                            <button x-on:click="quantity++; $wire.increment('{{ $item['id'] }}')" class="cursor-pointer text-coolGray-300 hover:text-coolGray-400 transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewbox="0 0 16 17" fill="none">
                                    <path d="M12.6667 7.4998H8.66675V3.4998C8.66675 3.32299 8.59651 3.15342 8.47149 3.02839C8.34646 2.90337 8.17689 2.83313 8.00008 2.83313C7.82327 2.83313 7.6537 2.90337 7.52868 3.02839C7.40365 3.15342 7.33341 3.32299 7.33341 3.4998V7.4998H3.33341C3.1566 7.4998 2.98703 7.57003 2.86201 7.69506C2.73699 7.82008 2.66675 7.98965 2.66675 8.16646C2.66675 8.34327 2.73699 8.51284 2.86201 8.63787C2.98703 8.76289 3.1566 8.83313 3.33341 8.83313H7.33341V12.8331C7.33341 13.0099 7.40365 13.1795 7.52868 13.3045C7.6537 13.4296 7.82327 13.4998 8.00008 13.4998C8.17689 13.4998 8.34646 13.4296 8.47149 13.3045C8.59651 13.1795 8.66675 13.0099 8.66675 12.8331V8.83313H12.6667C12.8436 8.83313 13.0131 8.76289 13.1382 8.63787C13.2632 8.51284 13.3334 8.34327 13.3334 8.16646C13.3334 7.98965 13.2632 7.82008 13.1382 7.69506C13.0131 7.57003 12.8436 7.4998 12.6667 7.4998Z" fill="currentColor"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="w-40">
                <div class="py-4 px-6 border-b border-coolGray-200">
                    <span class="text-rhino-800">Sous-total</span>
                </div>
                @foreach($items as $item)
                    <div class="py-4 px-6 border-b border-coolGray-200 h-32 flex items-center">
                        <p class="text-rhino-800">{{ number_format($item['price'] * $item['quantity'], 2, '.', '') }} FCFA</p>
                    </div>
                @endforeach
            </div>
        </div>

    @endif

</div>
