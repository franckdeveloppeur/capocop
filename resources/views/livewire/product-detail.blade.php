<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\ProductVariant;

new class extends Component {
    public ?Product $product = null;
    public ?ProductVariant $selectedVariant = null;
    public array $selectedAttributes = [];
    public int $quantity = 1;
    public int $selectedIndex = 0;

    public function mount(?string $slug = null)
    {
        $slug = $slug ?? request()->route('slug');
        if (!$slug) {
            return;
        }

        $this->product = Product::where('slug', $slug)
            ->with(['media', 'shop', 'categories', 'reviews', 'variants'])
            ->firstOrFail();
            
        // Sélectionner la première variante par défaut si disponible
        if ($this->product->variants->isNotEmpty()) {
            $this->selectedVariant = $this->product->variants->first();
            $this->selectedAttributes = $this->selectedVariant->attributes ?? [];
        }
    }

    #[\Livewire\Attributes\Computed]
    public function images()
    {
        return $this->product?->media()->get() ?? collect();
    }

    #[\Livewire\Attributes\Computed]
    public function hasVariants()
    {
        return $this->product && $this->product->variants->isNotEmpty();
    }

    #[\Livewire\Attributes\Computed]
    public function availableAttributes()
    {
        if (!$this->hasVariants) {
            return [];
        }

        $attributes = [];
        foreach ($this->product->variants as $variant) {
            if (!empty($variant->attributes)) {
                foreach ($variant->attributes as $key => $value) {
                    if (!empty($value) && $key !== 'color') {
                        $attributes[$key][] = $value;
                    }
                }
            }
        }

        // Dédupliquer les valeurs
        foreach ($attributes as $key => $values) {
            $attributes[$key] = array_values(array_unique($values));
        }

        return $attributes;
    }

    #[\Livewire\Attributes\Computed]
    public function availableColors()
    {
        if (!$this->hasVariants) {
            return [];
        }

        $colors = [];
        foreach ($this->product->variants as $variant) {
            if (!empty($variant->attributes['color'])) {
                $colorValue = $variant->attributes['color'];
                // Stocker la couleur avec son variant pour accès facile
                if (!isset($colors[$colorValue])) {
                    $colors[$colorValue] = [
                        'value' => $colorValue,
                        'variant' => $variant,
                        'stock' => $variant->stock,
                        'price' => $variant->price,
                    ];
                }
            }
        }

        return array_values($colors);
    }

    public function selectVariant($variantId)
    {
        $variant = $this->product->variants->firstWhere('id', $variantId);
        if ($variant) {
            $this->selectedVariant = $variant;
            $this->selectedAttributes = $variant->attributes ?? [];
        }
    }

    public function selectAttributeValue($attribute, $value)
    {
        $this->selectedAttributes[$attribute] = $value;
        
        // Trouver la variante qui correspond le mieux aux attributs sélectionnés
        $matchingVariant = $this->product->variants->first(function ($variant) {
            foreach ($this->selectedAttributes as $attr => $val) {
                if (!isset($variant->attributes[$attr]) || $variant->attributes[$attr] !== $val) {
                    return false;
                }
            }
            return true;
        });

        // Si pas de correspondance exacte, chercher par attribut principal
        if (!$matchingVariant) {
            $matchingVariant = $this->product->variants->first(function ($variant) use ($attribute, $value) {
                return isset($variant->attributes[$attribute]) && 
                       $variant->attributes[$attribute] === $value;
            });
        }

        if ($matchingVariant) {
            $this->selectedVariant = $matchingVariant;
            $this->selectedAttributes = $matchingVariant->attributes ?? [];
        }
    }

    #[\Livewire\Attributes\Computed]
    public function displayPrice()
    {
        if (!$this->product) {
            return null;
        }

        // Si une variante est sélectionnée, utiliser son prix
        if ($this->selectedVariant && $this->selectedVariant->price) {
            $variantPrice = (float) $this->selectedVariant->price;
            $basePrice = (float) $this->product->base_price;
            
            if ($variantPrice < $basePrice) {
                return [
                    'original' => $basePrice,
                    'promo' => $variantPrice,
                    'hasDiscount' => true,
                    'discountPercent' => round((1 - ($variantPrice / $basePrice)) * 100),
                ];
            }
            
            return [
                'original' => $variantPrice,
                'promo' => null,
                'hasDiscount' => false,
                'discountPercent' => 0,
            ];
        }

        // Sinon, utiliser le prix du produit
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

    #[\Livewire\Attributes\Computed]
    public function currentStock()
    {
        if ($this->selectedVariant) {
            return $this->selectedVariant->stock ?? 0;
        }
        return null;
    }

    #[\Livewire\Attributes\Computed]
    public function isInStock()
    {
        if ($this->selectedVariant) {
            return $this->selectedVariant->stock > 0;
        }
        return $this->product->stock_manage ?? false;
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
                                <div class="inline-block mb-4 rounded-full px-4 py-1 text-center uppercase text-white text-xs font-bold tracking-widest {{ $this->isInStock ? 'bg-green-500' : 'bg-red-500' }}">
                                    @if($this->hasVariants)
                                        @if($this->isInStock)
                                            {{ $this->currentStock }} en stock
                                        @else
                                            Rupture de stock
                                        @endif
                                    @else
                                        {{ $product->stock_manage ? 'En stock' : 'Hors stock' }}
                                    @endif
                                </div>

                                <!-- SKU Info -->
                                @if($selectedVariant)
                                    <div class="mb-2">
                                        <span class="text-xs text-coolGray-500">SKU: </span>
                                        <span class="text-xs font-semibold text-rhino-600">{{ $selectedVariant->sku }}</span>
                                    </div>
                                @endif

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
                                        @if($selectedVariant)
                                            <!-- Prix de la variante -->
                                            <div class="mb-3">
                                                <div class="flex items-baseline gap-2 mb-2">
                                                    <span class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Prix de la variante</span>
                                                    @if($selectedVariant->stock <= 5 && $selectedVariant->stock > 0)
                                                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-medium">
                                                            Plus que {{ $selectedVariant->stock }} restant{{ $selectedVariant->stock > 1 ? 's' : '' }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($this->displayPrice['hasDiscount'])
                                                    <div class="flex items-center gap-4">
                                                        <h2 class="text-purple-600 text-4xl font-bold font-heading">
                                                            {{ number_format($this->displayPrice['promo'], 0, ',', ' ') }} XFA
                                                        </h2>
                                                        <span class="text-xl text-gray-400 line-through">
                                                            {{ number_format($this->displayPrice['original'], 0, ',', ' ') }} XFA
                                                        </span>
                                                        <span class="text-sm bg-red-500 text-white px-3 py-1 rounded-full font-bold">
                                                            -{{ $this->displayPrice['discountPercent'] }}%
                                                        </span>
                                                    </div>
                                                @else
                                                    <h2 class="text-purple-600 text-4xl font-bold font-heading">
                                                        {{ number_format($this->displayPrice['original'], 0, ',', ' ') }} XFA
                                                    </h2>
                                                @endif
                                            </div>
                                            
                                            <!-- Prix de base du produit pour comparaison -->
                                            @if($product->base_price != $selectedVariant->price)
                                                <div class="mt-3 p-3 bg-coolGray-50 rounded-lg border border-coolGray-200">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs text-coolGray-600">Prix de base du produit:</span>
                                                        <span class="text-sm font-medium text-coolGray-700">{{ number_format($product->base_price, 0, ',', ' ') }} XFA</span>
                                                    </div>
                                                    @php
                                                        $priceDiff = $selectedVariant->price - $product->base_price;
                                                        $diffPercent = round(($priceDiff / $product->base_price) * 100);
                                                    @endphp
                                                    @if($priceDiff < 0)
                                                        <div class="mt-1 flex items-center gap-1 text-xs text-green-600">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span class="font-semibold">Économisez {{ number_format(abs($priceDiff), 0, ',', ' ') }} XFA ({{ abs($diffPercent) }}%)</span>
                                                        </div>
                                                    @elseif($priceDiff > 0)
                                                        <div class="mt-1 text-xs text-orange-600">
                                                            <span class="font-medium">+{{ number_format($priceDiff, 0, ',', ' ') }} XFA ({{ $diffPercent }}%)</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <!-- Prix standard du produit (sans variante) -->
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold text-rhino-500 uppercase tracking-wide">Prix</span>
                                            </div>
                                            @if($this->displayPrice['hasDiscount'])
                                                <div class="flex items-center gap-4">
                                                    <h2 class="text-rhino-700 text-4xl font-bold font-heading">
                                                        {{ number_format($this->displayPrice['promo'], 0, ',', ' ') }} XFA
                                                    </h2>
                                                    <span class="text-xl text-gray-400 line-through">
                                                        {{ number_format($this->displayPrice['original'], 0, ',', ' ') }} XFA
                                                    </span>
                                                    <span class="text-sm bg-red-500 text-white px-3 py-1 rounded-full font-bold">
                                                        -{{ $this->displayPrice['discountPercent'] }}%
                                                    </span>
                                                </div>
                                            @else
                                                <h2 class="text-rhino-700 text-4xl font-bold font-heading">
                                                    {{ number_format($this->displayPrice['original'], 0, ',', ' ') }} XFA
                                                </h2>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <!-- Color Selection (if available) -->
                                @if($this->hasVariants && count($this->availableColors) > 0)
                                    <div class="mb-6">
                                        <p class="uppercase text-xs font-bold text-rhino-500 mb-3">COULEUR</p>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($this->availableColors as $colorData)
                                                @php
                                                    $colorValue = $colorData['value'];
                                                    $isSelected = isset($selectedAttributes['color']) && $selectedAttributes['color'] === $colorValue;
                                                    $isAvailable = $colorData['stock'] > 0;
                                                @endphp
                                                <div class="relative group">
                                                    <button wire:click="selectAttributeValue('color', '{{ $colorValue }}')"
                                                            @disabled(!$isAvailable)
                                                            class="relative w-12 h-12 rounded-full border-3 transition {{ $isSelected ? 'border-purple-500 ring-4 ring-purple-200' : 'border-coolGray-300 hover:border-purple-400' }} {{ !$isAvailable ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                            style="background-color: {{ $colorValue }};"
                                                            title="{{ $colorValue }}">
                                                        @if($isSelected)
                                                            <div class="absolute inset-0 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        @if(!$isAvailable)
                                                            <div class="absolute inset-0 flex items-center justify-center">
                                                                <div class="w-px h-full bg-red-500 transform rotate-45"></div>
                                                            </div>
                                                        @endif
                                                    </button>
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                        <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                            {{ $colorValue }}
                                                            @if(!$isAvailable)
                                                                <span class="text-red-400">(Épuisé)</span>
                                                            @endif
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Size/Attributes Selection -->
                                @if($this->hasVariants && count($this->availableAttributes) > 0)
                                    @foreach($this->availableAttributes as $attributeName => $attributeValues)
                                        <div class="mb-6">
                                            <p class="uppercase text-xs font-bold text-rhino-500 mb-3">{{ strtoupper($attributeName) }}</p>
                                            <div class="flex flex-wrap -mx-1 -mb-1">
                                                @foreach($attributeValues as $value)
                                                    @php
                                                        $isSelected = isset($selectedAttributes[$attributeName]) && $selectedAttributes[$attributeName] === $value;
                                                    @endphp
                                                    <div class="w-1/3 md:w-1/6 px-1 mb-1">
                                                        <button wire:click="selectAttributeValue('{{ $attributeName }}', '{{ $value }}')"
                                                                class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 {{ $isSelected ? 'border-purple-500 text-purple-700 bg-purple-50 font-bold' : 'border-coolGray-200 text-coolGray-700 hover:border-purple-500 hover:text-purple-700' }}">
                                                            {{ strtoupper($value) }}
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <!-- Variant Selector (if no attributes but has variants) -->
                                @if($this->hasVariants && count($this->availableAttributes) === 0 && count($this->availableColors) === 0)
                                    <div class="mb-6">
                                        <p class="uppercase text-xs font-bold text-rhino-500 mb-3">VARIANTES</p>
                                        <div class="flex flex-col gap-2">
                                            @foreach($product->variants as $variant)
                                                @php
                                                    $isSelected = $selectedVariant && $selectedVariant->id === $variant->id;
                                                    $isAvailable = $variant->stock > 0;
                                                @endphp
                                                <button wire:click="selectVariant('{{ $variant->id }}')"
                                                        @disabled(!$isAvailable)
                                                        class="w-full border p-3 rounded-sm text-left transition duration-200 {{ $isSelected ? 'border-purple-500 bg-purple-50' : 'border-coolGray-200 hover:border-purple-400' }} {{ !$isAvailable ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <p class="font-semibold text-sm {{ $isSelected ? 'text-purple-700' : 'text-rhino-700' }}">
                                                                {{ $variant->sku }}
                                                            </p>
                                                            <p class="text-xs text-coolGray-500 mt-1">
                                                                Stock: {{ $variant->stock }} | Prix: {{ number_format($variant->price, 2) }} XFA
                                                            </p>
                                                        </div>
                                                        @if($isSelected)
                                                            <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Quantity and Add to Cart -->
                                <div class="mb-6 flex items-center gap-4">
                                    <div class="flex items-center border border-coolGray-200 rounded-sm">
                                        <button wire:click="decreaseQuantity" class="px-3 py-2 text-coolGray-700 hover:bg-coolGray-100" type="button">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="px-6 py-2 text-center font-semibold min-w-[60px]">{{ $quantity }}</span>
                                        <button wire:click="increaseQuantity" class="px-3 py-2 text-coolGray-700 hover:bg-coolGray-100" type="button">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    @if($this->hasVariants && !$this->isInStock)
                                        <button disabled class="flex-1 bg-gray-300 text-gray-600 px-6 py-3 rounded-sm font-semibold cursor-not-allowed">
                                            Rupture de stock
                                        </button>
                                    @else
                                        <livewire:cart-button 
                                            :product-id="$product->id" 
                                            :variant-id="$selectedVariant?->id" 
                                            :quantity="$quantity" 
                                            :key="'cart-detail-'.$product->id.'-'.($selectedVariant?->id ?? 'no-variant').'-'.$quantity" />
                                    @endif
                                </div>

                                <!-- Variant Info Card -->
                                @if($selectedVariant)
                                    <div class="mb-6 bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-4 shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 bg-purple-500 rounded-full p-2">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-purple-900 mb-2 flex items-center gap-2">
                                                    Variante sélectionnée
                                                    @if($selectedVariant->stock > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="w-1.5 h-1.5 mr-1 bg-green-400 rounded-full"></span>
                                                            Disponible
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="w-1.5 h-1.5 mr-1 bg-red-400 rounded-full"></span>
                                                            Épuisé
                                                        </span>
                                                    @endif
                                                </p>
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div class="bg-white/60 rounded-lg p-2">
                                                        <p class="text-purple-600 font-medium mb-0.5">SKU</p>
                                                        <p class="text-purple-900 font-semibold">{{ $selectedVariant->sku }}</p>
                                                    </div>
                                                    <div class="bg-white/60 rounded-lg p-2">
                                                        <p class="text-purple-600 font-medium mb-0.5">Stock</p>
                                                        <p class="text-purple-900 font-semibold">{{ $selectedVariant->stock }} unité{{ $selectedVariant->stock > 1 ? 's' : '' }}</p>
                                                    </div>
                                                    @if($selectedVariant->weight)
                                                        <div class="bg-white/60 rounded-lg p-2">
                                                            <p class="text-purple-600 font-medium mb-0.5">Poids</p>
                                                            <p class="text-purple-900 font-semibold">{{ $selectedVariant->weight }} kg</p>
                                                        </div>
                                                    @endif
                                                    @if(!empty($selectedVariant->attributes))
                                                        @foreach($selectedVariant->attributes as $attr => $val)
                                                            @if(!empty($val))
                                                                <div class="bg-white/60 rounded-lg p-2">
                                                                    <p class="text-purple-600 font-medium mb-0.5 capitalize">{{ $attr }}</p>
                                                                    <p class="text-purple-900 font-semibold uppercase">{{ $val }}</p>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
