<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Lazy;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;

new #[Lazy] class extends Component {
    public array $products = [];
    public int $initialLimit = 4;
    public int $expandedLimit = 8;
    public bool $showMore = false;

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $limit = $this->showMore ? $this->expandedLimit : $this->initialLimit;
        $cacheKey = 'best_sellers:limit:' . $limit;

        $this->products = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($limit) {
            // Récupérer les produits les plus vendus via OrderItem
            $topIds = [];
            try {
                $topIds = OrderItem::select('product_id', \DB::raw('SUM(quantity) as total'))
                    ->groupBy('product_id')
                    ->orderByDesc('total')
                    ->limit($limit)
                    ->pluck('product_id')
                    ->toArray();
            } catch (\Throwable $e) {
                $topIds = [];
            }

            // Fetch products pour ces IDs
            $productsMap = Product::whereIn('id', $topIds)
                ->where('status', 'active')
                ->with(['media' => fn($q) => $q->orderBy('order_column')])
                ->get()
                ->keyBy('id');

            $ordered = collect($topIds)
                ->map(fn($id) => $productsMap->get($id))
                ->filter()
                ->values()
                ->toArray();

            // Compléter si pas assez de produits
            $needed = $limit - count($ordered);
            if ($needed > 0) {
                $extra = Product::where('status', 'active')
                    ->when(count($topIds) > 0, fn($q) => $q->whereNotIn('id', $topIds))
                    ->with(['media' => fn($q) => $q->orderBy('order_column')])
                    ->latest()
                    ->limit($needed)
                    ->get()
                    ->toArray();

                $ordered = array_merge($ordered, $extra);
            }

            // Mapper vers un payload léger
            return collect($ordered)->map(function ($p) {
                $model = is_object($p) ? $p : (object) $p;
                $media = null;
                if (isset($model->media) && is_iterable($model->media)) {
                    $media = collect($model->media)->first();
                }

                $image = $media
                    ? asset('storage/' . (data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'))))
                    : null;

                return (object) [
                    'id' => $model->id ?? null,
                    'title' => $model->title ?? '',
                    'slug' => $model->slug ?? '#',
                    'base_price' => $model->base_price ?? 0,
                    'price_promo' => $model->price_promo ?? null,
                    'image' => $image,
                    'is_new' => isset($model->created_at) && now()->diffInDays($model->created_at) < 30,
                    'has_sale' => !empty($model->price_promo) && $model->price_promo < ($model->base_price ?? PHP_INT_MAX),
                ];
            })->take($limit)->toArray();
        });
    }

    public function toggleShowMore()
    {
        $this->showMore = !$this->showMore;
        Cache::forget('best_sellers:limit:' . ($this->showMore ? $this->expandedLimit : $this->initialLimit));
        $this->loadProducts();
    }

    public function placeholder()
    {
        return view('livewire.best-sellers-placeholder');
    }
};

?>

