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

            // Fetch products for those ids with all media
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

            // Map to payload with all images
            $payload = collect($ordered)->map(function ($p) {
                // $p might be array or Eloquent model
                $model = is_object($p) ? $p : (object) $p;
                $mediaCollection = collect([]);
                
                if (isset($model->media) && is_iterable($model->media)) {
                    $mediaCollection = collect($model->media);
                }

                // Get all images for the product
                $images = $mediaCollection->map(function ($media) {
                    try {
                        $path = data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'));
                        return asset('storage/' . $path);
                    } catch (\Throwable $e) {
                        return null;
                    }
                })->filter()->values()->toArray();

                // If no images, use default
                if (empty($images)) {
                    $images = [asset('coleos-assets/product-blocks/product-no-bg1.png')];
                }

                return (object) [
                    'id' => $model->id ?? null,
                    'title' => $model->title ?? '',
                    'slug' => $model->slug ?? '#',
                    'base_price' => $model->base_price ?? 0,
                    'price_promo' => $model->price_promo ?? null,
                    'images' => $images, // Array of all images
                    'image' => $images[0] ?? asset('coleos-assets/product-blocks/product-no-bg1.png'), // First image for fallback
                ];
            })->take($this->limit)->toArray();

            return $payload;
        });
    }
};

?>

@push('styles')
<style>
    /* Force vert Capocop - Variables CSS Swiper selon la documentation officielle */
    :root {
        --swiper-navigation-color: #00B600;
        --swiper-pagination-color: #00B600;
    }
    
    /* Application spécifique pour le conteneur produits */
    #productsSwiper {
        --swiper-navigation-color: #00B600;
        --swiper-pagination-color: #00B600;
    }
    
    /* Swiper Products Container - Full Width */
    .swiper-products {
        padding: 2rem 0 4rem 0 !important;
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        position: relative;
    }

    /* Force wrapper to use full width */
    .swiper-products .swiper-wrapper {
        width: 100% !important;
        display: flex !important;
    }

    .swiper-products .swiper-wrapper {
        align-items: stretch;
        display: flex;
    }

    /* Calculate slide width - Swiper will handle this with slidesPerView: 'auto' */
    .swiper-products .swiper-slide {
        height: auto;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-shrink: 0;
        box-sizing: border-box;
    }

    .swiper-products .swiper-slide-active {
        z-index: 10;
    }

    /* Ensure container takes full width */
    #productsSwiper {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Fix for proper slide width calculation */
    .swiper-products .swiper-slide > * {
        width: 100%;
    }

    /* Swiper Product Images - Fade Effect */
    .swiper-product-images {
        width: 100%;
        height: 100%;
    }

    .swiper-product-images .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .swiper-product-images .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .swiper-product-images:hover .swiper-slide-active img {
        transform: scale(1.05);
    }

    /* Navigation Buttons - Vert Capocop - Ultra spécifique */
    #productsSwiper .swiper-button-next,
    #productsSwiper .swiper-button-prev,
    .swiper-products .swiper-button-next,
    .swiper-products .swiper-button-prev,
    .swiper-button-next,
    .swiper-button-prev {
        color: #00B600 !important;
        background: rgba(255, 255, 255, 0.95) !important;
        width: 44px !important;
        height: 44px !important;
        border-radius: 50% !important;
        box-shadow: 0 2px 8px rgba(0, 182, 0, 0.2) !important;
        transition: all 0.3s ease !important;
        border: 2px solid rgba(0, 182, 0, 0.1) !important;
        margin-top: 0 !important;
    }

    #productsSwiper .swiper-button-next:after,
    #productsSwiper .swiper-button-prev:after,
    .swiper-products .swiper-button-next:after,
    .swiper-products .swiper-button-prev:after,
    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 20px !important;
        font-weight: bold !important;
        color: #00B600 !important;
    }

    #productsSwiper .swiper-button-next:hover,
    #productsSwiper .swiper-button-prev:hover,
    .swiper-products .swiper-button-next:hover,
    .swiper-products .swiper-button-prev:hover,
    .swiper-button-next:hover,
    .swiper-button-prev:hover {
        background: #00B600 !important;
        color: white !important;
        box-shadow: 0 4px 16px rgba(0, 182, 0, 0.4) !important;
        transform: scale(1.1) !important;
        border-color: #00B600 !important;
    }

    #productsSwiper .swiper-button-next:hover:after,
    #productsSwiper .swiper-button-prev:hover:after,
    .swiper-products .swiper-button-next:hover:after,
    .swiper-products .swiper-button-prev:hover:after,
    .swiper-button-next:hover:after,
    .swiper-button-prev:hover:after {
        color: white !important;
    }

    /* Pagination - Vert Capocop - Ultra spécifique */
    #productsSwiper .swiper-pagination-bullet,
    .swiper-products .swiper-pagination-bullet,
    .swiper-pagination-bullet {
        width: 10px !important;
        height: 10px !important;
        background: #00B600 !important;
        opacity: 0.3 !important;
        transition: all 0.3s ease !important;
    }

    #productsSwiper .swiper-pagination-bullet-active,
    .swiper-products .swiper-pagination-bullet-active,
    .swiper-pagination-bullet-active {
        opacity: 1 !important;
        width: 28px !important;
        border-radius: 5px !important;
        background: #00B600 !important;
        box-shadow: 0 2px 8px rgba(0, 182, 0, 0.4) !important;
    }

    /* Pagination for product images - Vert Capocop */
    .swiper-product-images .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.5) !important;
        opacity: 0.6 !important;
    }

    .swiper-product-images .swiper-pagination-bullet-active {
        background: #00B600 !important;
        opacity: 1 !important;
        width: 20px !important;
    }

    /* Product Card Enhancements */
    .product-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    /* Mobile Optimizations */
    @media (max-width: 640px) {
        .swiper-products {
            padding: 0.75rem 0 2.5rem 0 !important;
        }

        /* Masquer les boutons de navigation sur très petit écran, utiliser swipe tactile */
        #productsSwiper .swiper-button-next,
        #productsSwiper .swiper-button-prev,
        .swiper-products .swiper-button-next,
        .swiper-products .swiper-button-prev {
            display: none !important;
        }

        /* Pagination plus visible sur mobile */
        .swiper-pagination {
            bottom: 0.5rem !important;
        }

        .swiper-pagination-bullet {
            width: 8px !important;
            height: 8px !important;
            margin: 0 4px !important;
        }

        .swiper-pagination-bullet-active {
            width: 24px !important;
        }

        /* Cartes produits plus espacées sur mobile */
        .swiper-products .swiper-slide {
            padding-right: 0.75rem;
        }
    }

    @media (min-width: 641px) and (max-width: 768px) {
        .swiper-products {
            padding: 1rem 0 3rem 0 !important;
        }

        #productsSwiper .swiper-button-next,
        #productsSwiper .swiper-button-prev,
        .swiper-products .swiper-button-next,
        .swiper-products .swiper-button-prev,
        .swiper-button-next,
        .swiper-button-prev {
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            color: #00B600 !important;
        }

        #productsSwiper .swiper-button-next:after,
        #productsSwiper .swiper-button-prev:after,
        .swiper-products .swiper-button-next:after,
        .swiper-products .swiper-button-prev:after,
        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px !important;
            color: #00B600 !important;
        }
    }

    /* Tablet Optimizations */
    @media (min-width: 769px) and (max-width: 1024px) {
        .swiper-button-next,
        .swiper-button-prev {
            width: 40px !important;
            height: 40px !important;
        }
    }
