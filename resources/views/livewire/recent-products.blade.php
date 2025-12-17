<?php

use Livewire\Volt\Component;
use App\Models\Product;
use Illuminate\Pagination\Paginator;

new class extends Component {
    public int $limit = 8;
    public int $perPage = 4;
    public bool $showMore = false;

    #[\Livewire\Attributes\Computed]
    public function products()
    {
        return Product::where('status', 'active')
            ->with('media')
            ->latest('created_at')
            ->take($this->showMore ? $this->limit : $this->perPage)
            ->get();
    }

    public function toggleShowMore()
    {
        $this->showMore = !$this->showMore;
    }

    public function getImageUrl($product)
    {
        try {
            $firstMedia = $product->media->first();
            if ($firstMedia) {
                $path = data_get($firstMedia, 'custom_properties.full_path')
                    ?? ('products/' . data_get($firstMedia, 'file_name'));
                return asset('storage/' . $path);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    public function getDisplayPrice($product)
    {
        if ($product->price_promo && $product->price_promo < $product->base_price) {
            return [
                'original' => (float) $product->base_price,
                'promo' => (float) $product->price_promo,
                'hasDiscount' => true,
            ];
        }

        return [
            'original' => (float) $product->base_price,
            'promo' => null,
            'hasDiscount' => false,
        ];
    }
}; ?>

<section class="bg-white py-12 md:py-24 lg:py-32">
    <div class="container px-4 mx-auto">
        <h2 class="text-4xl text-center font-heading font-semibold text-rhino-600 tracking-xs mb-14">
            Produits récents
        </h2>

        <div class="flex flex-wrap -mx-4 -mb-8">
            @forelse($this->products as $product)
                @php
                    $priceData = $this->getDisplayPrice($product);
                    $imageUrl = $this->getImageUrl($product);
                @endphp

                <div class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 px-4 mb-8">
                    <a href="{{ route('products.show', $product->slug) }}" class="block group max-w-sm md:max-w-none mx-auto">
                        <div class="relative flex items-center justify-center h-96 mb-4 bg-coolGray-100 rounded-xl border-2 border-transparent group-hover:border-purple-500 transition duration-150 overflow-hidden">
                            @if($imageUrl)
                                <img
                                    class="block h-full w-full object-cover"
                                    src="{{ $imageUrl }}"
                                    alt="{{ $product->title }}"
                                    loading="lazy"
                                >
                            @else
                                <div class="flex items-center justify-center h-full w-full text-gray-400">
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif

                            @if($priceData['hasDiscount'])
                                <div class="absolute top-0 left-0 m-6">
                                    <span class="inline-block py-1 px-3 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full">
                                        Sale
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="text-center">
                            <span class="block text-xl text-rhino-500 font-semibold mb-1 line-clamp-2">
                                {{ $product->title }}
                            </span>

                            @if($priceData['hasDiscount'])
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-sm text-gray-400 line-through">
                                        {{ number_format($priceData['original'], 2) }} XFA
                                    </span>
                                    <span class="text-lg text-orange-500 font-bold">
                                        {{ number_format($priceData['promo'], 2) }} XFA
                                    </span>
                                </div>
                            @else
                                <span class="block text-base text-rhino-300">
                                    {{ number_format($priceData['original'], 2) }} XFA
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="w-full text-center py-8">
                    <p class="text-gray-500">Aucun produit disponible pour le moment.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-12">
            <button
                wire:click="toggleShowMore"
                :class="{ 'hidden': {{ $this->showMore ? 'true' : 'false' }} }"
                class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-purple-500 hover:text-white bg-white border border-purple-500 rounded-sm hover:bg-purple-500 transition duration-200"
            >
                @if($this->showMore)
                    Voir moins
                @else
                    Voir plus
                @endif
            </button>
        </div>
    </div>
</section>
