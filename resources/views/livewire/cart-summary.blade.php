<?php

use Livewire\Volt\Component;
use App\Services\CartService;

new class extends Component {
    public float $subtotal = 0.0;
    public float $shipping = 0.0;
    public float $total = 0.0;

    public function mount()
    {
        $this->refreshTotals();
    }

    #[\Livewire\Attributes\On('cart:updated')]
    public function onCartUpdated($count = null)
    {
        $this->refreshTotals();
    }

    private function refreshTotals(): void
    {
        try {
            $this->subtotal = (float) CartService::getTotalPrice();
        } catch (\Throwable $e) {
            $this->subtotal = 0.0;
        }

        // Default shipping, adapt later if you have shipping calculation
        $this->shipping = 0.0;
        $this->total = $this->subtotal + $this->shipping;
    }
};

?>

<div class="w-full lg:w-1/3 px-4">
  <div class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-rhino-700 text-lg mb-4 font-semibold">total panier</h2>
    <div class="pb-4 border-b border-coolGray-200 flex flex-wrap gap-2 justify-between items-center mb-4">
      <p class="text-rhino-300">Subtotal</p>
      <p class="text-rhino-800">{{ number_format($this->subtotal, 0, ',', ' ') }} FCFA</p>
    </div>
    <p class="text-rhino-800 mb-4">Shipping</p>
    <div class="mb-4">
      <div class="flex items-center justify-between flex-wrap gap-2">
        <p class="text-rhino-300">Flat Rate</p>
        <p class="text-rhino-800">{{ number_format($this->shipping, 0, ',', ' ') }} FCFA</p>
      </div>
      <p class="text-rhino-300">Shipping to your selected address</p>
    </div>
    <div class="pb-4 border-b border-coolGray-200 mb-4">
      <a class="text-purple-500 hover:text-purple-600 transition duration-200" href="#">Change Shipping Address</a>
    </div>
    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
      <h2 class="text-rhino-700 font-semibold text-lg">Order Total</h2>
      <h2 class="text-rhino-700 font-semibold text-lg">{{ number_format($this->total, 0, ',', ' ') }} FCFA</h2>
    </div>
    <a class="bg-purple-500 py-3 px-4 rounded-sm text-white text-center hover:bg-purple-600 transition duration-200 w-full inline-block" href="{{ url('/checkout') }}">Passer a l'achat</a>
  </div>
</div>
