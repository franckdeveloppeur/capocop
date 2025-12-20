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
    <div class="">
        <div class="text-center mb-8">
            <h2 class="text-4xl md:text-5xl font-heading text-rhino-700 font-semibold">Découvrez notre catalogue</h2>
            <p class="text-coolGray-500">Une large sélection de produits pour répondre à tous vos besoins : énergie domestique, équipements et fournitures scolaires</p>
        </div>

        <div class="flex flex-wrap -mx-4">
            @foreach($this->products as $product)
                <div class="w-1/2 md:w-1/3 lg:w-1/4 px-2 sm:px-4 pb-4 sm:pb-8">
                    <a href="{{ route('products.show', $product->slug) }}" class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150 group">
                        @if(!empty($product->price_promo) && $product->price_promo < $product->base_price)
                            <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                        @endif
                        <img class="absolute top-0 left-1/2 mt-5 transform -translate-x-1/2" src="{{ $product->image }}" alt="{{ $product->title }}" loading="lazy">
                        <div class="relative z-10 w-full px-8 mt-auto text-center">
                            <span class="block text-base text-rhino-500 mb-1">{{ $product->title }}</span>
                            @if(!empty($product->price_promo) && $product->price_promo < $product->base_price)
                                <span class="block text-base text-rhino-300">
                                    <span class="line-through mr-2">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-rhino-600 font-semibold">{{ number_format($product->price_promo, 0, ',', ' ') }} FCFA</span>
                                </span>
                            @else
                                <span class="block text-base text-rhino-300">{{ number_format($product->base_price, 0, ',', ' ') }} FCFA</span>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
