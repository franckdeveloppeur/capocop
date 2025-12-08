<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;

new class extends Component {
    // Filter states
    public array $selectedCategories = [];
    public array $selectedTags = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public string $sortBy = 'newest';
    public int $perPage = 12;
    public int $currentPage = 1;

    // Available filters (cached on mount)
    public $categories = [];
    public $tags = [];
    public float $priceRange = 798;

    public function mount()
    {
        // Load filter options
        $this->categories = Category::select('id', 'name', 'slug')
            ->whereHas('products', function ($q) {
                $q->where('status', 'active');
            })
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
            ])
            ->toArray();

        // Load tags used by active products
        $this->tags = \DB::table('tags')
            ->join('taggables', 'tags.id', '=', 'taggables.tag_id')
            ->join('products', 'products.id', '=', 'taggables.taggable_id')
            ->where('taggables.taggable_type', \App\Models\Product::class)
            ->where('products.status', 'active')
            ->select('tags.id', 'tags.name')
            ->distinct()
            ->get()
            ->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])
            ->toArray();

        // Get max price from database for range
        $maxDbPrice = Product::where('status', 'active')
            ->selectRaw('MAX(COALESCE(price_promo, base_price)) as max_price')
            ->value('max_price');
        
        $this->priceRange = (float) ($maxDbPrice ?? 798);
    }

    /**
     * Computed: Get filtered and sorted products
     */
    #[\Livewire\Attributes\Computed]
    public function products()
    {
        $query = Product::query()
            ->with(['media' => fn($q) => $q->orderBy('order_column')])
            ->published();

        // Apply filters
        if (!empty($this->selectedCategories)) {
            $query->byCategories($this->selectedCategories);
        }

        if (!empty($this->selectedTags)) {
            $query->byTags($this->selectedTags);
        }

        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->byPriceRange($this->minPrice, $this->maxPrice);
        }

        // Apply sort
        $query->sortBy($this->sortBy);

        $paginator = $query->paginate($this->perPage, ['*'], 'page', $this->currentPage);

        // Return serializable data
        return [
            'items' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    /**
     * Computed: Total products count
     */
    #[\Livewire\Attributes\Computed]
    public function totalProducts()
    {
        return Product::query()
            ->published()
            ->when(!empty($this->selectedCategories), function ($q) {
                $q->byCategories($this->selectedCategories);
            })
            ->when(!empty($this->selectedTags), function ($q) {
                $q->byTags($this->selectedTags);
            })
            ->when($this->minPrice !== null || $this->maxPrice !== null, function ($q) {
                $q->byPriceRange($this->minPrice, $this->maxPrice);
            })
            ->count();
    }

    // Actions - Event Listeners
    #[\Livewire\Attributes\On('toggle-category')]
    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_values(
                array_diff($this->selectedCategories, [$categoryId])
            );
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->currentPage = 1;
    }

    #[\Livewire\Attributes\On('toggle-tag')]
    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(
                array_diff($this->selectedTags, [$tagId])
            );
        } else {
            $this->selectedTags[] = $tagId;
        }
        $this->currentPage = 1;
    }

    #[\Livewire\Attributes\On('update-price')]
    public function updatePrice($min, $max)
    {
        $this->minPrice = (float) $min;
        $this->maxPrice = (float) $max;
        $this->currentPage = 1;
    }

    #[\Livewire\Attributes\On('clear-filters')]
    public function handleClearFilters()
    {
        $this->selectedCategories = [];
        $this->selectedTags = [];
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->sortBy = 'newest';
        $this->currentPage = 1;
    }

    #[\Livewire\Attributes\On('go-to-page')]
    public function handleGoToPage($page)
    {
        $this->currentPage = $page;
    }
};

?>

