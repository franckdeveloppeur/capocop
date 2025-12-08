<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class SimilarProducts extends Component
{
    public Product $product;
    public int $limit;
    public $products;

    public function __construct(Product $product, $limit = 12)
    {
        $this->product = $product;
        $this->limit = (int) $limit;

        $categoryIds = $product->categories->pluck('id')->toArray();

        $cacheKey = 'similar_products:product:'.$product->id.':cats:'.implode(',', $categoryIds).':limit:'.$this->limit;

        $this->products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($product, $categoryIds) {
            $query = Product::select(['id', 'title', 'slug', 'base_price', 'price_promo', 'shop_id'])
                ->with(['media' => function ($q) { $q->orderBy('order_column'); }])
                ->where('id', '<>', $product->id)
                ->where('status', 'active');

            if (!empty($categoryIds)) {
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            } else {
                // fallback to same shop
                if ($product->shop_id) {
                    $query->where('shop_id', $product->shop_id);
                }
            }

            $results = $query->latest()->limit($this->limit)->get();

            // Map small payload with image URL resolved (cheap, done once per cache)
            return $results->map(function ($p) {
                $media = $p->media->first();
                if ($media) {
                    try {
                        $path = data_get($media, 'custom_properties.full_path')
                            ?? ('products/' . data_get($media, 'file_name'));
                        $url = asset('storage/' . $path);
                    } catch (\Throwable $e) {
                        $url = asset('coleos-assets/product-blocks/product-no-bg1.png');
                    }
                } else {
                    $url = asset('coleos-assets/product-blocks/product-no-bg1.png');
                }

                return (object) [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'base_price' => $p->base_price,
                    'price_promo' => $p->price_promo,
                    'image' => $url,
                ];
            });
        });
    }

    public function render()
    {
        return view('components.details-headers-section-3', [
            'products' => $this->products,
            'limit' => $this->limit,
            'product' => $this->product,
        ]);
    }
}
