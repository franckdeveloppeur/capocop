<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    public string $query = '';
    public array $suggestions = [];
    public array $categorySuggestions = [];
    public bool $isLoading = false;

    public function updatedQuery($value)
    {
        $this->search($value);
    }

    public function search($term)
    {
        $term = trim($term);
        
        if (strlen($term) < 2) {
            $this->suggestions = [];
            $this->categorySuggestions = [];
            return;
        }

        $this->isLoading = true;

        $cacheKey = 'search:suggestions:' . md5($term);
        
        $results = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($term) {
            $likeTerm = '%' . $term . '%';

            // Recherche de catégories
            $categories = Category::where('name', 'LIKE', $likeTerm)
                ->whereHas('products', fn($q) => $q->where('status', 'active'))
                ->select('id', 'name', 'slug')
                ->limit(5)
                ->get()
                ->map(fn($cat) => [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'count' => $cat->products()->where('status', 'active')->count(),
                ])
                ->toArray();

            // Recherche de produits
            $products = Product::published()
                ->search($term)
                ->with(['media' => fn($q) => $q->orderBy('order_column')->limit(1), 'categories:id,name'])
                ->select('id', 'title', 'slug', 'base_price', 'price_promo', 'created_at')
                ->limit(8)
                ->get()
                ->map(function ($product) {
                    $media = $product->media->first();
                    $image = $media 
                        ? asset('storage/' . (data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'))))
                        : null;

                    return [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'base_price' => (float) $product->base_price,
                        'price_promo' => $product->price_promo ? (float) $product->price_promo : null,
                        'image' => $image,
                        'category' => $product->categories->first()?->name,
                        'is_new' => $product->created_at->diffInDays(now()) <= 30,
                    ];
                })
                ->toArray();

            return [
                'categories' => $categories,
                'products' => $products,
            ];
        });

        $this->categorySuggestions = $results['categories'];
        $this->suggestions = $results['products'];
        $this->isLoading = false;
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->suggestions = [];
        $this->categorySuggestions = [];
    }

    public function goToResults()
    {
        if (strlen(trim($this->query)) >= 2) {
            return $this->redirect(route('search', ['q' => $this->query]), navigate: true);
        }
    }
};

?>

<div 
    x-data="{
        open: false,
        query: @entangle('query'),
        suggestions: @entangle('suggestions'),
        categorySuggestions: @entangle('categorySuggestions'),
        isLoading: @entangle('isLoading'),
        selectedIndex: -1,
        
        init() {
            // Fermer quand on clique ailleurs
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) {
                    this.open = false;
                }
            });
        },
        
        handleInput() {
            this.open = this.query.length >= 2;
            this.selectedIndex = -1;
        },
        
        handleKeydown(e) {
            const totalItems = this.suggestions.length + this.categorySuggestions.length;
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this.selectedIndex = Math.min(this.selectedIndex + 1, totalItems - 1);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (this.selectedIndex >= 0) {
                        this.selectItem(this.selectedIndex);
                    } else if (this.query.length >= 2) {
                        this.goToResults();
                    }
                    break;
                case 'Escape':
                    this.open = false;
                    this.selectedIndex = -1;
                    break;
            }
        },
        
        selectItem(index) {
            // Les catégories sont en premier
            if (index < this.categorySuggestions.length) {
                const cat = this.categorySuggestions[index];
                Livewire.navigate('/recherche?category=' + cat.id);
            } else {
                const productIndex = index - this.categorySuggestions.length;
                const product = this.suggestions[productIndex];
                Livewire.navigate('/produit/' + product.slug);
            }
            this.open = false;
        },
        
        goToResults() {
            if (this.query.length >= 2) {
                Livewire.navigate('/recherche?q=' + encodeURIComponent(this.query));
                this.open = false;
            }
        },
        
        highlightMatch(text, query) {
            if (!query || query.length < 2) return text;
            const regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            return text.replace(regex, '<mark class=\'bg-purple-200 text-purple-800 rounded px-0.5\'>$1</mark>');
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(price) + ' FCFA';
        }
    }"
    class="relative w-full"
    @keydown="handleKeydown"
