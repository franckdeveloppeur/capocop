<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $slug;

    public function mount(string $slug)
    {
        $this->slug = $slug;
    }

    public function getUrl()
    {
        return url('/produit/' . $this->slug);
    }
};
?>

<div class="ml-2">
    <div class="flex flex-col gap-2">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/produit/' . $slug)) }}" target="_blank" rel="noopener" class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white text-coolGray-700 hover:bg-coolGray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="currentColor"><path d="M22 12.07C22 6.48 17.52 2 11.94 2S2 6.48 2 12.07c0 4.99 3.66 9.13 8.44 9.93v-7.03H8.08v-2.9h2.36V9.41c0-2.33 1.39-3.62 3.51-3.62. 1.02 0 2.09.18 2.09.18v2.3h-1.17c-1.15 0-1.5.71-1.5 1.44v1.73h2.56l-.41 2.9h-2.15v7.03C18.34 21.2 22 17.06 22 12.07z"/></svg>
        </a>

        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('/produit/' . $slug)) }}" target="_blank" rel="noopener" class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white text-coolGray-700 hover:bg-coolGray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="currentColor"><path d="M22.46 6c-.77.35-1.5.58-2.28.69.82-.49 1.45-1.27 1.75-2.2-.77.45-1.62.78-2.52.96-.72-.77-1.75-1.25-2.88-1.25-2.18 0-3.95 1.78-3.95 3.98 0 .31.03.61.1.9C7.69 9.1 5.1 7.7 3.4 5.6c-.34.6-.53 1.3-.53 2.04 0 1.4.7 2.64 1.77 3.37-.65 0-1.26-.2-1.8-.5v.05c0 1.95 1.37 3.57 3.18 3.94-.33.09-.67.14-1.03.14-.25 0-.5-.02-.74-.07.5 1.54 1.95 2.66 3.67 2.7-1.35 1.06-3.05 1.7-4.9 1.7-.32 0-.64-.02-.95-.06 1.76 1.13 3.85 1.8 6.1 1.8 7.32 0 11.33-6.06 11.33-11.33v-.52C21 7.6 21.8 6.86 22.46 6z"/></svg>
        </a>

        <a href="https://api.whatsapp.com/send?text={{ urlencode(url('/produit/' . $slug)) }}" target="_blank" rel="noopener" class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white text-coolGray-700 hover:bg-coolGray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.93 11.93 0 0 0 12.06 0C5.37 0 .08 5.3.08 11.99c0 2.12.55 4.18 1.6 6.01L0 24l6.25-1.62c1.66.9 3.53 1.39 5.81 1.39 6.69 0 11.98-5.3 11.98-11.99 0-3.2-1.25-6.13-3.52-8.26zM12.06 20.05c-1.81 0-3.5-.49-4.99-1.36l-.36-.21-3.71.96.99-3.62-.23-.37A8.77 8.77 0 0 1 3.3 11.99c0-4.88 3.97-8.85 8.77-8.85 2.34 0 4.54.92 6.2 2.58 1.66 1.66 2.58 3.86 2.58 6.2 0 4.8-3.98 8.77-8.77 8.77z"/></svg>
        </a>

        <button type="button" onclick="navigator.clipboard && navigator.clipboard.writeText(window.location.href)" class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white text-coolGray-700 hover:bg-coolGray-100 transition" title="Copier le lien">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewbox="0 0 24 24" fill="currentColor"><path d="M3 13v8a2 2 0 0 0 2 2h8v-2H5v-8H3zm13-11H8a2 2 0 0 0-2 2v6h2V4h8v6h2V4a2 2 0 0 0-2-2z"/></svg>
        </button>
    </div>
</div>
