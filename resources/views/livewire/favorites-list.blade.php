<?php

use Livewire\Volt\Component;
use App\Models\Favorite;
use App\Models\Product;

new class extends Component {
    public array $favorites = [];
    public int $perPage = 12;
    public int $currentPage = 1;
    public string $sortBy = 'newest';

    public function mount()
    {
        $this->loadFavorites();
    }

    public function loadFavorites()
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $favoritesQuery = Favorite::query()
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->where('favoritable_type', Product::class)
            ->with(['favoritable' => function ($q) {
                $q->with(['media' => function ($m) { $m->orderBy('order_column'); }]);
            }])
            ->get();

        // Extract products from favorites
        $products = $favoritesQuery->pluck('favoritable')
            ->filter()
            ->map(function ($product) {
                // Check if product is new (created in last 30 days)
                $isNew = $product->created_at->diffInDays(now()) <= 30;
                
                // Resolve image URL (same logic as product-detail)
                $media = $product->media->first();
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
                
                return (object) [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'base_price' => $product->base_price,
                    'price_promo' => $product->price_promo,
                    'created_at' => $product->created_at,
                    'isNew' => $isNew,
                    'image_url' => $imageUrl,
                ];
            })
            ->values()
            ->toArray();

        // Sort products
        $products = $this->sortProducts($products);

        $this->favorites = $products;
    }

    private function sortProducts($products)
    {
        return match($this->sortBy) {
            'oldest' => collect($products)->sortBy('created_at')->toArray(),
            'price_asc' => collect($products)
                ->sortBy(fn($p) => $p->price_promo ?? $p->base_price)
                ->toArray(),
            'price_desc' => collect($products)
                ->sortByDesc(fn($p) => $p->price_promo ?? $p->base_price)
                ->toArray(),
            default => collect($products)->sortByDesc('created_at')->toArray(),
        };
    }

    public function changeSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->loadFavorites();
    }

    #[\Livewire\Attributes\Computed]
    public function paginatedFavorites()
    {
        $start = ($this->currentPage - 1) * $this->perPage;
        $items = array_slice($this->favorites, $start, $this->perPage);

        return [
            'items' => $items,
            'total' => count($this->favorites),
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => ceil(count($this->favorites) / $this->perPage) ?: 1,
            'from' => count($this->favorites) > 0 ? $start + 1 : 0,
            'to' => min($start + $this->perPage, count($this->favorites)),
        ];
    }

    #[\Livewire\Attributes\Computed]
    public function totalFavorites()
    {
        return count($this->favorites);
    }

    public function goToPage($page)
    {
        $lastPage = $this->paginatedFavorites()['last_page'];
        $this->currentPage = max(1, min($page, max(1, $lastPage)));
    }
};

?>

