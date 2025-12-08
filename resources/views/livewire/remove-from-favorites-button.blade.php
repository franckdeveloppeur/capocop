<?php

use Livewire\Volt\Component;
use App\Models\Favorite;
use App\Models\Product;
use Filament\Notifications\Notification;

new class extends Component {
    public string $productId;

    public function mount(string $productId)
    {
        $this->productId = $productId;
    }

    public function removeFromFavorites()
    {
        try {
            $query = Favorite::where('favoritable_type', Product::class)
                ->where('favoritable_id', $this->productId);

            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            } else {
                $query->where('session_id', session()->getId());
            }

            $favorite = $query->first();

            if ($favorite) {
                $favorite->delete();

                Notification::make()
                    ->title('Retiré des favoris')
                    ->description('Le produit a été retiré de vos favoris.')
                    ->success()
                    ->send();

                // Dispatch event to update favorites page
                $this->dispatch('favorites:updated');
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Erreur')
                ->description('Une erreur s\'est produite.')
                ->danger()
                ->send();
        }
    }
};

?>

<button 
    wire:click="removeFromFavorites"
    class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-full transition duration-200"
    title="Retirer des favoris"
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50"
>
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
    </svg>
</button>
