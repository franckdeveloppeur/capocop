<?php

use Livewire\Volt\Component;

new class extends Component {
    public $products = [];
    public int $totalProducts = 0;

    public function goToPage($page)
    {
        $this->dispatch('go-to-page', page: $page);
    }
};

?>

<div class="pb-8 w-full md:w-2/3 lg:w-3/4 px-4">
    @if(!empty($products['items']) && count($products['items']) > 0)
        <!-- Products Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
            @foreach($products['items'] as $product)
                @php
                    $media = optional($product->media->first());
                    if ($media) {
                        try {
                            $path = data_get($media, 'custom_properties.full_path')
                                ?? ('products/' . data_get($media, 'file_name'));
                            $imageUrl = asset('storage/' . $path);
                        } catch (\Throwable $e) {
                            $imageUrl = asset('coleos-assets/product-blocks/product-no-bg1.png');
                        }
                    } else {
                        $imageUrl = asset('coleos-assets/product-blocks/product-no-bg1.png');
                    }
                    $hasPromo = !empty($product->price_promo) && $product->price_promo < $product->base_price;
                @endphp
                <a href="{{ route('products.show', $product->slug) }}" class="group">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150 overflow-hidden">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $product->title }}"
                            loading="lazy"
                            class="w-full h-full object-cover rounded-lg"
                        >
                        @if($hasPromo)
                            <div class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                Sale
                            </div>
                        @endif
                    </div>
                    <p class="text-rhino-700 font-semibold line-clamp-2 mb-1">{{ $product->title }}</p>
                    
                    @if($hasPromo)
                        <p class="text-rhino-300">
                            <span class="line-through mr-2">{{ number_format($product->base_price, 2) }} FCFA</span>
                            <span class="text-red-600 font-bold">{{ number_format($product->price_promo, 2) }} FCFA</span>
                        </p>
                    @else
                        <p class="text-rhino-300">{{ number_format($product->base_price, 2) }} FCFA</p>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products['last_page'] > 1)
            <div class="flex justify-center items-center gap-2 mb-8 flex-wrap">
                @if($products['current_page'] == 1)
                    <span class="px-3 py-2 rounded-sm bg-coolGray-100 text-coolGray-400 cursor-not-allowed">← Précédent</span>
                @else
                    <button wire:click="goToPage({{ $products['current_page'] - 1 }})" class="px-3 py-2 rounded-sm bg-coolGray-100 hover:bg-coolGray-200 text-coolGray-700 transition duration-200">← Précédent</button>
                @endif

                @for($page = 1; $page <= $products['last_page']; $page++)
                    @if($page == $products['current_page'])
                        <span class="px-3 py-2 rounded-sm bg-purple-500 text-white font-semibold">{{ $page }}</span>
                    @else
                        <button wire:click="goToPage({{ $page }})" class="px-3 py-2 rounded-sm bg-coolGray-100 hover:bg-coolGray-200 text-coolGray-700 transition duration-200">{{ $page }}</button>
                    @endif
                @endfor

                @if($products['current_page'] < $products['last_page'])
                    <button wire:click="goToPage({{ $products['current_page'] + 1 }})" class="px-3 py-2 rounded-sm bg-coolGray-100 hover:bg-coolGray-200 text-coolGray-700 transition duration-200">Suivant →</button>
                @else
                    <span class="px-3 py-2 rounded-sm bg-coolGray-100 text-coolGray-400 cursor-not-allowed">Suivant →</span>
                @endif
            </div>
        @endif
    @else
        <div class="py-12 text-center">
            <p class="text-rhino-400 text-lg mb-4">Aucun produit trouvé</p>
            <p class="text-rhino-300 text-sm">Essayez d'ajuster vos filtres ou de rechercher d'autres produits.</p>
        </div>
    @endif
</div>
