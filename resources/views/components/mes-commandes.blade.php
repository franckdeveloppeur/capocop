<section class="py-12 bg-gray-100 min-h-screen">
  <div class="container px-4 mx-auto">
    <p class="text-rhino-300 text-center text-xs font-bold tracking-widest uppercase">Commandes récentes</p>
    <h1 class="font-heading text-rhino-700 text-center text-4xl font-semibold mb-12">Historique des commandes</h1>

    @forelse($orders as $order)
      <div class="bg-white rounded-xl shadow-md mb-6">
        {{-- En-tête de la commande --}}
        <div class="p-6 border-b border-gray-100">
          {{-- Badge de statut --}}
          @php
            $statusConfig = match($order->status) {
              'pending' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-500', 'label' => 'En attente'],
              'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-500', 'label' => 'En traitement'],
              'paid' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-500', 'label' => 'Payée'],
              'shipped' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-500', 'label' => 'Expédiée'],
              'delivered' => ['bg' => 'bg-green-100', 'text' => 'text-green-500', 'label' => 'Livrée'],
              'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-500', 'label' => 'Annulée'],
              'refunded' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'label' => 'Remboursée'],
              default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'label' => ucfirst($order->status)],
            };
          @endphp
          <div class="mb-4 inline-block py-1 px-3 rounded-xl {{ $statusConfig['bg'] }} uppercase {{ $statusConfig['text'] }} text-xs font-bold tracking-widest">
            {{ $statusConfig['label'] }}
          </div>

          <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-rhino-800 text-2xl font-semibold">
              Commande #{{ strtoupper(substr($order->id, 0, 8)) }}
            </h2>
            <div class="flex flex-wrap gap-2">
              <a class="py-2 px-4 text-purple-500 border border-gray-200 rounded-sm text-center text-sm font-medium shadow-md hover:bg-purple-500 hover:text-white transition duration-200" href="#">
                Facture
              </a>
              @if($order->shipment && $order->shipment->tracking_number)
                <a class="py-2 px-4 bg-purple-500 rounded-sm text-center text-sm text-white font-medium hover:bg-purple-600 transition duration-200" href="#">
                  Suivre la commande
                </a>
              @endif
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-6">
            <p class="text-rhino-400 text-sm">
              <span>Passée le :</span>
              <span class="text-rhino-700">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
            </p>
            <div class="h-3 w-px bg-rhino-200"></div>
            <p class="text-rhino-400 text-sm">
              <span>Total :</span>
              <span class="text-rhino-700 font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </p>
            @if($order->shipment)
              <div class="h-3 w-px bg-rhino-200"></div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.625V7.5L7.125 4.5V1.125C7.125 0.503657 6.62134 0 6 0C5.37866 0 4.875 0.503657 4.875 1.125V4.5L0 7.5V8.625L4.875 7.125V10.125L3.75 10.875V12L6 11.25L8.25 12V10.875L7.125 10.125V7.125L12 8.625Z" fill="#06B83A"></path>
                </svg>
                <span class="text-green-500 text-sm font-medium">
                  Transporteur : {{ $order->shipment->carrier ?? 'Non spécifié' }}
                </span>
              </div>
            @endif
          </div>
        </div>

        {{-- Articles de la commande --}}
        @foreach($order->items as $index => $item)
          <div class="p-6 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
            <div class="flex items-center flex-wrap -mx-6">
              <div class="w-full md:w-auto px-4 mb-6 md:mb-0">
                <div class="bg-gray-100 w-20 h-20 rounded-lg flex items-center justify-center overflow-hidden">
                  @if($item->product && $item->product->media->isNotEmpty())
                    <img src="{{ asset('storage/' . $item->product->media->first()->path) }}" alt="{{ $item->product->title }}" class="w-full h-full object-cover">
                  @else
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  @endif
                </div>
              </div>
              <div class="w-full md:w-2/3 xl:w-5/6 px-4 flex-grow">
                <div class="flex flex-col xs:flex-row flex-wrap xs:items-center justify-between gap-4 mb-2">
                  <h2 class="text-rhino-800 font-semibold">
                    {{ $item->product->title ?? 'Produit supprimé' }}
                  </h2>
                  <p class="text-rhino-500 font-semibold">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="flex flex-col xs:flex-row flex-wrap xs:items-center justify-between gap-4">
                  <div class="flex flex-wrap items-center gap-3">
                    @if($item->variant)
                      <p class="text-rhino-300 text-sm">{{ $item->variant->name ?? 'Variante' }}</p>
                      <div class="w-px h-3 bg-rhino-200"></div>
                    @endif
                    <p class="text-rhino-300 text-sm">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA/unité</p>
                  </div>
                  <p class="text-sm text-rhino-300">Qté : {{ $item->quantity }}</p>
                </div>
              </div>
            </div>
          </div>
        @endforeach

        {{-- Résumé de la commande --}}
        <div class="p-6 bg-gray-50 rounded-b-xl">
          <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex flex-wrap items-center gap-6 text-sm">
              @if($order->discount_amount > 0)
                <p class="text-rhino-400">
                  <span>Réduction :</span>
                  <span class="text-green-600 font-medium">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</span>
                </p>
              @endif
              @if($order->shipping_amount > 0)
                <p class="text-rhino-400">
                  <span>Livraison :</span>
                  <span class="text-rhino-700">{{ number_format($order->shipping_amount, 0, ',', ' ') }} FCFA</span>
                </p>
              @endif
              <p class="text-rhino-400">
                <span>Paiement :</span>
                <span class="text-rhino-700 capitalize">
                  @php
                    $paymentLabels = [
                      'mobile_money' => 'Mobile Money',
                      'card' => 'Carte bancaire',
                      'wallet' => 'Portefeuille',
                      'installment' => 'Paiement échelonné',
                    ];
                  @endphp
                  {{ $paymentLabels[$order->payment_method] ?? $order->payment_method ?? 'Non spécifié' }}
                </span>
              </p>
            </div>
            <p class="text-rhino-800 font-heading font-semibold text-lg">
              Total : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
            </p>
          </div>
        </div>
      </div>
    @empty
      {{-- État vide - Aucune commande --}}
      <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
        </div>
        <h2 class="font-heading text-rhino-700 text-2xl font-semibold mb-4">Aucune commande pour le moment</h2>
        <p class="text-rhino-400 mb-8 max-w-md mx-auto">
          Vous n'avez pas encore passé de commande. Découvrez nos produits et passez votre première commande !
        </p>
        <a href="{{ route('produits') }}" class="inline-block py-3 px-8 bg-purple-500 rounded-lg text-center text-white font-medium hover:bg-purple-600 transition duration-200">
          Découvrir les produits
        </a>
      </div>
    @endforelse
  </div>
</section>
