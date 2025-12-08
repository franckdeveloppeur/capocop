<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public ?Product $product = null;
    public string $selectedSize = 'XS';
    public int $quantity = 1;
    public int $selectedIndex = 0;

    public function mount(?string $slug = null)
    {
        $slug = $slug ?? request()->route('slug');
        if (!$slug) {
            return;
        }

        $this->product = Product::where('slug', $slug)
            ->with(['media', 'shop', 'categories', 'reviews'])
            ->firstOrFail();
    }

    #[\Livewire\Attributes\Computed]
    public function images()
    {
        return $this->product?->media()->get() ?? collect();
    }

    #[\Livewire\Attributes\Computed]
    public function displayPrice()
    {
        if (!$this->product) {
            return null;
        }

        if ($this->product->price_promo && $this->product->price_promo < $this->product->base_price) {
            return [
                'original' => (float) $this->product->base_price,
                'promo' => (float) $this->product->price_promo,
                'hasDiscount' => true,
                'discountPercent' => round((1 - ($this->product->price_promo / $this->product->base_price)) * 100),
            ];
        }

        return [
            'original' => (float) $this->product->base_price,
            'promo' => null,
            'hasDiscount' => false,
            'discountPercent' => 0,
        ];
    }

    public function getImageUrl($media)
    {
        try {
            if ($media) {
                $path = data_get($media, 'custom_properties.full_path')
                    ?? ('products/' . data_get($media, 'file_name'));
                return asset('storage/' . $path);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return null;
    }

    public function addToCart()
    {
        // TODO: Implement cart functionality
        session()->flash('message', 'Produit ajouté au panier!');
    }

    public function toggleFavorite()
    {
        // TODO: Implement favorite functionality
        session()->flash('message', 'Ajouté aux favoris!');
    }

    public function increaseQuantity()
    {
        $this->quantity++;
    }

    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function setSelectedIndex(int $index)
    {
        $this->selectedIndex = max(0, (int) $index);
    }
}; ?>

<div>
    @if($product)
        <section class="bg-white py-12 md:py-24 lg:py-32">
        <div class="container px-4 mx-auto">
            <div class="px-4 md:px-0 max-w-lg mx-auto lg:max-w-5xl xl:max-w-7xl">
                <!-- Breadcrumb -->
                <div class="mb-8">
                    <div class="flex items-center flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <img src="/coleos-assets/logos/logo-coleos.png" alt="">
                            <a class="text-rhino-500 text-sm hover:text-rhino-600 transition duration-200" href="/">Accueil</a>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                                <path d="M15.1211 12C15.1212 12.1313 15.0954 12.2614 15.0451 12.3828C14.9948 12.5041 14.9211 12.6143 14.8281 12.707L10.5859 16.9497C10.3984 17.1372 10.1441 17.2426 9.87889 17.2426C9.6137 17.2426 9.35937 17.1372 9.17186 16.9497C8.98434 16.7622 8.879 16.5079 8.879 16.2427C8.879 15.9775 8.98434 15.7232 9.17186 15.5357L12.707 12L9.17183 8.46437C8.98431 8.27686 8.87897 8.02253 8.87897 7.75734C8.87897 7.49215 8.98431 7.23782 9.17183 7.05031C9.35934 6.86279 9.61367 6.75744 9.87886 6.75744C10.144 6.75744 10.3984 6.86279 10.5859 7.0503L14.8281 11.293C14.9211 11.3857 14.9949 11.4959 15.0451 11.6173C15.0954 11.7386 15.1212 11.8687 15.1211 12Z" fill="#A0A5B8"></path>
                            </svg>
                        </div>
                        <a class="text-rhino-500 text-sm hover:text-rhino-600 transition duration-200" href="/produits">Produits</a>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                                <path d="M15.1211 12C15.1212 12.1313 15.0954 12.2614 15.0451 12.3828C14.9948 12.5041 14.9211 12.6143 14.8281 12.707L10.5859 16.9497C10.3984 17.1372 10.1441 17.2426 9.87889 17.2426C9.6137 17.2426 9.35937 17.1372 9.17186 16.9497C8.98434 16.7622 8.879 16.5079 8.879 16.2427C8.879 15.9775 8.98434 15.7232 9.17186 15.5357L12.707 12L9.17183 8.46437C8.98431 8.27686 8.87897 8.02253 8.87897 7.75734C8.87897 7.49215 8.98431 7.23782 9.17183 7.05031C9.35934 6.86279 9.61367 6.75744 9.87886 6.75744C10.144 6.75744 10.3984 6.86279 10.5859 7.0503L14.8281 11.293C14.9211 11.3857 14.9949 11.4959 15.0451 11.6173C15.0954 11.7386 15.1212 11.8687 15.1211 12Z" fill="#A4AFBB"></path>
                            </svg>
                        </div>
                        <span class="text-rhino-300 text-sm">{{ $product->title }}</span>
                    </div>
                </div>

                <!-- Product Detail Section -->
                <div class="relative border border-coolGray-200 rounded-lg p-4 sm:p-9">
                    <div class="flex flex-wrap -mx-4">
                        <!-- Images Section -->
                        <div class="w-full lg:w-1/2 xl:w-3/5 px-4 mb-12 lg:mb-0">
                            <div class="max-w-xl relative">
                                <!-- Main Image -->
                                <div class="block w-full h-128 mb-6 lg:mb-0 rounded-xl object-cover bg-coolGray-100">
                                    @if($this->images->count() > 0)
                                        @php
                                            $mainMedia = $this->images->get($selectedIndex) ?? $this->images->first();
                                            $mainImg = $this->getImageUrl($mainMedia);
                                        @endphp
                                        @if($mainImg)
                                            <img class="block w-full h-full object-cover rounded-xl" src="{{ $mainImg }}" alt="{{ $product->title }}">
                                        @endif
                                    @endif
                                </div>

                                <!-- Thumbnail Gallery (placed below main image to avoid overlap) -->
                                @if($this->images->count() > 0)
                                    <div class="mt-4 w-full">
                                        <div class="flex flex-wrap -mx-1" style="z-index:1; position:relative;">
                                            @foreach($this->images->take(8) as $media)
                                                @php $thumbImg = $this->getImageUrl($media); @endphp
                                                @if($thumbImg)
                                                    @php $isSelected = ($selectedIndex === $loop->index); @endphp
                                                    <div class="w-1/2 lg:w-1/4 p-1">
                                                        <button type="button" wire:click="setSelectedIndex({{ $loop->index }})"
                                                                class="block h-28 xl:h-32 w-full lg:p-1.5 bg-white rounded-xl transition {{ $isSelected ? 'border-2 border-purple-500' : 'hover:border-2 hover:border-purple-500' }}">
                                                            <img class="block w-full h-full object-cover rounded-xl" src="{{ $thumbImg }}" alt="">
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Product Info Section -->
                        <div class="w-full lg:w-1/2 xl:w-2/5 relative px-4">
                            <div class="max-w-md ml-auto">
                                <!-- Action Buttons: Favorite and Share -->
                                <div class="absolute top-0 right-0 -mr-6 md:-mr-11 flex items-start gap-3">
                                    <livewire:favorite-button :product-id="$product->id" />
                                    <livewire:share-button :slug="$product->slug" />
                                </div>

                                <!-- Stock Status -->
                                <div class="inline-block mb-4 bg-orange-500 rounded-full px-4 py-1 text-center uppercase text-white text-xs font-bold tracking-widest">
                                    {{ $product->stock_manage ? 'In stock' : 'Out of stock' }}
                                </div>

                                <!-- Product Title -->
                                <h1 class="mb-4 font-heading text-3xl sm:text-4xl text-rhino-700 font-semibold">
                                    {{ $product->title }}
                                </h1>

                                <!-- Product Description -->
                                <p class="mb-6 text-rhino-400 text-sm font-medium break-words whitespace-pre-line max-h-32 overflow-auto">
                                    {!! nl2br(e(Str::limit(strip_tags($product->description ?? ''), 300))) !!}
                                </p>

                                <!-- Price Section -->
                                <div class="mb-8">
                                    <div class="py-4">
                                        @if($this->displayPrice['hasDiscount'])
                                            <div class="flex items-center gap-4">
                                                <h2 class="text-rhino-700 text-4xl font-semibold font-heading">
                                                    {{ number_format($this->displayPrice['promo'], 2) }} XFA
                                                </h2>
                                                <span class="text-xl text-gray-400 line-through">
                                                    {{ number_format($this->displayPrice['original'], 2) }} XFA
                                                </span>
                                                <span class="text-sm bg-orange-100 text-orange-700 px-3 py-1 rounded-full font-bold">
                                                    -{{ $this->displayPrice['discountPercent'] }}%
                                                </span>
                                            </div>
                                        @else
                                            <h2 class="text-rhino-700 text-4xl font-semibold font-heading">
                                                {{ number_format($this->displayPrice['original'], 2) }} XFA
                                            </h2>
                                        @endif
                                    </div>
                                </div>

                                <!-- Size Selection -->
                                <div class="mb-6">
                                    <p class="uppercase text-xs font-bold text-rhino-500 mb-3">SIZE</p>
                                    <div class="flex flex-wrap -mx-1 -mb-1">
                                        @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                            <div class="w-1/3 md:w-1/6 px-1 mb-1">
                                                <button wire:click="$set('selectedSize', '{{ $size }}')"
                                                        :class="{ 'border-purple-500 text-purple-700 bg-purple-50': $wire.selectedSize === '{{ $size }}', 'border-coolGray-200 text-coolGray-700': $wire.selectedSize !== '{{ $size }}' }"
                                                        class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 hover:border-purple-500 hover:text-purple-700">
                                                    {{ $size }}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Quantity and Add to Cart -->
                                <div class="mb-6 flex items-center gap-4">
                                    <div class="flex items-center border border-coolGray-200 rounded-sm">
                                        <button wire:click="decreaseQuantity" class="px-3 py-2 text-coolGray-700 hover:bg-coolGray-100">-</button>
                                        <span class="px-4 py-2 text-center font-semibold">{{ $quantity }}</span>
                                        <button wire:click="increaseQuantity" class="px-3 py-2 text-coolGray-700 hover:bg-coolGray-100">+</button>
                                    </div>
                                    <button wire:click="addToCart" class="flex-1 px-3 py-4 rounded-sm text-center text-white text-sm font-medium bg-purple-500 hover:bg-purple-600 transition duration-200">
                                        Ajouter au panier
                                    </button>
                                </div>

                                <!-- Shop Info -->
                                @if($product->shop)
                                    <div class="py-4 border-t border-coolGray-200">
                                        <p class="text-xs text-coolGray-500 mb-2">Vendu par</p>
                                        <p class="text-sm font-semibold text-rhino-700">{{ $product->shop->name }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Similar Products -->
    <x-similar-products :product="$product" :limit="12" />
    @else
        <section class="bg-white py-12 md:py-24 lg:py-32">
            <div class="container px-4 mx-auto text-center">
                <h2 class="text-2xl font-semibold text-rhino-700 mb-4">Produit non trouvé</h2>
                <p class="text-rhino-400 mb-6">Le produit que vous recherchez n'existe pas ou a été supprimé.</p>
                <a href="/produits" class="inline-block px-6 py-3 bg-purple-500 text-white rounded-sm hover:bg-purple-600 transition duration-200">
                    Retour aux produits
                </a>
            </div>
        </section>
    @endif
</div>
