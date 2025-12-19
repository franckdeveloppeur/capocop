<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{
    public function creating(ProductVariant $variant): void
    {
        // Generate SKU if not provided
        if (empty($variant->sku)) {
            $variant->sku = 'SKU-' . strtoupper(substr(md5($variant->product_id . time()), 0, 10));
        }
    }

    public function created(ProductVariant $variant): void
    {
        // Log inventory transaction for initial stock
        if ($variant->stock > 0) {
            $variant->inventoryTransactions()->create([
                'change' => $variant->stock,
                'reason' => 'restock',
                'meta' => ['initial_stock' => true],
            ]);
        }
    }
}
