<div class="pt-12 bg-white">
    <div class="container px-4 mx-auto">
        <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-0 justify-between flex-wrap mb-6">
            <div>
                <h1 class="font-heading text-rhino-700 text-2xl font-semibold">Les produits disponibles</h1>
                <p class="text-rhino-300">
                    <span wire:key="total-products">{{ $this->totalProducts }}</span> produits trouvés
                </p>
            </div>

            <div class="flex gap-4 flex-wrap">
                <!-- Sort Dropdown -->
                <select wire:model.live="sortBy" class="rounded-sm border border-coolGray-200 py-3 px-4 text-coolGray-400 text-sm outline-none">
                    <option value="newest">Plus récent</option>
                    <option value="oldest">Plus ancien</option>
                    <option value="price_asc">Prix: faible à élevé</option>
                    <option value="price_desc">Prix: élevé à faible</option>
                    <option value="popular">Populaire</option>
                </select>

                <!-- View Toggle (Alpine) -->
                <div class="border border-gray-200 rounded-sm flex" x-data="{ view: 'grid' }">
                    <a @click="view = 'grid'" :class="{ 'bg-coolGray-100': view === 'grid' }" class="flex-1 py-1 px-4 flex items-center justify-center hover:bg-coolGray-200 transition duration-200 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewbox="0 0 14 14" fill="none">
                            <path d="M12.6667 0.333252H0.666667C0.489856 0.333252 0.320286 0.40349 0.195262 0.528514C0.0702379 0.653538 0 0.823108 0 0.999919V12.9999C0 13.1767 0.0702379 13.3463 0.195262 13.4713C0.320286 13.5963 0.489856 13.6666 0.666667 13.6666H12.6667C12.8435 13.6666 13.013 13.5963 13.1381 13.4713C13.2631 13.3463 13.3333 13.1767 13.3333 12.9999V0.999919C13.3333 0.823108 13.2631 0.653538 13.1381 0.528514C13.013 0.40349 12.8435 0.333252 12.6667 0.333252ZM4 12.3333H1.33333V9.66659H4V12.3333ZM4 8.33325H1.33333V5.66659H4V8.33325ZM4 4.33325H1.33333V1.66659H4V4.33325ZM8 12.3333H5.33333V9.66659H8V12.3333ZM8 8.33325H5.33333V5.66659H8V8.33325ZM8 4.33325H5.33333V1.66659H8V4.33325ZM12 12.3333H9.33333V9.66659H12V12.3333ZM12 8.33325H9.33333V5.66659H12V8.33325ZM12 4.33325H9.33333V1.66659H12V4.33325Z" fill="currentColor"></path>
                        </svg>
                    </a>
                    <a @click="view = 'list'" :class="{ 'bg-coolGray-100': view === 'list' }" class="flex-1 py-1 px-4 flex items-center justify-center group hover:bg-coolGray-200 transition duration-200 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewbox="0 0 14 14" fill="none">
                            <path d="M0.916667 2.33325H13.0833C13.2741 2.33325 13.4167 2.19067 13.4167 2.01659V1.31659C13.4167 1.1425 13.2741 0.999919 13.0833 0.999919H0.916667C0.725833 0.999919 0.583333 1.14325 0.583333 1.31659V2.01659C0.583333 2.19067 0.725833 2.33325 0.916667 2.33325ZM0.916667 8.49992H13.0833C13.2741 8.49992 13.4167 8.35733 13.4167 8.18325V7.48325C13.4167 7.30917 13.2741 7.16659 13.0833 7.16659H0.916667C0.725833 7.16659 0.583333 7.30992 0.583333 7.48325V8.18325C0.583333 8.35733 0.725833 8.49992 0.916667 8.49992ZM0.916667 14.6666H13.0833C13.2741 14.6666 13.4167 14.524 13.4167 14.3499V13.6499C13.4167 13.4758 13.2741 13.3333 13.0833 13.3333H0.916667C0.725833 13.3333 0.583333 13.4766 0.583333 13.6499V14.3499C0.583333 14.524 0.725833 14.6666 0.916667 14.6666Z" fill="currentColor"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap -mx-4">
            <!-- Filters Sidebar -->
            <livewire:products-filters
                :categories="$categories"
                :tags="$tags"
                :selected-categories="$selectedCategories"
                :selected-tags="$selectedTags"
                :min-price="$minPrice"
                :max-price="$maxPrice"
                :price-range="$priceRange"
                wire:key="filters-{{ implode(',', $selectedCategories) }}-{{ implode(',', $selectedTags) }}"
            />

            <!-- Products Grid -->
            <livewire:products-grid
                :products="$this->products"
                :total-products="$this->totalProducts"
                wire:key="grid-{{ implode(',', $selectedCategories) }}-{{ implode(',', $selectedTags) }}-{{ $minPrice }}-{{ $maxPrice }}-{{ $currentPage }}-{{ $sortBy }}"
            />
        </div>
    </div>
</div>
