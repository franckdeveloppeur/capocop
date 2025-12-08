<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Bouteilles de gaz
            'bouteille gaz 6kg',
            'bouteille gaz 12kg',
            'bouteille gaz 13kg',
            'recharge gaz',
            'detendeur',
            'accessoires gaz',

            // Fournitures scolaires
            'crayons',
            'stylos',
            'cahiers',
            'trousses',
            'gommes',
            'taille-crayon',
            'règle',
            'colle',
            'feutres',
            'surligneurs',

            // Denrées alimentaires
            'riz',
            'huile',
            'sucre',
            'pâtes',
            'conserves',
            'farine',
            ' lait',
            'épices',
            'café',
            'thé',

            // Divers / complémentaires
            'ménage',
            'hygiène',
            'boissons',
            'snacks'
        ];

        // Ensure we have exactly 30 items (if the array is shorter/longer, trim or repeat)
        if (count($tags) < 30) {
            // duplicate some generic tags until we have 30
            $i = 0;
            while (count($tags) < 30) {
                $tags[] = $tags[$i % count($tags)];
                $i++;
            }
        } elseif (count($tags) > 30) {
            $tags = array_slice($tags, 0, 30);
        }

        foreach ($tags as $name) {
            // Normalize and trim the name
            $name = trim((string) $name);

            // Avoid inserting empty names
            if ($name === '') {
                continue;
            }

            // Insert by name only — tags table doesn't have a `slug` column in this schema
            Tag::firstOrCreate(
                ['name' => ucfirst($name)],
                ['name' => ucfirst($name)]
            );
        }
    }
}
