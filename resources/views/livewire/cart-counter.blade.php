<?php

use Livewire\Volt\Component;
use App\Services\CartService;

new class extends Component {
    public int $cartCount = 0;

    #[\Livewire\Attributes\On('cart:updated')]
    public function updateCart($count = null)
    {
        
        if ($count !== null) {
            $this->cartCount = $count;
        } else {
            $this->loadCartCount();
        }
    }

    public function mount()
    {
        $this->loadCartCount();
    }

    private function loadCartCount()
    {
        try {
            $this->cartCount = CartService::getTotalQuantity();
            
        } catch (\Throwable $e) {
            $this->cartCount = 0;
        }
    }
};

?>

<a class="relative inline-flex items-center text-sm text-purple-400 hover:text-purple-200" href="{{ url('/panier') }}">
    <svg width="18" height="17" viewbox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M6.99992 15.3333C7.36811 15.3333 7.66658 15.0349 7.66658 14.6667C7.66658 14.2985 7.36811 14 6.99992 14C6.63173 14 6.33325 14.2985 6.33325 14.6667C6.33325 15.0349 6.63173 15.3333 6.99992 15.3333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.3334 15.3333C14.7016 15.3333 15.0001 15.0349 15.0001 14.6667C15.0001 14.2985 14.7016 14 14.3334 14C13.9652 14 13.6667 14.2985 13.6667 14.6667C13.6667 15.0349 13.9652 15.3333 14.3334 15.3333Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M1.66675 1.33334H4.33341L6.12008 10.26C6.18104 10.5669 6.34802 10.8426 6.59178 11.0389C6.83554 11.2351 7.14055 11.3393 7.45341 11.3333H13.9334C14.2463 11.3393 14.5513 11.2351 14.7951 11.0389C15.0388 10.8426 15.2058 10.5669 15.2667 10.26L16.3334 4.66667H5.00008" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>
   
    @if($cartCount >= 0)
        <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-white bg-red-500 rounded-full animate-pulse">
            {{ $cartCount }}
        </span>
    @endif

</a>
