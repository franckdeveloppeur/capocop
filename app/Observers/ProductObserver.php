<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    public function created(Product $product): void
    {
        // Handle product_images if they were uploaded
        $this->handleProductImages($product);
    }

    public function updated(Product $product): void
    {
        // Handle product_images if they were updated
        $this->handleProductImages($product);
    }

    private function handleProductImages(Product $product): void
    {
        // This is a placeholder for when Filament hydrates the product_images
        // The actual image handling should be done in the Filament Page class
        // where we have access to request and can properly handle the uploaded files
    }

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





















