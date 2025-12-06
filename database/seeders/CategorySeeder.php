<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Électronique', 'slug' => 'electronique'],
            ['name' => 'Vêtements', 'slug' => 'vetements'],
            ['name' => 'Maison & Jardin', 'slug' => 'maison-jardin'],
            ['name' => 'Sport & Loisirs', 'slug' => 'sport-loisirs'],
            ['name' => 'Beauté & Santé', 'slug' => 'beaute-sante'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}