<section class="py-12">
    <div class="container px-4 mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-0 justify-between flex-wrap mb-6">
            <div>
                <h1 class="font-heading text-rhino-700 text-2xl font-semibold">{{ $this->totalFavorites() }} produit{{ $this->totalFavorites() !== 1 ? 's' : '' }} en favoris trouvé{{ $this->totalFavorites() !== 1 ? 's' : '' }}</h1>
                <p class="text-rhino-300">Vos produits préférés sauvegardés</p>
            </div>

            <!-- Toolbar -->
            <div class="flex gap-4 flex-wrap">
                <a class="rounded-sm border border-coolGray-200 py-2 px-4 flex items-center flex-wrap justify-between gap-12 hover:bg-coolGray-100 transition duration-200" href="#">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 17.5L10 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M5 17.5L5 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15 17.5L15 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <ellipse cx="5.00004" cy="7.49992" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                            <ellipse cx="15" cy="7.49992" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                            <ellipse cx="10" cy="13.3334" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                        </svg>
                        <span class="text-sm text-coolGray-800 font-medium">Filtres</span>
                    </div>
                    <div class="bg-rhino-600 py-1 px-3 text-center rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold block">0</span>
                    </div>
                </a>

                <div class="border border-gray-200 rounded-sm flex bg-white">
                    <a class="flex-1 py-1 px-5 flex items-center justify-center bg-coolGray-100 hover:bg-coolGray-200 transition duration-200" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M12.6667 0.333252H0.666667C0.489856 0.333252 0.320286 0.40349 0.195262 0.528514C0.0702379 0.653538 0 0.823108 0 0.999919V12.9999C0 13.1767 0.0702379 13.3463 0.195262 13.4713C0.320286 13.5963 0.489856 13.6666 0.666667 13.6666H12.6667C12.8435 13.6666 13.013 13.5963 13.1381 13.4713C13.2631 13.3463 13.3333 13.1767 13.3333 12.9999V0.999919C13.3333 0.823108 13.2631 0.653538 13.1381 0.528514C13.013 0.40349 12.8435 0.333252 12.6667 0.333252ZM4 12.3333H1.33333V9.66659H4V12.3333ZM4 8.33325H1.33333V5.66659H4V8.33325ZM4 4.33325H1.33333V1.66659H4V4.33325ZM8 12.3333H5.33333V9.66659H8V12.3333ZM8 8.33325H5.33333V5.66659H8V8.33325ZM8 4.33325H5.33333V1.66659H8V4.33325ZM12 12.3333H9.33333V9.66659H12V12.3333ZM12 8.33325H9.33333V5.66659H12V8.33325ZM12 4.33325H9.33333V1.66659H12V4.33325Z" fill="currentColor"></path>
                        </svg>
                    </a>
                    <a class="flex-1 py-1 px-5 flex items-center justify-center group hover:bg-coolGray-100 transition duration-200" href="#">
                        <div class="text-coolGray-400 group-hover:text-coolGray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M12.6667 7.66659H1.00004C0.82323 7.66659 0.65366 7.73682 0.528636 7.86185C0.403612 7.98687 0.333374 8.15644 0.333374 8.33325V12.9999C0.333374 13.1767 0.403612 13.3463 0.528636 13.4713C0.65366 13.5963 0.82323 13.6666 1.00004 13.6666H12.6667C12.8435 13.6666 13.0131 13.5963 13.1381 13.4713C13.2631 13.3463 13.3334 13.1767 13.3334 12.9999V8.33325C13.3334 8.15644 13.2631 7.98687 13.1381 7.86185C13.0131 7.73682 12.8435 7.66659 12.6667 7.66659ZM12 12.3333H1.66671V8.99992H12V12.3333ZM12.6667 0.333252H1.00004C0.82323 0.333252 0.65366 0.40349 0.528636 0.528514C0.403612 0.653538 0.333374 0.823108 0.333374 0.999919V5.66659C0.333374 5.8434 0.403612 6.01297 0.528636 6.13799C0.65366 6.26301 0.82323 6.33325 1.00004 6.33325H12.6667C12.8435 6.33325 13.0131 6.26301 13.1381 6.13799C13.2631 6.01297 13.3334 5.8434 13.3334 5.66659V0.999919C13.3334 0.823108 13.2631 0.653538 13.1381 0.528514C13.0131 0.40349 12.8435 0.333252 12.6667 0.333252ZM12 4.99992H1.66671V1.66659H12V4.99992Z" fill="currentColor"></path>
                            </svg>
                        </div>
                    </a>
                </div>

                <select wire:change="changeSortBy($event.target.value)" class="rounded-sm border border-coolGray-200 py-3 px-4 text-coolGray-400 text-sm outline-none">
                    <option value="newest">Trier par: Récent</option>
                    <option value="oldest">Trier par: Ancien</option>
                    <option value="price_asc">Trier par: Prix (croissant)</option>
                    <option value="price_desc">Trier par: Prix (décroissant)</option>
                </select>
            </div>
        </div>

        <!-- Products Grid or Empty State -->
        @if($this->totalFavorites() === 0)
            <div class="text-center py-12">
                <p class="text-rhino-300 mb-6">Vous n'avez pas encore ajouté de produits à vos favoris.</p>
                <a href="/produits" class="inline-block px-6 py-3 bg-purple-500 text-white rounded-sm hover:bg-purple-600 transition duration-200">
                    Découvrir les produits
                </a>
            </div>
        @else
            <div class="flex flex-wrap -mx-4">
                @foreach($this->paginatedFavorites()['items'] as $product)
                    <div class="w-1/2 md:w-1/3 lg:w-1/4 px-2 sm:px-4">
                        <a class="block mb-10 group" href="/produit/{{ $product->slug }}">
                            <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150 overflow-hidden">
                                <!-- Badge Section -->
                                <div class="absolute left-5 top-5 z-10">
                                    @if($product->isNew)
                                        <div class="uppercase bg-orange-500 py-1 px-3 rounded-full text-white text-xs font-bold text-center">Nouveau</div>
                                    @elseif($product->price_promo && $product->price_promo < $product->base_price)
                                        <div class="uppercase bg-white py-1 px-3 rounded-full text-rhino-700 text-xs font-bold text-center">Solde</div>
                                    @endif
                                </div>

                                    <!-- Image -->
                                <img 
                                    src="{{ $product->image_url }}" 
                                    alt="{{ $product->title }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >

                                <!-- Hover Buttons -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-200 flex items-center justify-center gap-3">
                                    <!-- Cart Button Component -->
                                    <livewire:cart-button :product-id="$product->id" :key="'cart-'.$product->id" />

                                    <!-- Remove from Favorites Component -->
                                    <livewire:remove-from-favorites-button :product-id="$product->id" :key="'fav-'.$product->id" />
                                </div>
                            </div>

                            <!-- Product Info -->
                            <p class="text-rhino-700">{{ $product->title }}</p>
                            <p class="text-rhino-300">
                                @if($product->price_promo && $product->price_promo < $product->base_price)
                                    <span class="line-through">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-red-600 font-semibold">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                @else
                                    {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                @endif
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->paginatedFavorites()['last_page'] > 1)
                <div class="flex items-center justify-center gap-2 mt-12">
                    <button 
                        wire:click="goToPage({{ max(1, $this->currentPage - 1) }})"
                        @disabled($this->currentPage === 1)
                        class="px-4 py-2 border border-coolGray-200 rounded-sm hover:bg-coolGray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        ← Précédent
                    </button>

                    @for($page = 1; $page <= $this->paginatedFavorites()['last_page']; $page++)
                        <button 
                            wire:click="goToPage({{ $page }})"
                            @class([
                                'px-3 py-2 rounded-sm text-sm transition',
                                'bg-purple-500 text-white' => $page === $this->currentPage,
                                'border border-coolGray-200 hover:bg-coolGray-100' => $page !== $this->currentPage,
                            ])
                        >
                            {{ $page }}
                        </button>
                    @endfor

                    <button 
                        wire:click="goToPage({{ min($this->paginatedFavorites()['last_page'], $this->currentPage + 1) }})"
                        @disabled($this->currentPage === $this->paginatedFavorites()['last_page'])
                        class="px-4 py-2 border border-coolGray-200 rounded-sm hover:bg-coolGray-100 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        Suivant →
                    </button>
                </div>
            @endif
        @endif
    </div>
</section>
