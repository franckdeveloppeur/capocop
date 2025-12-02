<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product): void
    {
        // Ensure slug is unique
        $baseSlug = $product->slug;
        $counter = 1;
        while (Product::where('slug', $product->slug)->exists()) {
            $product->slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

    public function updating(Product $product): void
    {
        // Ensure slug is unique on update
        if ($product->isDirty('slug')) {
            $baseSlug = $product->slug;
            $counter = 1;
            while (Product::where('slug', $product->slug)->where('id', '!=', $product->id)->exists()) {
                $product->slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }
    }
}

