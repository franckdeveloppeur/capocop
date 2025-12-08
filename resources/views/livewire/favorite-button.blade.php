<?php

use Livewire\Volt\Component;
use App\Models\Favorite;
use Illuminate\Support\Str;

new class extends Component {
    public string $productId;
    public bool $favorited = false;

    public function mount(string $productId)
    {
        $this->productId = $productId;

        $query = Favorite::where('favoritable_type', \App\Models\Product::class)
            ->where('favoritable_id', $this->productId);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }

        $this->favorited = $query->exists();
    }

    public function toggle()
    {
        $query = Favorite::where('favoritable_type', \App\Models\Product::class)
            ->where('favoritable_id', $this->productId);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }

        $fav = $query->first();

        if ($fav) {
            $fav->delete();
            $this->favorited = false;
        } else {
            Favorite::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'favoritable_type' => \App\Models\Product::class,
                'favoritable_id' => $this->productId,
                'created_at' => now(),
            ]);
            $this->favorited = true;
        }
    }
};
?>

<div>
    <button wire:click="toggle" class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white mb-4 transition duration-200"
            aria-pressed="{{ $favorited ? 'true' : 'false' }}" title="Ajouter aux favoris">
        @if($favorited)
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 24 24" fill="none" class="text-red-500">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z" fill="currentColor"/>
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 24 24" fill="none" class="text-coolGray-700">
                <path d="M12.1 8.64l-.1.1-.11-.11A2.5 2.5 0 0 0 7 7c-.9 0-1.7.4-2.24 1.02A3.5 3.5 0 0 0 4 12.5c0 1.5.67 2.9 1.73 3.82L12 21l6.27-4.68A3.5 3.5 0 0 0 20 12.5c0-1.72-.97-3.26-2.4-4.06A2.5 2.5 0 0 0 17 7a2.5 2.5 0 0 0-4.9 1.64z" stroke="currentColor" stroke-width="1.2" fill="none"/>
            </svg>
        @endif
    </button>
</div>
