<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;

new class extends Component {
    public array $products = [];
    public int $limit = 16;

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $cacheKey = 'featured_products:limit:'.$this->limit;

        $this->products = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            // 1) try get best selling product ids (group by OrderItem)
            $topIds = [];
            try {
                $topIds = OrderItem::select('product_id', \DB::raw('SUM(quantity) as total'))
                    ->groupBy('product_id')
                    ->orderByDesc('total')
                    ->limit($this->limit)
                    ->pluck('product_id')
                    ->toArray();
            } catch (\Throwable $e) {
                // if OrderItem/table not present or error, ignore and fallback
                $topIds = [];
            }

            // Fetch products for those ids
            $productsMap = Product::whereIn('id', $topIds)
                ->where('status', 'active')
                ->with(['media' => function ($q) { $q->orderBy('order_column'); }])
                ->get()
                ->keyBy('id');

            $ordered = collect($topIds)
                ->map(fn($id) => $productsMap->get($id))
                ->filter()
                ->values()
                ->toArray();

            // If not enough, fetch recent active products excluding already included
            $needed = $this->limit - count($ordered);
            if ($needed > 0) {
                $extra = Product::where('status', 'active')
                    ->when(count($topIds) > 0, fn($q) => $q->whereNotIn('id', $topIds))
                    ->with(['media' => function ($q) { $q->orderBy('order_column'); }])
                    ->latest()
                    ->limit($needed)
                    ->get()
                    ->toArray();

                $ordered = array_merge($ordered, $extra);
            }

            // Map to small payload and resolve image url using same logic as product-detail
            $payload = collect($ordered)->map(function ($p) {
                // $p might be array or Eloquent model
                $model = is_object($p) ? $p : (object) $p;
                $media = null;
                if (isset($model->media) && is_iterable($model->media)) {
                    $media = collect($model->media)->first();
                }

                if ($media) {
                    try {
                        $path = data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'));
                        $image = asset('storage/' . $path);
                    } catch (\Throwable $e) {
                        $image = asset('coleos-assets/product-blocks/product-no-bg1.png');
                    }
                } else {
                    $image = asset('coleos-assets/product-blocks/product-no-bg1.png');
                }

                return (object) [
                    'id' => $model->id ?? null,
                    'title' => $model->title ?? '',
                    'slug' => $model->slug ?? '#',
                    'base_price' => $model->base_price ?? 0,
                    'price_promo' => $model->price_promo ?? null,
                    'image' => $image,
                ];
            })->take($this->limit)->toArray();

            return $payload;
        });
    }
};

?>

<section class="py-12">
    <div class="container mx-auto px-4 md:px-6 max-w-[95%] xl:max-w-[98%]">
        <div class="text-center mb-8">
            <h2 class="text-4xl md:text-5xl font-heading text-rhino-700 font-semibold">Découvrez notre catalogue</h2>
            <p class="text-coolGray-500">Une large sélection de produits pour répondre à tous vos besoins : énergie domestique, équipements et fournitures scolaires</p>
        </div>

        <div class="flex flex-wrap -mx-2 md:-mx-4">
            @foreach($this->products as $product)
                @php
                    $hasPromo = !empty($product->price_promo) && $product->price_promo < $product->base_price;
                    $discountPercent = $hasPromo ? round((($product->base_price - $product->price_promo) / $product->base_price) * 100) : 0;
                @endphp
                <div class="w-1/2 md:w-1/3 lg:w-1/4 px-1 md:px-2 lg:px-4 pb-3 md:pb-6">
                    <!-- Version Mobile (xs à lg) -->
                    <a href="{{ route('products.show', $product->slug) }}" class="block lg:hidden h-full bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden group flex flex-col">
                        <!-- Image Container -->
                        <div class="relative h-40 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img 
                                class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-300" 
                                src="{{ $product->image }}" 
                                alt="{{ $product->title }}" 
                                loading="lazy"
                            >
                            
                            <!-- Badge Sale (gauche) -->
                            @if($hasPromo)
                                <div class="absolute top-2 left-2 bg-white rounded-full px-2 py-1 shadow-sm z-10">
                                    <span class="text-[10px] font-bold text-rhino-700 uppercase">Sale</span>
                                </div>
                                
                                <!-- Badge Discount Percentage (droite) -->
                                <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded-md shadow-sm z-10">
                                    <span class="text-[10px] font-bold">-{{ $discountPercent }}%</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info - SÉPARÉ de l'image -->
                        <div class="p-3 bg-white flex flex-col flex-grow">
                            <h3 class="text-xs font-semibold text-rhino-700 mb-2 line-clamp-2 min-h-[2rem] group-hover:text-purple-600 transition-colors">
                                {{ $product->title }}
                            </h3>
                            
                            <!-- Pricing - Hauteur fixe pour uniformiser -->
                            <div class="flex flex-col gap-0.5 min-h-[2rem] justify-end">
                                @if($hasPromo)
                                    <span class="text-[10px] text-gray-400 line-through">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                    <span class="text-sm font-bold text-rhino-900">
                                        {{ number_format($product->price_promo, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                    <span class="text-sm font-bold text-rhino-900">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                    <!-- Espace invisible pour maintenir la hauteur -->
                                    <span class="text-[10px] opacity-0">Placeholder</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    
                    <!-- Version Desktop (lg et plus) -->
                    <a href="{{ route('products.show', $product->slug) }}" class="hidden lg:block h-full bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden group flex flex-col">
                        <!-- Image Container -->
                        <div class="relative h-48 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <img 
                                class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300" 
                                src="{{ $product->image }}" 
                                alt="{{ $product->title }}" 
                                loading="lazy"
                            >
                            
                            <!-- Badge Sale (gauche) -->
                            @if($hasPromo)
                                <div class="absolute top-3 left-3 bg-white rounded-full px-3 py-1 shadow-sm z-10">
                                    <span class="text-xs font-bold text-rhino-700 uppercase">Sale</span>
                                </div>
                                
                                <!-- Badge Discount Percentage (droite) -->
                                <div class="absolute top-3 right-3 bg-orange-500 text-white px-3 py-1 rounded-md shadow-sm z-10">
                                    <span class="text-xs font-bold">-{{ $discountPercent }}%</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info - SÉPARÉ de l'image -->
                        <div class="p-4 bg-white flex flex-col flex-grow">
                            <h3 class="text-sm font-semibold text-rhino-700 mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-purple-600 transition-colors">
                                {{ $product->title }}
                            </h3>
                            
                            <!-- Pricing - Hauteur fixe pour uniformiser -->
                            <div class="flex flex-col gap-1 min-h-[2.5rem] justify-end">
                                @if($hasPromo)
                                    <span class="text-xs text-gray-400 line-through">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                    <span class="text-lg font-bold text-rhino-900">
                                        {{ number_format($product->price_promo, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                    <span class="text-lg font-bold text-rhino-900">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                    <!-- Espace invisible pour maintenir la hauteur -->
                                    <span class="text-xs opacity-0">Placeholder</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