>
    {{-- Champ de recherche --}}
    <div class="flex items-center px-4 py-1 border border-coolGray-200 rounded-full bg-white focus-within:border-purple-400 focus-within:ring-2 focus-within:ring-purple-100 transition-all duration-200">
        <input 
            type="search"
            x-model="query"
            wire:model.live.debounce.300ms="query"
            @input="handleInput"
            @focus="open = query.length >= 2"
            @keydown.enter.prevent="goToResults()"
            class="h-10 w-full bg-transparent border-0 text-sm text-coolGray-700 placeholder-coolGray-400 outline-none focus:ring-0"
            placeholder="Rechercher un produit..."
            autocomplete="off"
        >
        
        {{-- Bouton clear --}}
        <button 
            x-show="query.length > 0"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            @click="query = ''; $wire.clearSearch(); open = false;"
            type="button"
            class="p-1 mr-1 text-coolGray-400 hover:text-coolGray-600 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        {{-- Loading spinner --}}
        <div x-show="isLoading" class="mr-2">
            <svg class="animate-spin h-4 w-4 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        {{-- Bouton recherche --}}
        <button 
            @click="goToResults()"
            type="button"
            class="p-2 text-coolGray-400 hover:text-purple-500 transition-colors"
            :class="{ 'text-purple-500': query.length >= 2 }"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </div>
    
    {{-- Dropdown suggestions --}}
    <div 
        x-show="open && (suggestions.length > 0 || categorySuggestions.length > 0)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50 max-h-[70vh] overflow-y-auto"
        x-cloak
    >
        <div class="flex flex-col lg:flex-row">
            {{-- Catégories (sidebar style AliExpress) --}}
            <template x-if="categorySuggestions.length > 0">
                <div class="lg:w-48 bg-gradient-to-b from-purple-50 to-white border-b lg:border-b-0 lg:border-r border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="text-xs font-bold text-purple-600 uppercase tracking-wider">Catégories</h3>
                    </div>
                    <ul class="py-2">
                        <template x-for="(cat, index) in categorySuggestions" :key="cat.id">
                            <li>
                                <a 
                                    :href="'/recherche?category=' + cat.id"
                                    wire:navigate
                                    @click="open = false"
                                    class="flex items-center justify-between px-4 py-2.5 hover:bg-purple-100 transition-colors group"
                                    :class="{ 'bg-purple-100': selectedIndex === index }"
                                >
                                    <span class="text-sm text-rhino-600 group-hover:text-purple-700" x-html="highlightMatch(cat.name, query)"></span>
                                    <span class="text-xs text-coolGray-400 bg-coolGray-100 px-2 py-0.5 rounded-full" x-text="cat.count"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
            
            {{-- Produits --}}
            <div class="flex-1">
                <template x-if="suggestions.length > 0">
                    <div>
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-rhino-500 uppercase tracking-wider">Produits</h3>
                            <button 
                                @click="goToResults()"
                                class="text-xs text-purple-500 hover:text-purple-700 font-medium transition-colors"
                            >
                                Voir tous les résultats →
                            </button>
                        </div>
                        <ul class="py-2">
                            <template x-for="(product, index) in suggestions" :key="product.id">
                                <li>
                                    <a 
                                        :href="'/produit/' + product.slug"
                                        wire:navigate
                                        @click="open = false"
                                        class="flex items-center gap-4 px-4 py-3 hover:bg-gray-50 transition-colors group"
                                        :class="{ 'bg-gray-50': selectedIndex === (categorySuggestions.length + index) }"
                                    >
                                        {{-- Image produit --}}
                                        <div class="w-14 h-14 bg-coolGray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            <template x-if="product.image">
                                                <img :src="product.image" :alt="product.title" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!product.image">
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        {{-- Infos produit --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm text-rhino-700 font-medium truncate group-hover:text-purple-600 transition-colors" x-html="highlightMatch(product.title, query)"></p>
                                                <template x-if="product.is_new">
                                                    <span class="flex-shrink-0 text-2xs bg-orange-500 text-white px-1.5 py-0.5 rounded-full font-bold uppercase">New</span>
                                                </template>
                                            </div>
                                            <template x-if="product.category">
                                                <p class="text-xs text-coolGray-400 mt-0.5" x-text="product.category"></p>
                                            </template>
                                        </div>
                                        
                                        {{-- Prix --}}
                                        <div class="text-right flex-shrink-0">
                                            <template x-if="product.price_promo && product.price_promo < product.base_price">
                                                <div>
                                                    <p class="text-xs text-coolGray-400 line-through" x-text="formatPrice(product.base_price)"></p>
                                                    <p class="text-sm text-purple-600 font-semibold" x-text="formatPrice(product.price_promo)"></p>
                                                </div>
                                            </template>
                                            <template x-if="!product.price_promo || product.price_promo >= product.base_price">
                                                <p class="text-sm text-rhino-600 font-medium" x-text="formatPrice(product.base_price)"></p>
                                            </template>
                                        </div>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
                
                {{-- Pas de produits mais catégories --}}
                <template x-if="suggestions.length === 0 && categorySuggestions.length > 0">
                    <div class="px-4 py-8 text-center">
                        <p class="text-sm text-coolGray-500">Aucun produit trouvé pour "<span class="font-medium" x-text="query"></span>"</p>
                        <p class="text-xs text-coolGray-400 mt-1">Essayez une catégorie ci-contre</p>
                    </div>
                </template>
            </div>
        </div>
        
        {{-- Footer avec lien vers résultats complets --}}
        <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-indigo-50 border-t border-gray-100">
            <button 
                @click="goToResults()"
                class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Rechercher "<span x-text="query" class="font-semibold"></span>"
            </button>
        </div>
    </div>
    
    {{-- Message si aucun résultat --}}
    <div 
        x-show="open && query.length >= 2 && !isLoading && suggestions.length === 0 && categorySuggestions.length === 0"
        x-transition
        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 p-6 text-center z-50"
        x-cloak
    >
        <div class="w-16 h-16 mx-auto mb-4 bg-coolGray-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-coolGray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-rhino-600 font-medium mb-1">Aucun résultat trouvé</p>
        <p class="text-sm text-coolGray-400">Essayez avec d'autres mots-clés</p>
    </div>
</div>










