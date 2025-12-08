<?php

use Livewire\Volt\Component;

new class extends Component {
    public array $categories = [];
    public array $tags = [];
    public array $selectedCategories = [];
    public array $selectedTags = [];
    public ?float $minPrice = null;
    public ?float $maxPrice = null;
    public float $priceRange = 798;

    public function updateCategory($categoryId)
    {
        $this->dispatch('toggle-category', categoryId: $categoryId);
    }

    public function updateTag($tagId)
    {
        $this->dispatch('toggle-tag', tagId: $tagId);
    }

    public function updatePrice($min, $max)
    {
        $this->dispatch('update-price', min: (float) $min, max: (float) $max);
    }

    public function clearAll()
    {
        $this->dispatch('clear-filters');
    }
};

?>

<div class="w-full md:w-1/3 lg:w-1/4 px-4">
    <!-- Categories Filter -->
    <div class="py-6 border-b border-t border-coolGray-200" x-data="{ accordion: true }">
        <div class="flex justify-between items-center flex-wrap gap-4 cursor-pointer" @click="accordion = !accordion">
            <p class="text-rhino-700 font-semibold">Catégories</p>
            <span class="inline-block transform" :class="{ 'rotate-180': accordion }">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewbox="0 0 24 25" fill="none">
                    <path d="M18 9.5L12 15.5L6 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
        <div x-show="accordion" x-transition class="mt-4">
            <ul class="text-coolGray-700 flex flex-col gap-3">
                @foreach($categories as $category)
                    <li class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="cat-{{ $category['id'] }}"
                            wire:click="updateCategory('{{ $category['id'] }}')"
                            @checked(in_array($category['id'], $selectedCategories))
                            class="rounded border-coolGray-300 cursor-pointer"
                        >
                        <label for="cat-{{ $category['id'] }}" class="cursor-pointer hover:text-coolGray-800 transition duration-200">
                            {{ $category['name'] }}
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Tags Filter -->
    <div class="py-6 border-b border-coolGray-200" x-data="{ accordion: true }">
        <div class="flex justify-between items-center flex-wrap gap-4 cursor-pointer" @click="accordion = !accordion">
            <p class="text-rhino-700 font-semibold">Tags</p>
            <span class="inline-block transform" :class="{ 'rotate-180': accordion }">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewbox="0 0 24 25" fill="none">
                    <path d="M18 9.5L12 15.5L6 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
        <div x-show="accordion" x-transition class="mt-4">
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <button
                        wire:click="updateTag('{{ $tag['id'] }}')"
                        @class([
                            'px-3 py-1 rounded-full text-sm transition duration-200',
                            'bg-purple-500 text-white' => in_array($tag['id'], $selectedTags),
                            'bg-coolGray-100 text-coolGray-700 hover:bg-coolGray-200' => !in_array($tag['id'], $selectedTags),
                        ])
                    >
                        {{ $tag['name'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Price Range Filter -->
    <div class="py-6 border-b border-coolGray-200" x-data="{ accordion: true }">
        <div class="flex justify-between items-center flex-wrap gap-4 cursor-pointer" @click="accordion = !accordion">
            <p class="text-rhino-700 font-semibold">Prix</p>
            <span class="inline-block transform" :class="{ 'rotate-180': accordion }">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewbox="0 0 24 25" fill="none">
                    <path d="M18 9.5L12 15.5L6 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
        <div x-show="accordion" x-transition class="mt-4">
            <div x-data="{ 
                min: @js((int)($minPrice ?? 0)), 
                max: @js((int)($maxPrice ?? $priceRange)),
                range: @js((int)$priceRange)
            }" class="space-y-4">
                <input
                    type="range"
                    x-model="min"
                    @change="$wire.updatePrice(min, max)"
                    :max="range"
                    class="w-full"
                >
                <input
                    type="range"
                    x-model="max"
                    @change="$wire.updatePrice(min, max)"
                    :max="range"
                    class="w-full"
                >
                <div class="flex justify-between text-sm text-coolGray-700">
                    <span x-text="`$${min}`"></span>
                    <span x-text="`$${max}`"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Filters -->
    @if(!empty($selectedCategories) || !empty($selectedTags) || $minPrice !== null || $maxPrice !== null)
        <div class="py-6">
            <button
                wire:click="clearAll"
                class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-sm hover:bg-red-100 transition duration-200 font-medium text-sm"
            >
                Effacer les filtres
            </button>
        </div>
    @endif
</div>
