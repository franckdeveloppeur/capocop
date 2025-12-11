<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {
    public ?Order $order = null;
    public string $orderId = '';

    public function mount(string $orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::with(['items.product.media', 'address', 'user'])->find($orderId);
    }
};

?>

<section class="py-12">
    <div class="container px-4 mx-auto">
        @if($order)
        <div class="flex flex-wrap -mx-4 justify-between">
            <!-- Image de succès -->
            <div class="w-full lg:w-1/2 px-4 mb-8 lg:mb-0">
                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl p-12 flex items-center justify-center min-h-[400px]">
                    <div class="text-center">
                        <div class="w-32 h-32 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-rhino-700 mb-2">Commande confirmée !</h2>
                        <p class="text-rhino-400">Votre commande a été enregistrée avec succès</p>
                    </div>
                </div>
            </div>

            <!-- Détails de la commande -->
            <div class="w-full lg:w-1/2 px-4">
                <p class="uppercase text-rhino-300 text-xs font-bold tracking-widest">Votre paiement a été validé</p>
                <h1 class="font-heading text-rhino-700 text-4xl font-semibold mb-6">Merci pour votre commande</h1>
                
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h2 class="font-heading text-2xl font-semibold text-rhino-800">N° de commande: {{ strtoupper(substr($order->id, 0, 8)) }}</h2>
                    <a class="py-1 px-4 rounded-sm border border-gray-200 shadow-md flex items-center gap-2 text-purple-500 text-sm font-medium hover:text-white hover:bg-purple-500 transition duration-200" href="#">
                        <span>Votre facture</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5.99725 14.4194L14.2468 6.16985" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M14.8361 5.58057C14.8945 5.63889 14.9413 5.70778 14.9736 5.78329C15.006 5.85879 15.0233 5.93942 15.0247 6.02054L15.1435 12.6731C15.1464 12.8369 15.0841 12.9928 14.9704 13.1066C14.8566 13.2204 14.7007 13.2826 14.5369 13.2797C14.3731 13.2768 14.2149 13.2089 14.097 13.091C13.9791 12.9731 13.9112 12.8149 13.9083 12.6511L13.8005 6.61612L7.76552 6.50835C7.60173 6.50543 7.44349 6.43757 7.3256 6.31968C7.20772 6.2018 7.13985 6.04355 7.13693 5.87976C7.13401 5.71597 7.19628 5.56004 7.31003 5.44629C7.42379 5.33253 7.57971 5.27026 7.74351 5.27318L14.3961 5.39196C14.4772 5.39329 14.5579 5.41063 14.6334 5.443C14.7089 5.47537 14.7778 5.52212 14.8361 5.58057Z" fill="currentColor"></path>
                        </svg>
                    </a>
                </div>

                <div class="flex items-center gap-6 flex-wrap mb-6">
                    <p class="text-rhino-400 text-sm">
                        <span>Date de commande:</span>
                        <span class="text-rhino-700 ml-1">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </p>
                    <div class="h-4 w-px bg-rhino-200"></div>
                    <div class="flex flex-wrap items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.625V7.5L7.125 4.5V1.125C7.125 0.503657 6.62134 0 6 0C5.37866 0 4.875 0.503657 4.875 1.125V4.5L0 7.5V8.625L4.875 7.125V10.125L3.75 10.875V12L6 11.25L8.25 12V10.875L7.125 10.125V7.125L12 8.625Z" fill="#06B83A"></path>
                        </svg>
                        <span class="text-green-500 text-sm font-medium">Livraison prévue: {{ $order->created_at->addDays(5)->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Articles commandés -->
                @foreach($order->items as $item)
                <div class="py-6 border-t border-b border-gray-100">
                    <div class="flex flex-wrap gap-6">
                        <div class="bg-gray-100 rounded-lg w-40 h-40 flex items-center justify-center overflow-hidden">
                            @if($item->product && $item->product->media->first())
                                @php
                                    $media = $item->product->media->first();
                                    $imagePath = data_get($media, 'custom_properties.full_path') ?? ('products/' . data_get($media, 'file_name'));
                                @endphp
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $item->product->title }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col justify-between gap-4">
                            <div>
                                <h2 class="text-rhino-800 text-lg font-semibold">{{ $item->product->title ?? 'Produit' }}</h2>
                                <p class="text-rhino-400 text-sm">{{ Str::limit($item->product->description ?? '', 80) }}</p>
                            </div>
                            <p class="text-rhino-800 text-2xl font-bold">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                            <p class="text-rhino-800 text-sm">Qté: {{ $item->quantity }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Récapitulatif des prix -->
                <div class="bg-purple-100 rounded-xl py-4 px-6 flex items-center justify-between flex-wrap mt-6">
                    <p class="text-rhino-800">Sous-total</p>
                    <p class="text-rhino-400 font-heading text-xl font-semibold">{{ number_format($order->total_amount - $order->shipping_amount + $order->discount_amount, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="py-4 px-6 flex items-center justify-between flex-wrap">
                    <p class="text-rhino-800">Livraison</p>
                    <p class="text-rhino-400 font-heading text-xl font-semibold">{{ $order->shipping_amount == 0 ? 'Gratuit' : number_format($order->shipping_amount, 0, ',', ' ') . ' FCFA' }}</p>
                </div>
                @if($order->discount_amount > 0)
                <div class="bg-orange-50 rounded-xl py-4 px-6 flex items-center justify-between flex-wrap">
                    <p class="text-orange-600">Réduction</p>
                    <p class="text-orange-500 font-heading text-xl font-semibold">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</p>
                </div>
                @endif
                <div class="py-4 px-6 flex items-center justify-between flex-wrap mb-6 border-b border-gray-100">
                    <p class="text-rhino-800 font-bold">Total de la commande</p>
                    <p class="text-rhino-800 font-heading text-xl font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                </div>

                <!-- Informations de livraison et paiement -->
                <div class="flex flex-wrap flex-col xs:flex-row justify-between items-start gap-4 px-6 mb-6">
                    @if($order->address)
                    <ul class="text-sm">
                        <li class="text-rhino-700 font-semibold mb-2">Adresse de livraison</li>
                        <li class="text-rhino-400">{{ $order->address->full_name }}</li>
                        <li class="text-rhino-400">{{ $order->address->line1 }}</li>
                        @if($order->address->line2)
                        <li class="text-rhino-400">{{ $order->address->line2 }}</li>
                        @endif
                        <li class="text-rhino-400">{{ $order->address->city }}, {{ $order->address->postal_code }}</li>
                        <li class="text-rhino-400">{{ $order->address->country }}</li>
                    </ul>
                    @endif
                    
                    <ul class="text-sm">
                        <li class="text-rhino-700 font-semibold mb-2">Contact</li>
                        @if($order->user)
                        <li class="text-rhino-400">{{ $order->user->email }}</li>
                        @endif
                        @if($order->address)
                        <li class="text-rhino-400">{{ $order->address->phone }}</li>
                        @endif
                    </ul>
                    
                    <ul class="text-sm">
                        <li class="text-rhino-700 font-semibold mb-2">Moyen de paiement</li>
                        <li class="text-rhino-400">
                            @switch($order->payment_method)
                                @case('wallet')
                                    Capocop Pay
                                    @break
                                @case('mobile_money')
                                    Mobile Money
                                    @break
                                @case('card')
                                    Carte bancaire
                                    @break
                                @case('cash')
                                    Paiement à la livraison
                                    @break
                                @default
                                    {{ ucfirst($order->payment_method ?? 'Non spécifié') }}
                            @endswitch
                        </li>
                        <li class="text-rhino-400">
                            Statut: 
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('processing') bg-blue-100 text-blue-800 @break
                                    @case('paid') bg-green-100 text-green-800 @break
                                    @case('shipped') bg-indigo-100 text-indigo-800 @break
                                    @case('delivered') bg-green-100 text-green-800 @break
                                    @case('cancelled') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                @switch($order->status)
                                    @case('pending') En attente @break
                                    @case('processing') En cours @break
                                    @case('paid') Payé @break
                                    @case('shipped') Expédié @break
                                    @case('delivered') Livré @break
                                    @case('cancelled') Annulé @break
                                    @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="border-t border-gray-100 pt-6 flex flex-wrap gap-4 justify-end">
                    <a class="py-3 px-6 rounded-sm border border-gray-200 shadow-md text-sm font-medium text-purple-500 hover:text-white hover:bg-purple-500 transition duration-200" href="{{ route('commandes') ?? '#' }}">
                        Voir mes commandes
                    </a>
                    <a class="px-6 py-3 rounded-sm text-center text-white text-sm font-medium bg-purple-500 hover:bg-purple-600 transition duration-200" href="{{ route('produits') }}">
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
        @else
        <!-- Commande non trouvée -->
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-rhino-700 mb-2">Commande introuvable</h2>
            <p class="text-rhino-400 mb-8">La commande que vous recherchez n'existe pas ou a été supprimée.</p>
            <a class="px-6 py-3 rounded-sm text-center text-white text-sm font-medium bg-purple-500 hover:bg-purple-600 transition duration-200" href="{{ route('produits') }}">
                Retourner à la boutique
            </a>
        </div>
        @endif
    </div>
</section>

