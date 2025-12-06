<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Observers\ProductVariantObserver;

#[ObservedBy([ProductVariantObserver::class])]
class ProductVariant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price',
        'stock',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'weight' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'variant_id');
    }
}





