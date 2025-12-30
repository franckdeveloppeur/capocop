<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ substr($order->id, 0, 8) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">
        {{-- En-tête avec bouton d'impression --}}
        <div class="flex justify-between items-start mb-8 no-print">
            <h1 class="text-3xl font-bold text-gray-800">Facture</h1>
            <button onclick="window.print()" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
                Imprimer / Télécharger PDF
            </button>
        </div>

        {{-- Informations de l'entreprise --}}
        <div class="mb-8 pb-8 border-b-2 border-gray-200">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Capocop</h2>
                    <p class="text-gray-600">E-commerce Platform</p>
                    <p class="text-gray-600">Email: contact@capocop.com</p>
                    <p class="text-gray-600">Téléphone: +237 XXX XXX XXX</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 mb-1">Facture N°</p>
                    <p class="text-2xl font-bold text-gray-800">#{{ strtoupper(substr($order->id, 0, 8)) }}</p>
                    <p class="text-gray-600 mt-4">Date: {{ $order->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Informations client --}}
        <div class="mb-8 pb-8 border-b-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations Client</h3>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <p class="text-gray-600 mb-1">Nom</p>
                    <p class="font-semibold text-gray-800">{{ $order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-1">Email</p>
                    <p class="font-semibold text-gray-800">{{ $order->user->email ?? 'N/A' }}</p>
                </div>
            </div>
            @if($order->address)
                <div class="mt-4">
                    <p class="text-gray-600 mb-1">Adresse de livraison</p>
                    <p class="font-semibold text-gray-800">
                        {{ $order->address->line1 ?? '' }}
                        @if($order->address->line2), {{ $order->address->line2 }}@endif
                        <br>
                        {{ $order->address->city ?? '' }}, {{ $order->address->postal_code ?? '' }}
                        <br>
                        {{ $order->address->country ?? '' }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Articles de la commande --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Articles</h3>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left text-gray-700">Produit</th>
                        <th class="border border-gray-300 px-4 py-2 text-center text-gray-700">Quantité</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-gray-700">Prix unitaire</th>
                        <th class="border border-gray-300 px-4 py-2 text-right text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td class="border border-gray-300 px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $item->product->title ?? 'Produit supprimé' }}</div>
                                @if($item->variant)
                                    <div class="text-sm text-gray-600">Variante: {{ $item->variant->sku ?? 'N/A' }}</div>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="border border-gray-300 px-4 py-3 text-right font-semibold">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Résumé financier --}}
        <div class="mb-8">
            <div class="flex justify-end">
                <div class="w-1/2">
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Sous-total:</span>
                            <span>{{ number_format($order->total_amount - $order->shipping_amount + $order->discount_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Remise:</span>
                                <span>-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        @if($order->shipping_amount > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>Frais de livraison:</span>
                                <span>{{ number_format($order->shipping_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xl font-bold text-gray-800 pt-2 border-t-2 border-gray-300">
                            <span>Total:</span>
                            <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations de paiement --}}
        <div class="mb-8 pb-8 border-b-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de Paiement</h3>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <p class="text-gray-600 mb-1">Méthode de paiement</p>
                    <p class="font-semibold text-gray-800">
                        @php
                            $paymentLabels = [
                                'cash' => 'Espèces',
                                'card' => 'Carte bancaire',
                                'mobile_money' => 'Mobile Money',
                                'bank_transfer' => 'Virement bancaire',
                            ];
                        @endphp
                        {{ $paymentLabels[$order->payment_method] ?? $order->payment_method ?? 'Non spécifiée' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 mb-1">Statut</p>
                    <p class="font-semibold text-gray-800">
                        @php
                            $statusLabels = [
                                'pending' => 'En attente',
                                'processing' => 'En traitement',
                                'paid' => 'Payée',
                                'shipped' => 'Expédiée',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée',
                                'refunded' => 'Remboursée',
                            ];
                        @endphp
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </p>
                </div>
            </div>
            @if($order->is_installment && $order->installmentPlan)
                <div class="mt-4">
                    <p class="text-gray-600 mb-1">Paiement échelonné</p>
                    <p class="font-semibold text-gray-800">
                        {{ $order->installmentPlan->number_of_installments }} échéances de 
                        {{ number_format(($order->installmentPlan->total_amount - $order->installmentPlan->deposit_amount) / $order->installmentPlan->number_of_installments, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            @endif
        </div>

        {{-- Pied de page --}}
        <div class="text-center text-gray-600 text-sm">
            <p>Merci pour votre achat !</p>
            <p class="mt-2">Pour toute question, contactez-nous à contact@capocop.com</p>
        </div>
    </div>
</body>
</html>

