<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    #[Url(as: 'q')]
    public string $query = '';
    
    #[Url]
    public ?string $category = null;
    
    #[Url]
    public string $sort = 'relevance';
    
    #[Url]
    public ?float $minPrice = null;
    
    #[Url]
    public ?float $maxPrice = null;

    public int $perPage = 12;
    public int $currentPage = 1;
    public array $categories = [];
    public float $priceRange = 500000;

    public function mount()
    {
        $this->loadCategories();
        $this->loadPriceRange();
    }

    public function loadCategories()
    {
        $this->categories = Cache::remember('search:categories', now()->addMinutes(30), function () {
            return Category::whereHas('products', fn($q) => $q->where('status', 'active'))
                ->withCount(['products' => fn($q) => $q->where('status', 'active')])
                ->orderBy('name')
                ->get()
                ->map(fn($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'count' => $cat->products_count,
                ])
                ->toArray();
        });
    }

    public function loadPriceRange()
    {
        $this->priceRange = Cache::remember('search:price_range', now()->addMinutes(30), function () {
            return (float) Product::where('status', 'active')
                ->selectRaw('MAX(COALESCE(price_promo, base_price)) as max_price')
                ->value('max_price') ?? 500000;
        });
    }

    #[\Livewire\Attributes\Computed]
    public function results()
    {
        $query = Product::published()
            ->with(['media' => fn($q) => $q->orderBy('order_column')->limit(1), 'categories:id,name']);

        // Recherche par mot-clé
        if (!empty($this->query)) {
            $query->searchWithRelevance($this->query);
        }

        // Filtre par catégorie
        if ($this->category) {
            $query->byCategories([$this->category]);
        }

        // Filtre par prix
        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->where(function ($q) {
                if ($this->minPrice !== null) {
                    $q->where(function ($sq) {
                        $sq->where('price_promo', '>=', $this->minPrice)
                           ->orWhere(function ($ssq) {
                               $ssq->whereNull('price_promo')
                                   ->where('base_price', '>=', $this->minPrice);
                           });
                    });
                }
                if ($this->maxPrice !== null) {
                    $q->where(function ($sq) {
                        $sq->where('price_promo', '<=', $this->maxPrice)
                           ->orWhere(function ($ssq) {
                               $ssq->whereNull('price_promo')
                                   ->where('base_price', '<=', $this->maxPrice);
                           });
                    });
                }
            });
        }

        // Tri (si pas de recherche par relevance)
        if ($this->sort !== 'relevance' || empty($this->query)) {
            $query->sortBy($this->sort === 'relevance' ? 'newest' : $this->sort);
        }

        $paginator = $query->paginate($this->perPage, ['*'], 'page', $this->currentPage);

        return [
            'items' => collect($paginator->items())->map(function ($product) {
                $media = $product->media->first();
                $image = $media 
                    ? asset('storage/' . (data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'))))
                    : null;

                return (object) [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'base_price' => (float) $product->base_price,
                    'price_promo' => $product->price_promo ? (float) $product->price_promo : null,
                    'image' => $image,
                    'category' => $product->categories->first()?->name,
                    'is_new' => $product->created_at->diffInDays(now()) <= 30,
                    'has_sale' => $product->price_promo && $product->price_promo < $product->base_price,
                ];
            })->toArray(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem() ?? 0,
            'to' => $paginator->lastItem() ?? 0,
        ];
    }

    #[\Livewire\Attributes\Computed]
    public function selectedCategory()
    {
        if (!$this->category) return null;
        return collect($this->categories)->firstWhere('id', $this->category);
    }

    public function setCategory($categoryId)
    {
        $this->category = $categoryId === $this->category ? null : $categoryId;
        $this->currentPage = 1;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
        $this->currentPage = 1;
    }

    public function applyPriceFilter($min, $max)
    {
        $this->minPrice = $min > 0 ? $min : null;
        $this->maxPrice = $max < $this->priceRange ? $max : null;
        $this->currentPage = 1;
    }

    public function clearFilters()
    {
        $this->category = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sort = 'relevance';
        $this->currentPage = 1;
    }

    public function goToPage($page)
    {
        $this->currentPage = max(1, min($page, $this->results()['last_page']));
    }
};

?>

<section 
    class="py-8 lg:py-12 bg-gray-50 min-h-screen"
    x-data="{
        showFilters: window.innerWidth >= 1024,
        minPrice: {{ $this->minPrice ?? 0 }},
        maxPrice: {{ $this->maxPrice ?? $this->priceRange }},
        priceRange: {{ $this->priceRange }},
        viewMode: 'grid',
        
        formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(price) + ' FCFA';
        },
        
        applyPriceFilter() {
            $wire.applyPriceFilter(this.minPrice, this.maxPrice);
        }
    }"
