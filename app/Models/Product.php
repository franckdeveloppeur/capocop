<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use App\Observers\ProductObserver;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'shop_id',
        'title',
        'slug',
        'description',
        'base_price',
        'price_promo',
        'status',
        'stock_manage',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'price_promo' => 'decimal:2',
            'stock_manage' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function getPrice(): ?float
    {
        return $this->price_promo !== null ? (float)$this->price_promo : (float)$this->base_price;
    }

    /**
     * Scope: Filter by categories
     */
    public function scopeByCategories($query, array $categoryIds)
    {
        if (empty($categoryIds)) {
            return $query;
        }

        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->whereIn('categories.id', $categoryIds);
        });
    }

    /**
     * Scope: Filter by price range
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('base_price', '>=', $minPrice)
                  ->orWhere('price_promo', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('base_price', '<=', $maxPrice)
                  ->orWhere('price_promo', '<=', $maxPrice);
            });
        }

        return $query;
    }

    /**
     * Scope: Filter by status
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by tags
     */
    public function scopeByTags($query, array $tagIds)
    {
        if (empty($tagIds)) {
            return $query;
        }

        return $query->whereIn('id', function ($subquery) use ($tagIds) {
            $subquery->select('taggables.taggable_id')
                ->from('taggables')
                ->whereIn('taggables.tag_id', $tagIds)
                ->where('taggables.taggable_type', static::class);
        });
    }

    /**
     * Scope: Sort products
     */
    public function scopeSortBy($query, $sort = 'newest')
    {
        return match($sort) {
            'oldest' => $query->oldest('created_at'),
            'price_asc' => $query->orderByRaw('COALESCE(price_promo, base_price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(price_promo, base_price) DESC'),
            'popular' => $query->orderByRaw('(SELECT COUNT(*) FROM order_items WHERE order_items.product_id = products.id) DESC')->orderBy('created_at', 'desc'),
            default => $query->latest('created_at'),
        };
    }
}