<section class="bg-white py-12 md:py-24 lg:py-32" x-data="{ showContent: @entangle('showMore') }">
    <div class="container px-4 mx-auto">
        {{-- Header --}}
        <div class="flex flex-wrap -mx-4 mb-14 justify-between">
            <div class="w-full md:w-1/2 px-4 mb-12 md:mb-0">
                <h2 class="text-4xl font-heading font-semibold text-rhino-600 tracking-xs">Les meilleures ventes</h2>
            </div>
            <div class="w-full md:w-1/2 px-4 md:text-right">
                <button 
                    wire:click="toggleShowMore"
                    wire:loading.attr="disabled"
                    class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-purple-500 hover:text-white bg-white border border-purple-500 rounded-sm hover:bg-purple-500 transition duration-200 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="toggleShowMore">
                        <span x-show="!showContent">Voir plus</span>
                        <span x-show="showContent" x-cloak>Voir moins</span>
                    </span>
                    <span wire:loading wire:target="toggleShowMore" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Chargement...
                    </span>
                </button>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex flex-wrap -mx-4">
            @forelse($this->products as $index => $product)
                @if($index === 0)
                    {{-- Premier produit en grand (2/3 largeur) --}}
                    <div 
                        class="w-full xl:w-2/3 px-4 mb-8"
                        x-data="{ loaded: false }"
                        x-init="setTimeout(() => loaded = true, {{ $index * 100 }})"
                        x-show="loaded"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                    >
                        <div class="flex flex-wrap -mx-4">
                            {{-- Sous-carte gauche --}}
                            <div class="w-full sm:w-1/2 px-4 mb-8">
                                <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150 overflow-hidden group" href="{{ route('products.show', $product->slug) }}">
                                    @if($product->has_sale)
                                        <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                                    @elseif($product->is_new)
                                        <span class="relative z-10 inline-block py-1 px-3 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full">New</span>
                                    @endif
                                    @if($product->image)
                                        <img 
                                            class="absolute top-0 left-1/2 mt-12 transform -translate-x-1/2 max-h-36 object-contain group-hover:scale-105 transition-transform duration-300" 
                                            src="{{ $product->image }}" 
                                            alt="{{ $product->title }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2 w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="relative z-10 w-full px-8 mt-auto text-center">
                                        <span class="block text-base text-rhino-500 mb-1 line-clamp-1">{{ $product->title }}</span>
                                        @if($product->has_sale)
                                            <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                            <span class="text-base text-rhino-600 font-semibold">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="block text-base text-rhino-300">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </div>
                                </a>
                            </div>

                            {{-- Sous-carte droite (2ème produit si disponible) --}}
                            @if(isset($this->products[1]))
                                @php $product2 = $this->products[1]; @endphp
                                <div class="w-full sm:w-1/2 px-4 mb-8">
                                    <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150 overflow-hidden group" href="{{ route('products.show', $product2->slug) }}">
                                        @if($product2->is_new)
                                            <span class="relative z-10 inline-block py-1 px-3 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full">New</span>
                                        @endif
                                        @if($product2->image)
                                            <img 
                                                class="absolute top-0 left-1/2 mt-4 transform -translate-x-1/2 max-h-40 object-contain group-hover:scale-105 transition-transform duration-300" 
                                                src="{{ $product2->image }}" 
                                                alt="{{ $product2->title }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="absolute top-0 left-1/2 mt-8 transform -translate-x-1/2 w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="relative z-10 w-full px-8 mt-auto text-center">
                                            <span class="block text-base text-rhino-500 mb-1 line-clamp-1">{{ $product2->title }}</span>
                                            @if($product2->has_sale)
                                                <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product2->base_price, 0, ',', ' ') }} FCFA</span>
                                                <span class="text-base text-rhino-600 font-semibold">{{ number_format($product2->price_promo, 0, ',', ' ') }} FCFA</span>
                                            @else
                                                <span class="block text-base text-rhino-300">{{ number_format($product2->base_price, 0, ',', ' ') }} FCFA</span>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            @endif

                            {{-- Banner CTA --}}
                            <div class="w-full px-4">
                                <div class="relative rounded-xl bg-gradient-to-r from-yellow-300 to-amber-400 overflow-hidden">
                                    <div class="relative z-10 px-6 py-20 sm:py-6">
                                        <h3 class="text-4xl font-heading font-semibold mb-14">
                                            <span class="block">Nouvelle</span>
                                            <span class="block">collection</span>
                                            <span class="block">2025</span>
                                        </h3>
                                        <a class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-white hover:text-purple-500 bg-purple-500 hover:bg-white rounded-sm transition duration-200" href="{{ route('produits') }}">
                                            Voir la collection
                                        </a>
                                    </div>
                                    <img class="absolute top-0 right-0 m-2 opacity-80" src="/coleos-assets/product-blocks/arrow.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Produit vedette à droite (3ème produit) --}}
                    @if(isset($this->products[2]))
                        @php $product3 = $this->products[2]; @endphp
                        <div 
                            class="w-full xl:w-1/3 px-4 mb-8"
                            x-data="{ loaded: false }"
                            x-init="setTimeout(() => loaded = true, 200)"
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                        >
                            <a class="relative flex flex-col items-start h-128 xl:h-full py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150 overflow-hidden group" href="{{ route('products.show', $product3->slug) }}">
                                <div>
                                    @if($product3->is_new)
                                        <span class="relative z-10 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full">New</span>
                                    @endif
                                    @if($product3->has_sale)
                                        <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                                    @endif
                                </div>
                                @if($product3->image)
                                    <img 
                                        class="absolute top-0 left-1/2 mt-5 transform -translate-x-1/2 h-96 object-contain group-hover:scale-105 transition-transform duration-300" 
                                        src="{{ $product3->image }}" 
                                        alt="{{ $product3->title }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="absolute top-0 left-1/2 mt-20 transform -translate-x-1/2 w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="relative z-10 w-full mt-auto">
                                    <span class="block text-base text-rhino-500 mb-1 line-clamp-2">{{ $product3->title }}</span>
                                    @if($product3->has_sale)
                                        <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product3->base_price, 0, ',', ' ') }} FCFA</span>
                                        <span class="text-base text-rhino-600 font-semibold">{{ number_format($product3->price_promo, 0, ',', ' ') }} FCFA</span>
                                    @else
                                        <span class="block text-base text-rhino-300">{{ number_format($product3->base_price, 0, ',', ' ') }} FCFA</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endif

                @elseif($index > 2)
                    {{-- Produits supplémentaires (affichés avec showMore) --}}
                    <div 
                        class="w-full xl:w-1/3 px-4 mb-8"
                        x-show="showContent"
                        x-transition:enter="transition ease-out duration-300 delay-{{ ($index - 3) * 100 }}"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-cloak
                    >
                        <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150 overflow-hidden group" href="{{ route('products.show', $product->slug) }}">
                            @if($product->has_sale)
                                <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                            @elseif($product->is_new)
                                <span class="relative z-10 inline-block py-1 px-3 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full">New</span>
                            @endif
                            @if($product->image)
                                <img 
                                    class="absolute top-0 left-1/2 mt-12 transform -translate-x-1/2 max-h-36 object-contain group-hover:scale-105 transition-transform duration-300" 
                                    src="{{ $product->image }}" 
                                    alt="{{ $product->title }}"
                                    loading="lazy"
                                >
                            @else
                                <div class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2 w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                            <div class="relative z-10 w-full px-8 mt-auto text-center">
                                <span class="block text-base text-rhino-500 mb-1 line-clamp-1">{{ $product->title }}</span>
                                @if($product->has_sale)
                                    <span class="text-sm text-gray-400 line-through mr-2">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-base text-rhino-600 font-semibold">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                @else
                                    <span class="block text-base text-rhino-300">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </div>
                        </a>
                    </div>
                @endif
            @empty
                {{-- État vide --}}
                <div class="w-full text-center py-12">
                    <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg">Aucun produit disponible pour le moment.</p>
                    <a href="{{ route('produits') }}" class="inline-block mt-4 text-purple-500 hover:text-purple-600 font-medium">
                        Découvrir tous les produits →
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>