>
    <div class="container px-4 mx-auto">
        {{-- Header de recherche --}}
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-coolGray-500 mb-4">
                <a href="/" wire:navigate class="hover:text-purple-600 transition-colors">Accueil</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-rhino-600">Recherche</span>
                @if($this->query)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-purple-600 font-medium">"{{ $this->query }}"</span>
                @endif
            </nav>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="font-heading text-rhino-700 text-2xl lg:text-3xl font-semibold">
                        @if($this->query)
                            Résultats pour "{{ $this->query }}"
                        @elseif($this->selectedCategory)
                            {{ $this->selectedCategory['name'] }}
                        @else
                            Tous les produits
                        @endif
                    </h1>
                    <p class="text-coolGray-500 mt-1">
                        <span class="font-semibold text-rhino-600">{{ $this->results()['total'] }}</span> produit{{ $this->results()['total'] !== 1 ? 's' : '' }} trouvé{{ $this->results()['total'] !== 1 ? 's' : '' }}
                    </p>
                </div>

                {{-- Actions toolbar --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Toggle filtres mobile --}}
                    <button 
                        @click="showFilters = !showFilters"
                        class="lg:hidden flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-5 h-5 text-coolGray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span class="text-sm font-medium text-rhino-600">Filtres</span>
                        @if($this->category || $this->minPrice || $this->maxPrice)
                            <span class="w-5 h-5 bg-purple-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ ($this->category ? 1 : 0) + (($this->minPrice || $this->maxPrice) ? 1 : 0) }}
                            </span>
                        @endif
                    </button>

                    {{-- Mode d'affichage --}}
                    <div class="hidden sm:flex border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <button 
                            @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-purple-50 text-purple-600' : 'text-coolGray-400 hover:bg-gray-50'"
                            class="p-2.5 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            :class="viewMode === 'list' ? 'bg-purple-50 text-purple-600' : 'text-coolGray-400 hover:bg-gray-50'"
                            class="p-2.5 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Tri --}}
                    <select 
                        wire:model.live="sort"
                        class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-rhino-600 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all cursor-pointer"
                    >
                        <option value="relevance">Pertinence</option>
                        <option value="newest">Plus récent</option>
                        <option value="oldest">Plus ancien</option>
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                        <option value="popular">Popularité</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filtres (style AliExpress) --}}
            <aside 
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-4"
                class="w-full lg:w-72 flex-shrink-0"
            >
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                    {{-- Header filtres --}}
                    <div class="px-5 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-between">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtres
                        </h3>
                        @if($this->category || $this->minPrice || $this->maxPrice)
                            <button 
                                wire:click="clearFilters"
                                class="text-white/80 hover:text-white text-sm underline transition-colors"
                            >
                                Réinitialiser
                            </button>
                        @endif
                    </div>

                    {{-- Catégories --}}
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="font-semibold text-rhino-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Catégories
                        </h4>
                        <div class="space-y-1 max-h-64 overflow-y-auto">
                            @foreach($this->categories as $cat)
                                <button
                                    wire:click="setCategory('{{ $cat['id'] }}')"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-left transition-all duration-200
                                        {{ $this->category === $cat['id'] 
                                            ? 'bg-purple-100 text-purple-700 font-medium' 
                                            : 'hover:bg-gray-50 text-rhino-600' }}"
                                >
                                    <span class="text-sm">{{ $cat['name'] }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $this->category === $cat['id'] ? 'bg-purple-200 text-purple-700' : 'bg-gray-100 text-coolGray-500' }}">
                                        {{ $cat['count'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Filtre Prix --}}
                    <div class="p-5">
                        <h4 class="font-semibold text-rhino-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Prix
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <label class="text-xs text-coolGray-500 mb-1 block">Min</label>
                                    <input 
                                        type="number" 
                                        x-model.number="minPrice"
                                        min="0"
                                        :max="maxPrice"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 outline-none transition-all"
                                        placeholder="0"
                                    >
                                </div>
                                <span class="text-coolGray-400 mt-5">—</span>
                                <div class="flex-1">
                                    <label class="text-xs text-coolGray-500 mb-1 block">Max</label>
                                    <input 
                                        type="number" 
                                        x-model.number="maxPrice"
                                        :min="minPrice"
                                        :max="priceRange"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 outline-none transition-all"
                                        :placeholder="priceRange"
                                    >
                                </div>
                            </div>
                            
                            {{-- Range slider --}}
                            <div class="px-1">
                                <input 
                                    type="range" 
                                    x-model="maxPrice"
                                    min="0"
                                    :max="priceRange"
                                    step="1000"
                                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                >
                                <div class="flex justify-between text-xs text-coolGray-400 mt-1">
                                    <span>0 FCFA</span>
                                    <span x-text="formatPrice(priceRange)"></span>
                                </div>
                            </div>

                            <button 
                                @click="applyPriceFilter()"
                                class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-sm hover:shadow"
                            >
                                Appliquer le filtre
                            </button>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Grille de résultats --}}
            <main class="flex-1">
                {{-- Filtres actifs --}}
                @if($this->category || $this->minPrice || $this->maxPrice)
                    <div class="flex flex-wrap items-center gap-2 mb-6">
                        <span class="text-sm text-coolGray-500">Filtres actifs :</span>
                        
                        @if($this->selectedCategory)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-100 text-purple-700 text-sm rounded-full">
                                {{ $this->selectedCategory['name'] }}
                                <button wire:click="setCategory('{{ $this->category }}')" class="hover:text-purple-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        
                        @if($this->minPrice || $this->maxPrice)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm rounded-full">
                                {{ $this->minPrice ? number_format($this->minPrice, 0, ',', ' ') : '0' }} - {{ $this->maxPrice ? number_format($this->maxPrice, 0, ',', ' ') : number_format($this->priceRange, 0, ',', ' ') }} FCFA
                                <button wire:click="applyPriceFilter(0, {{ $this->priceRange }})" class="hover:text-indigo-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        
                        <button wire:click="clearFilters" class="text-sm text-red-500 hover:text-red-700 underline transition-colors">
                            Tout effacer
                        </button>
                    </div>
                @endif

                @if(count($this->results()['items']) > 0)
                    {{-- Grille de produits --}}
                    <div 
                        :class="viewMode === 'grid' 
                            ? 'grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6' 
                            : 'flex flex-col gap-4'"
                    >
                        @foreach($this->results()['items'] as $product)
                            <a 
                                href="/produit/{{ $product->slug }}"
                                wire:navigate
                                wire:key="product-{{ $product->id }}"
                                class="group"
                                :class="viewMode === 'list' ? 'flex bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow' : 'block'"
                            >
                                {{-- Mode Grille --}}
                                <template x-if="viewMode === 'grid'">
                                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-purple-200 transition-all duration-300">
                                        <div class="relative aspect-square bg-coolGray-100 overflow-hidden">
                                            @if($product->image)
                                                <img 
                                                    src="{{ $product->image }}" 
                                                    alt="{{ $product->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            {{-- Badges --}}
                                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                                @if($product->is_new)
                                                    <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full uppercase">New</span>
                                                @endif
                                                @if($product->has_sale)
                                                    <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                                        -{{ round((1 - $product->price_promo / $product->base_price) * 100) }}%
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Actions hover --}}
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <div class="flex gap-2">
                                                    <livewire:cart-button :product-id="$product->id" :key="'cart-'.$product->id" />
                                                    <livewire:favorite-button :product-id="$product->id" :key="'fav-'.$product->id" />
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4">
                                            @if($product->category)
                                                <p class="text-xs text-purple-500 font-medium mb-1">{{ $product->category }}</p>
                                            @endif
                                            <h3 class="text-rhino-700 font-medium line-clamp-2 group-hover:text-purple-600 transition-colors mb-2">
                                                {{ $product->title }}
                                            </h3>
                                            <div class="flex items-center gap-2">
                                                @if($product->has_sale)
                                                    <span class="text-coolGray-400 text-sm line-through">{{ number_format($product->base_price, 0, ',', ' ') }}</span>
                                                    <span class="text-lg font-bold text-purple-600">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                                @else
                                                    <span class="text-lg font-bold text-rhino-700">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Mode Liste --}}
                                <template x-if="viewMode === 'list'">
                                    <div class="flex w-full">
                                        <div class="w-32 h-32 sm:w-40 sm:h-40 bg-coolGray-100 flex-shrink-0 overflow-hidden">
                                            @if($product->image)
                                                <img 
                                                    src="{{ $product->image }}" 
                                                    alt="{{ $product->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 p-4 flex flex-col justify-between">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    @if($product->category)
                                                        <span class="text-xs text-purple-500 font-medium">{{ $product->category }}</span>
                                                    @endif
                                                    @if($product->is_new)
                                                        <span class="px-2 py-0.5 bg-orange-500 text-white text-xs font-bold rounded-full uppercase">New</span>
                                                    @endif
                                                </div>
                                                <h3 class="text-rhino-700 font-medium group-hover:text-purple-600 transition-colors">
                                                    {{ $product->title }}
                                                </h3>
                                            </div>
                                            <div class="flex items-center justify-between mt-2">
                                                <div class="flex items-center gap-2">
                                                    @if($product->has_sale)
                                                        <span class="text-coolGray-400 text-sm line-through">{{ number_format($product->base_price, 0, ',', ' ') }}</span>
                                                        <span class="text-xl font-bold text-purple-600">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                                    @else
                                                        <span class="text-xl font-bold text-rhino-700">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2">
                                                    <livewire:cart-button :product-id="$product->id" :key="'list-cart-'.$product->id" />
                                                    <livewire:favorite-button :product-id="$product->id" :key="'list-fav-'.$product->id" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($this->results()['last_page'] > 1)
                        <div class="flex flex-wrap items-center justify-center gap-2 mt-12">
                            <button 
                                wire:click="goToPage({{ max(1, $this->currentPage - 1) }})"
                                @disabled($this->currentPage === 1)
                                class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-rhino-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                ← Précédent
                            </button>

                            @php
                                $lastPage = $this->results()['last_page'];
                                $current = $this->currentPage;
                                $pages = [];
                                
                                if ($lastPage <= 7) {
                                    $pages = range(1, $lastPage);
                                } else {
                                    if ($current <= 3) {
                                        $pages = [1, 2, 3, 4, '...', $lastPage];
                                    } elseif ($current >= $lastPage - 2) {
                                        $pages = [1, '...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
                                    } else {
                                        $pages = [1, '...', $current - 1, $current, $current + 1, '...', $lastPage];
                                    }
                                }
                            @endphp

                            @foreach($pages as $page)
                                @if($page === '...')
                                    <span class="px-3 py-2 text-coolGray-400">...</span>
                                @else
                                    <button 
                                        wire:click="goToPage({{ $page }})"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                                            {{ $page === $this->currentPage 
                                                ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-sm' 
                                                : 'bg-white border border-gray-200 text-rhino-600 hover:bg-gray-50' }}"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            <button 
                                wire:click="goToPage({{ min($this->results()['last_page'], $this->currentPage + 1) }})"
                                @disabled($this->currentPage === $this->results()['last_page'])
                                class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-medium text-rhino-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                Suivant →
                            </button>
                        </div>
                    @endif
                @else
                    {{-- État vide --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-heading font-semibold text-rhino-700 mb-2">Aucun produit trouvé</h2>
                        <p class="text-coolGray-500 mb-6 max-w-md mx-auto">
                            @if($this->query)
                                Nous n'avons trouvé aucun produit correspondant à "{{ $this->query }}".
                            @else
                                Aucun produit ne correspond à vos critères de recherche.
                            @endif
                        </p>
                        <div class="flex flex-wrap justify-center gap-3">
                            @if($this->category || $this->minPrice || $this->maxPrice)
                                <button 
                                    wire:click="clearFilters"
                                    class="px-6 py-3 bg-white border border-purple-500 text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition-colors"
                                >
                                    Réinitialiser les filtres
                                </button>
                            @endif
                            <a 
                                href="/produits" 
                                wire:navigate
                                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-medium hover:from-purple-700 hover:to-indigo-700 transition-all shadow-sm hover:shadow"
                            >
                                Voir tous les produits
                            </a>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</section>





