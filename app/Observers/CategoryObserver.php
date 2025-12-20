<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        // Ensure slug is unique
        $baseSlug = $category->slug;
        $counter = 1;
        while (Category::where('slug', $category->slug)->exists()) {
            $category->slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }
}


