</style>
@endpush

<section class="py-6 sm:py-8 md:py-12 bg-gray-50 w-full overflow-hidden">
    <div class="">
        <div class="text-center mb-4 sm:mb-6 md:mb-8 max-w-full mx-auto px-4 md:px-6">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-heading text-rhino-700 font-semibold mb-2 sm:mb-3 leading-tight">Découvrez notre catalogue</h2>
            <p class="text-coolGray-500 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed">Une large sélection de produits pour répondre à tous vos besoins : énergie domestique, équipements et fournitures scolaires</p>
        </div>

        <!-- Swiper Products Container - Full Width -->
        <div class="swiper swiper-products w-full" id="productsSwiper">
            <div class="swiper-wrapper">
                @foreach($this->products as $index => $product)
                @php
                    $hasPromo = !empty($product->price_promo) && $product->price_promo < $product->base_price;
                    $discountPercent = $hasPromo ? round((($product->base_price - $product->price_promo) / $product->base_price) * 100) : 0;
                        $images = $product->images ?? [$product->image ?? asset('coleos-assets/product-blocks/product-no-bg1.png')];
                @endphp
                    
                    <div class="swiper-slide">
                        <a href="{{ route('products.show', $product->slug) }}" class="block h-full product-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col">
                            
                            <!-- Swiper Product Images Container -->
                            <div class="relative h-48 sm:h-52 md:h-48 lg:h-56 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <div class="swiper swiper-product-images product-images-{{ $index }}" style="width: 100%; height: 100%;">
                                    <div class="swiper-wrapper">
                                        @foreach($images as $image)
                                            <div class="swiper-slide">
                                                <img 
                                                    class="w-full h-full object-contain p-4 sm:p-5 md:p-4" 
                                                    src="{{ $image }}" 
                                                    alt="{{ $product->title }} - Image {{ $loop->iteration }}"
                                                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                                >
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(count($images) > 1)
                                        <!-- Pagination for product images -->
                                        <div class="swiper-pagination product-images-pagination-{{ $index }}"></div>
                                    @endif
                                </div>
                                
                                <!-- Badges -->
                                @if($hasPromo)
                                    <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 md:top-3 md:left-3 bg-white rounded-full px-2.5 py-1.5 sm:px-3 sm:py-1.5 md:px-3 md:py-1.5 shadow-lg z-20">
                                        <span class="text-xs sm:text-xs md:text-xs font-bold text-rhino-700 uppercase">Sale</span>
                                    </div>
                                    
                                    <div class="absolute top-2.5 right-2.5 sm:top-3 sm:right-3 md:top-3 md:right-3 bg-orange-500 text-white px-2.5 py-1.5 sm:px-3 sm:py-1.5 md:px-3 md:py-1.5 rounded-md shadow-lg z-20">
                                        <span class="text-xs sm:text-xs md:text-xs font-bold">-{{ $discountPercent }}%</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Info -->
                            <div class="p-4 sm:p-4 md:p-4 bg-white flex flex-col flex-grow">
                                <h3 class="text-sm sm:text-sm md:text-sm font-semibold text-rhino-700 mb-2.5 sm:mb-3 line-clamp-2 min-h-[2.5rem] sm:min-h-[2.5rem] md:min-h-[2.5rem] group-hover:text-purple-600 transition-colors leading-snug">
                                {{ $product->title }}
                            </h3>
                            
                                <!-- Pricing -->
                                <div class="flex flex-col gap-1 sm:gap-1 md:gap-1 min-h-[2.5rem] sm:min-h-[2.5rem] md:min-h-[2.5rem] justify-end">
                                @if($hasPromo)
                                        <span class="text-xs sm:text-xs md:text-xs text-gray-400 line-through">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                        <span class="text-base sm:text-base md:text-lg font-bold text-rhino-900">
                                        {{ number_format($product->price_promo, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                        <span class="text-base sm:text-base md:text-lg font-bold text-rhino-900">
                                        {{ number_format($product->base_price, 0, ',', ' ') }} FCFA
                                    </span>
                                        <span class="text-xs sm:text-xs md:text-xs opacity-0">Placeholder</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
            </div>

            <!-- Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function() {
        // Ajouter un style global pour forcer le vert
        const style = document.createElement('style');
        style.id = 'capocop-swiper-green';
        style.textContent = `
            #productsSwiper .swiper-button-next,
            #productsSwiper .swiper-button-prev {
                color: #00B600 !important;
            }
            #productsSwiper .swiper-button-next::after,
            #productsSwiper .swiper-button-prev::after {
                color: #00B600 !important;
            }
            #productsSwiper .swiper-pagination-bullet {
                background: #00B600 !important;
            }
            #productsSwiper .swiper-pagination-bullet-active {
                background: #00B600 !important;
            }
        `;
        document.head.appendChild(style);

        // Fonction pour forcer le vert
        function applyCapocopGreen() {
            const swiper = document.getElementById('productsSwiper');
            if (!swiper) return;

            // Navigation buttons
            const nextBtn = swiper.querySelector('.swiper-button-next');
            const prevBtn = swiper.querySelector('.swiper-button-prev');
            
            [nextBtn, prevBtn].forEach(btn => {
                if (btn) {
                    btn.style.setProperty('color', '#00B600', 'important');
                    btn.style.setProperty('border-color', 'rgba(0, 182, 0, 0.1)', 'important');
                }
            });

            // Pagination bullets
            const bullets = swiper.querySelectorAll('.swiper-pagination-bullet');
            bullets.forEach(bullet => {
                bullet.style.setProperty('background', '#00B600', 'important');
            });
        }

        // Appliquer immédiatement si DOM est prêt
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                applyCapocopGreen();
                setTimeout(applyCapocopGreen, 500);
                setTimeout(applyCapocopGreen, 1000);
            });
        } else {
            applyCapocopGreen();
            setTimeout(applyCapocopGreen, 500);
            setTimeout(applyCapocopGreen, 1000);
        }
    })();
</script>
@endpush

