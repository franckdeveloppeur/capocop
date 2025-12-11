<?php

use Livewire\Volt\Component;
use App\Services\CartService;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

new class extends Component {
    // Étapes actives
    public array $openSteps = [1, 2, 3, 4];
    public array $completedSteps = [];
    
    // Adresse de livraison
    public string $email = '';
    public string $firstName = '';
    public string $lastName = '';
    public string $streetAddress1 = '';
    public string $streetAddress2 = '';
    public string $country = 'Cameroun';
    public string $city = '';
    public string $postalCode = '';
    public string $company = '';
    public string $phone = '';
    public bool $differentBillingAddress = false;
    
    // Méthode de livraison
    public string $shippingMethod = 'standard';
    public float $shippingCost = 0;
    
    // Méthode de paiement
    public string $paymentMethod = '';
    public bool $isNewCapocopClient = true;
    public string $capocopId = '';
    
    // Carte de crédit
    public string $cardName = '';
    public string $cardType = '';
    public string $cardNumber = '';
    public string $cardExpMonth = '';
    public string $cardExpYear = '';
    public string $cardCvc = '';
    
    // Code promo
    public string $discountCode = '';
    public float $discountPercent = 0;
    public string $orderComment = '';
    
    // Panier
    public $cartItems = [];
    public float $subtotal = 0;
    public float $total = 0;

    protected $rules = [
        'email' => 'required|email',
        'firstName' => 'required|min:2',
        'lastName' => 'required|min:2',
        'streetAddress1' => 'required|min:5',
        'country' => 'required',
        'city' => 'required',
        'phone' => 'required|min:9',
        'paymentMethod' => 'required',
    ];

    public function mount()
    {
        $this->loadCart();
        
        if (auth()->check()) {
            $user = auth()->user();
            $this->email = $user->email ?? '';
            $this->firstName = $user->first_name ?? '';
            $this->lastName = $user->last_name ?? '';
            $this->phone = $user->phone ?? '';
        }
    }

    public function loadCart()
    {
        $this->cartItems = CartService::getItems();
        $this->subtotal = (float) CartService::getTotalPrice();
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $discount = $this->subtotal * ($this->discountPercent / 100);
        $this->total = $this->subtotal + $this->shippingCost - $discount;
    }

    public function toggleStep(int $step)
    {
        if (in_array($step, $this->openSteps)) {
            $this->openSteps = array_diff($this->openSteps, [$step]);
        } else {
            $this->openSteps[] = $step;
        }
    }

    public function isStepOpen(int $step): bool
    {
        return in_array($step, $this->openSteps);
    }

    public function setShippingMethod(string $method)
    {
        $this->shippingMethod = $method;
        $this->shippingCost = match($method) {
            'express' => 2500,
            default => 0,
        };
        $this->calculateTotal();
    }

    public function setPaymentMethod(string $method)
    {
        $this->paymentMethod = $method;
        if ($method !== 'capocop') {
            $this->isNewCapocopClient = true;
            $this->capocopId = '';
        }
    }

    public function setCapocopClientType(bool $isNew)
    {
        $this->isNewCapocopClient = $isNew;
        if ($isNew) {
            $this->capocopId = '';
        }
    }

    public function applyDiscount()
    {
        if (strtoupper($this->discountCode) === 'CAPOCOP20') {
            $this->discountPercent = 20;
        } elseif (strtoupper($this->discountCode) === 'CAPOCOP10') {
            $this->discountPercent = 10;
        } else {
            $this->discountPercent = 0;
        }
        $this->calculateTotal();
    }

    public function updateQuantity(string $productId, int $quantity)
    {
        if ($quantity > 0) {
            CartService::updateQuantity($productId, $quantity);
        } else {
            CartService::removeProduct($productId);
        }
        $this->loadCart();
        $this->dispatch('cart:updated', count: CartService::getTotalQuantity());
    }

    public function removeItem(string $productId)
    {
        CartService::removeProduct($productId);
        $this->loadCart();
        $this->dispatch('cart:updated', count: CartService::getTotalQuantity());
    }

    public function confirmOrder()
    {
        $this->validate();

        if ($this->paymentMethod === 'capocop' && !$this->isNewCapocopClient && empty($this->capocopId)) {
            $this->addError('capocopId', 'Veuillez entrer votre identifiant Capocop');
            return;
        }

        try {
            $address = Address::create([
                'user_id' => auth()->id(),
                'full_name' => $this->firstName . ' ' . $this->lastName,
                'phone' => $this->phone,
                'country' => $this->country,
                'city' => $this->city,
                'postal_code' => $this->postalCode ?: '00000',
                'line1' => $this->streetAddress1,
                'line2' => $this->streetAddress2,
            ]);

            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $address->id,
                'total_amount' => $this->total,
                'shipping_amount' => $this->shippingCost,
                'discount_amount' => $this->subtotal * ($this->discountPercent / 100),
                'status' => 'pending',
                'payment_method' => $this->paymentMethod === 'capocop' ? 'wallet' : $this->paymentMethod,
            ]);

            foreach ($this->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);
            }

            CartService::clear();
            $this->dispatch('cart:updated', count: 0);

            session()->flash('success', 'Votre commande a été confirmée avec succès !');
            return redirect()->route('orders.confirmation', $order->id);

        } catch (\Exception $e) {
            $this->addError('general', 'Une erreur est survenue lors de la confirmation de votre commande.');
        }
    }
};

?>

<section class="relative bg-white overflow-hidden">
    <style>
        .step-section {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .step-section.collapsed {
            max-height: 0;
            padding-top: 0;
            padding-bottom: 0;
            opacity: 0;
        }
        .step-section.expanded {
            max-height: 2000px;
            opacity: 1;
        }
        .step-header {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .step-header:hover {
            background-color: rgba(139, 92, 246, 0.05);
        }
        .chevron-rotate {
            transition: transform 0.3s ease;
        }
        .chevron-rotate.open {
            transform: rotate(180deg);
        }
        .payment-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .payment-card:hover {
            border-color: #8b5cf6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }
        .payment-card.selected {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
        }
        .capocop-expand {
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .custom-checkbox-1:checked + div {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
        }
        .custom-checkbox-1:checked + div svg {
            display: block;
        }
        .btn-capocop {
            transition: all 0.3s ease;
        }
        .btn-capocop:hover {
            transform: scale(1.02);
        }
        .btn-capocop.active {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }
    </style>

    <div class="container mx-auto px-4">
        <div class="py-20">
            <div class="flex flex-wrap -mx-2">
                
                <!-- Colonne 1: Adresse de livraison -->
                <div class="w-full lg:w-1/3 px-2 mb-8">
                    <div class="bg-coolGray-50 rounded-md">
                        <div class="step-header p-6 border-b border-coolGray-200 flex justify-between items-center" wire:click="toggleStep(1)">
                            <h2 class="text-rhino-700 text-xl font-semibold font-heading">Adresse de livraison</h2>
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-200 p-3 flex items-center justify-center rounded">
                                    <span class="text-xs font-bold text-rhino-800">01</span>
                                </div>
                                <svg class="w-5 h-5 text-rhino-400 chevron-rotate {{ $this->isStepOpen(1) ? 'open' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-section {{ $this->isStepOpen(1) ? 'expanded p-6' : 'collapsed' }}">
                            <div class="mb-6">
                                <label class="block mb-2 text-coolGray-700 font-medium text-sm">Adresse email *</label>
                                <input wire:model="email" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="email" placeholder="votre@email.com">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-6">
                                <label class="block mb-2 text-coolGray-700 font-medium text-sm">Prénom *</label>
                                <input wire:model="firstName" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Jean">
                                @error('firstName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-6">
                                <label class="block mb-2 text-coolGray-700 font-medium text-sm">Nom *</label>
                                <input wire:model="lastName" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Dupont">
                                @error('lastName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-6">
                                <label class="block mb-2 text-coolGray-700 font-medium text-sm">Adresse *</label>
                                <input wire:model="streetAddress1" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400 mb-3" type="text" placeholder="Rue, numéro, quartier">
                                <input wire:model="streetAddress2" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Complément (optionnel)">
                                @error('streetAddress1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-6 flex flex-wrap gap-4">
                                <div class="flex-1">
                                    <label class="block mb-2 text-coolGray-700 font-medium text-sm">Pays *</label>
                                    <select wire:model="country" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400">
                                        <option value="Cameroun">Cameroun</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Congo">Congo</option>
                                        <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                        <option value="Sénégal">Sénégal</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block mb-2 text-coolGray-700 font-medium text-sm">Ville *</label>
                                    <input wire:model="city" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Douala">
                                    @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-6 flex flex-wrap gap-4">
                                <div class="flex-1">
                                    <label class="block mb-2 text-coolGray-700 font-medium text-sm">Code postal</label>
                                    <input wire:model="postalCode" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="00000">
                                </div>
                                <div class="flex-1">
                                    <label class="block mb-2 text-coolGray-700 font-medium text-sm">Téléphone *</label>
                                    <input wire:model="phone" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="tel" placeholder="+237 6XX XXX XXX">
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="block mb-2 text-coolGray-700 font-medium text-sm">Entreprise (optionnel)</label>
                                <input wire:model="company" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Nom de l'entreprise">
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <input wire:model="differentBillingAddress" class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0 cursor-pointer" type="checkbox">
                                    <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white">
                                        <svg class="hidden text-white" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                        </svg>
                                    </div>
                                </div>
                                <label class="block text-coolGray-700 cursor-pointer">J'ai une adresse de facturation différente</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne 2: Livraison & Paiement -->
                <div class="w-full lg:w-1/3 px-2 mb-8">
                    <!-- Méthodes de livraison -->
                    <div class="bg-coolGray-50 rounded-md mb-8">
                        <div class="step-header p-6 border-b border-coolGray-200 flex justify-between items-center" wire:click="toggleStep(2)">
                            <h2 class="text-rhino-700 text-xl font-semibold font-heading">Mode de livraison</h2>
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-200 p-3 flex items-center justify-center rounded">
                                    <span class="text-xs font-bold text-rhino-800">02</span>
                                </div>
                                <svg class="w-5 h-5 text-rhino-400 chevron-rotate {{ $this->isStepOpen(2) ? 'open' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-section {{ $this->isStepOpen(2) ? 'expanded p-6' : 'collapsed' }}">
                            <div class="mb-6">
                                <span class="block mb-2 text-coolGray-700 font-medium text-sm">Livraison standard</span>
                                <div class="flex items-center gap-3 cursor-pointer" wire:click="setShippingMethod('standard')">
                                    <div class="relative">
                                        <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $shippingMethod === 'standard' ? 'checked' : '' }}>
                                        <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $shippingMethod === 'standard' ? 'bg-purple-500 border-purple-500' : '' }}">
                                            @if($shippingMethod === 'standard')
                                            <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <label class="block text-gray-700 cursor-pointer">
                                        <span>3-5 jours</span>
                                        <span class="font-bold text-green-600 ml-2">Gratuit</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <span class="block mb-2 text-coolGray-700 font-medium text-sm">Livraison express</span>
                                <div class="flex items-center gap-3 cursor-pointer" wire:click="setShippingMethod('express')">
                                    <div class="relative">
                                        <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $shippingMethod === 'express' ? 'checked' : '' }}>
                                        <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $shippingMethod === 'express' ? 'bg-purple-500 border-purple-500' : '' }}">
                                            @if($shippingMethod === 'express')
                                            <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <label class="block text-gray-700 cursor-pointer">
                                        <span>24-48 heures</span>
                                        <span class="font-bold text-black ml-2">2 500 FCFA</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Méthodes de paiement -->
                    <div class="bg-coolGray-50 rounded-md">
                        <div class="step-header p-6 border-b border-coolGray-200 flex justify-between items-center" wire:click="toggleStep(3)">
                            <h2 class="text-rhino-700 text-xl font-semibold font-heading">Moyen de paiement</h2>
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-200 p-3 flex items-center justify-center rounded">
                                    <span class="text-xs font-bold text-rhino-800">03</span>
                                </div>
                                <svg class="w-5 h-5 text-rhino-400 chevron-rotate {{ $this->isStepOpen(3) ? 'open' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-section {{ $this->isStepOpen(3) ? 'expanded p-6' : 'collapsed' }}">
                            
                            <!-- CAPOCOP PAY - Option mise en avant -->
                            <div class="payment-card rounded-md border-2 p-4 mb-4 relative {{ $paymentMethod === 'capocop' ? 'selected border-purple-500' : 'border-coolGray-200' }}" wire:click="setPaymentMethod('capocop')">
                                <div class="absolute -top-3 right-4 bg-gradient-to-r from-purple-500 to-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    RECOMMANDÉ
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $paymentMethod === 'capocop' ? 'checked' : '' }}>
                                        <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $paymentMethod === 'capocop' ? 'bg-purple-500 border-purple-500' : '' }}">
                                            @if($paymentMethod === 'capocop')
                                            <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                                <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-white font-black text-sm">CP</span>
                                        </div>
                                        <div>
                                            <span class="block text-gray-800 font-bold">Capocop Pay</span>
                                            <span class="text-xs text-gray-500">Paiement rapide et sécurisé</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($paymentMethod === 'capocop')
                                <div class="capocop-expand mt-4 pt-4 border-t border-coolGray-200" wire:click.stop>
                                    <p class="text-gray-700 font-medium mb-3">Êtes-vous déjà client Capocop ?</p>
                                    <div class="flex gap-3 mb-4">
                                        <button 
                                            wire:click.stop="setCapocopClientType(false)"
                                            class="btn-capocop flex-1 py-2 px-3 rounded-md text-sm font-medium border {{ !$isNewCapocopClient ? 'active' : 'border-coolGray-200 text-gray-600 hover:border-purple-300' }}"
                                        >
                                            Oui, j'ai un compte
                                        </button>
                                        <button 
                                            wire:click.stop="setCapocopClientType(true)"
                                            class="btn-capocop flex-1 py-2 px-3 rounded-md text-sm font-medium border {{ $isNewCapocopClient ? 'active' : 'border-coolGray-200 text-gray-600 hover:border-purple-300' }}"
                                        >
                                            Non, je suis nouveau
                                        </button>
                                    </div>
                                    
                                    @if(!$isNewCapocopClient)
                                    <div class="bg-white rounded-md p-4 border border-coolGray-200">
                                        <label class="block mb-2 text-coolGray-700 font-medium text-sm">Votre identifiant Capocop</label>
                                        <input 
                                            wire:model="capocopId"
                                            wire:click.stop
                                            class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" 
                                            type="text"
                                            placeholder="Ex: CP-123456789"
                                        >
                                        @error('capocopId') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    @else
                                    <div class="bg-purple-50 rounded-md p-4 border border-purple-100">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-purple-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-purple-700 font-medium text-sm">Bienvenue chez Capocop !</p>
                                                <p class="text-purple-600 text-xs mt-1">Un compte sera créé automatiquement avec votre email. Vous recevrez vos identifiants par SMS et email.</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Paiement à la livraison -->
                            <div class="payment-card flex items-center gap-3 mb-4 p-4 rounded-md border-2 {{ $paymentMethod === 'cash' ? 'selected border-purple-500' : 'border-coolGray-200' }}" wire:click="setPaymentMethod('cash')">
                                <div class="relative">
                                    <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $paymentMethod === 'cash' ? 'checked' : '' }}>
                                    <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $paymentMethod === 'cash' ? 'bg-purple-500 border-purple-500' : '' }}">
                                        @if($paymentMethod === 'cash')
                                        <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                            <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                        </svg>
                                        @endif
                                    </div>
                                </div>
                                <label class="block text-gray-700 cursor-pointer">Paiement à la livraison</label>
                            </div>

                            <!-- Mobile Money -->
                            <div class="payment-card flex items-center gap-3 mb-4 p-4 rounded-md border-2 {{ $paymentMethod === 'mobile_money' ? 'selected border-purple-500' : 'border-coolGray-200' }}" wire:click="setPaymentMethod('mobile_money')">
                                <div class="relative">
                                    <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $paymentMethod === 'mobile_money' ? 'checked' : '' }}>
                                    <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $paymentMethod === 'mobile_money' ? 'bg-purple-500 border-purple-500' : '' }}">
                                        @if($paymentMethod === 'mobile_money')
                                        <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                            <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                        </svg>
                                        @endif
                                    </div>
                                </div>
                                <label class="block text-gray-700 cursor-pointer">Mobile Money (MTN, Orange)</label>
                            </div>

                            <!-- Carte bancaire -->
                            <div class="payment-card rounded-md border-2 p-4 mb-2 {{ $paymentMethod === 'card' ? 'selected border-purple-500' : 'border-coolGray-200' }}" wire:click="setPaymentMethod('card')">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <input class="custom-checkbox-1 opacity-0 absolute z-10 h-5 w-5 top-0 left-0" type="checkbox" {{ $paymentMethod === 'card' ? 'checked' : '' }}>
                                        <div class="border border-coolGray-200 w-5 h-5 flex justify-center items-center rounded-sm bg-white {{ $paymentMethod === 'card' ? 'bg-purple-500 border-purple-500' : '' }}">
                                            @if($paymentMethod === 'card')
                                            <svg class="text-white" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                                <path d="M9.76764 0.22597C9.45824 -0.0754185 8.95582 -0.0752285 8.64601 0.22597L3.59787 5.13702L1.35419 2.95437C1.04438 2.65298 0.542174 2.65298 0.23236 2.95437C-0.0774534 3.25576 -0.0774534 3.74431 0.23236 4.0457L3.03684 6.77391C3.19165 6.92451 3.39464 7 3.59765 7C3.80067 7 4.00386 6.9247 4.15867 6.77391L9.76764 1.31727C10.0775 1.01609 10.0775 0.52734 9.76764 0.22597Z" fill="currentColor"></path>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <label class="block text-gray-700 cursor-pointer">Carte bancaire</label>
                                </div>
                                
                                @if($paymentMethod === 'card')
                                <div class="capocop-expand rounded-md bg-white border border-coolGray-200 p-4 flex flex-col gap-4 mt-4" wire:click.stop>
                                    <div>
                                        <label class="block mb-2 text-coolGray-700 font-medium text-sm">Nom sur la carte</label>
                                        <input wire:model="cardName" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="JEAN DUPONT">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-coolGray-700 font-medium text-sm">Numéro de carte</label>
                                        <input wire:model="cardNumber" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-coolGray-700 font-medium text-sm">Date d'expiration</label>
                                        <div class="flex flex-wrap gap-4">
                                            <select wire:model="cardExpMonth" class="py-3 px-4 rounded-sm border border-coolGray-200 flex-1 outline-none focus:ring-1 ring-indigo-400">
                                                <option value="">Mois</option>
                                                @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                                @endfor
                                            </select>
                                            <select wire:model="cardExpYear" class="py-3 px-4 rounded-sm border border-coolGray-200 flex-1 outline-none focus:ring-1 ring-indigo-400">
                                                <option value="">Année</option>
                                                @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-coolGray-700 font-medium text-sm">CVC</label>
                                        <input wire:model="cardCvc" class="w-full py-3 px-4 rounded-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            @error('paymentMethod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Colonne 3: Récapitulatif -->
                <div class="w-full lg:w-1/3 px-2">
                    <div class="bg-coolGray-50 rounded-md mb-8">
                        <div class="step-header p-6 border-b border-coolGray-200 flex justify-between items-center" wire:click="toggleStep(4)">
                            <h2 class="text-rhino-700 text-xl font-semibold font-heading">Récapitulatif</h2>
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-200 p-3 flex items-center justify-center rounded">
                                    <span class="text-xs font-bold text-rhino-800">04</span>
                                </div>
                                <svg class="w-5 h-5 text-rhino-400 chevron-rotate {{ $this->isStepOpen(4) ? 'open' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="step-section {{ $this->isStepOpen(4) ? 'expanded p-6' : 'collapsed' }}">
                            <!-- Articles du panier -->
                            @forelse($cartItems as $item)
                            <div class="flex gap-4 pb-6 border-b border-coolGray-200 mb-4">
                                <div class="bg-coolGray-100 rounded-lg w-20 h-20 flex items-center justify-center flex-shrink-0">
                                    @if($item->attributes->image)
                                        <img src="{{ $item->attributes->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <svg class="w-8 h-8 text-coolGray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                                        <p class="text-sm font-medium text-coolGray-700">{{ $item->name }}</p>
                                        <button wire:click="removeItem('{{ $item->id }}')" class="text-orange-500 hover:text-orange-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M6.66667 12C6.84348 12 7.01305 11.9298 7.13807 11.8048C7.2631 11.6798 7.33333 11.5102 7.33333 11.3334V7.33337C7.33333 7.15656 7.2631 6.98699 7.13807 6.86197C7.01305 6.73695 6.84348 6.66671 6.66667 6.66671C6.48986 6.66671 6.32029 6.73695 6.19526 6.86197C6.07024 6.98699 6 7.15656 6 7.33337V11.3334C6 11.5102 6.07024 11.6798 6.19526 11.8048C6.32029 11.9298 6.48986 12 6.66667 12ZM13.3333 4.00004H10.6667V3.33337C10.6667 2.80294 10.456 2.29423 10.0809 1.91916C9.70581 1.54409 9.1971 1.33337 8.66667 1.33337H7.33333C6.8029 1.33337 6.29419 1.54409 5.91912 1.91916C5.54405 2.29423 5.33333 2.80294 5.33333 3.33337V4.00004H2.66667C2.48986 4.00004 2.32029 4.07028 2.19526 4.1953C2.07024 4.32033 2 4.4899 2 4.66671C2 4.84352 2.07024 5.01309 2.19526 5.13811C2.32029 5.26314 2.48986 5.33337 2.66667 5.33337H3.33333V12.6667C3.33333 13.1971 3.54405 13.7058 3.91912 14.0809C4.29419 14.456 4.8029 14.6667 5.33333 14.6667H10.6667C11.1971 14.6667 11.7058 14.456 12.0809 14.0809C12.456 13.7058 12.6667 13.1971 12.6667 12.6667V5.33337H13.3333C13.5101 5.33337 13.6797 5.26314 13.8047 5.13811C13.9298 5.01309 14 4.84352 14 4.66671C14 4.4899 13.9298 4.32033 13.8047 4.1953C13.6797 4.07028 13.5101 4.00004 13.3333 4.00004Z" fill="currentColor"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="rounded-sm py-2 px-3 border border-coolGray-200 bg-white flex items-center gap-3">
                                            <button wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})" class="cursor-pointer text-coolGray-300 hover:text-coolGray-500 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                    <path d="M12.6666 7.33337H3.33329C3.15648 7.33337 2.98691 7.40361 2.86189 7.52864C2.73686 7.65366 2.66663 7.82323 2.66663 8.00004C2.66663 8.17685 2.73686 8.34642 2.86189 8.47145C2.98691 8.59647 3.15648 8.66671 3.33329 8.66671H12.6666C12.8434 8.66671 13.013 8.59647 13.138 8.47145C13.2631 8.34642 13.3333 8.17685 13.3333 8.00004C13.3333 7.82323 13.2631 7.65366 13.138 7.52864C13.013 7.40361 12.8434 7.33337 12.6666 7.33337Z" fill="currentColor"></path>
                                                </svg>
                                            </button>
                                            <span class="text-sm text-coolGray-700 min-w-[20px] text-center">{{ $item->quantity }}</span>
                                            <button wire:click="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})" class="cursor-pointer text-coolGray-300 hover:text-coolGray-500 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                    <path d="M12.6666 7.33329H8.66663V3.33329C8.66663 3.15648 8.59639 2.98691 8.47136 2.86189C8.34634 2.73686 8.17677 2.66663 7.99996 2.66663C7.82315 2.66663 7.65358 2.73686 7.52855 2.86189C7.40353 2.98691 7.33329 3.15648 7.33329 3.33329V7.33329H3.33329C3.15648 7.33329 2.98691 7.40353 2.86189 7.52855C2.73686 7.65358 2.66663 7.82315 2.66663 7.99996C2.66663 8.17677 2.73686 8.34634 2.86189 8.47136C2.98691 8.59639 3.15648 8.66663 3.33329 8.66663H7.33329V12.6666C7.33329 12.8434 7.40353 13.013 7.52855 13.138C7.65358 13.2631 7.82315 13.3333 7.99996 13.3333C8.17677 13.3333 8.34634 13.2631 8.47136 13.138C8.59639 13.013 8.66663 12.8434 8.66663 12.6666V8.66663H12.6666C12.8434 8.66663 13.013 8.59639 13.138 8.47136C13.2631 8.34634 13.3333 8.17677 13.3333 7.99996C13.3333 7.82315 13.2631 7.65358 13.138 7.52855C13.013 7.40353 12.8434 7.33329 12.6666 7.33329Z" fill="currentColor"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <h2 class="text-rhino-500 font-semibold">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} FCFA</h2>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-coolGray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <p>Votre panier est vide</p>
                            </div>
                            @endforelse

                            <!-- Totaux -->
                            <div class="flex justify-between py-3 px-4 rounded-sm mb-2 border-b border-coolGray-200 pb-4">
                                <p class="uppercase text-xs font-bold text-rhino-300">Sous-total</p>
                                <p class="text-rhino-800 text-xs font-bold">{{ number_format($subtotal, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="flex justify-between py-3 px-4 rounded-sm mb-2 border-b border-coolGray-200 pb-4">
                                <p class="uppercase text-xs font-bold text-rhino-300">Livraison</p>
                                <p class="text-rhino-800 text-xs font-bold">{{ $shippingCost == 0 ? 'Gratuit' : number_format($shippingCost, 0, ',', ' ') . ' FCFA' }}</p>
                            </div>
                            @if($discountPercent > 0)
                            <div class="flex justify-between py-3 px-4 mb-4 border-b border-coolGray-200 pb-4">
                                <div class="uppercase bg-orange-500 rounded-xl py-1 px-3 text-white text-xs font-bold tracking-widest">Réduction</div>
                                <p class="text-orange-500 text-xs font-bold">-{{ $discountPercent }}%</p>
                            </div>
                            @endif
                            <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
                                <h2 class="text-coolGray-800 text-lg font-semibold">Total</h2>
                                <p class="text-purple-500 text-lg font-semibold">{{ number_format($total, 0, ',', ' ') }} FCFA</p>
                            </div>

                            <!-- Code promo et commentaire -->
                            <div class="rounded-md bg-white border border-coolGray-200 p-6 flex flex-col gap-4 mb-4">
                                <div class="mb-4">
                                    <label class="text-sm text-coolGray-700 font-medium mb-2 block">Code promo</label>
                                    <div class="flex flex-wrap lg:flex-nowrap gap-4">
                                        <input wire:model="discountCode" class="flex-1 rounded-sm py-3 px-4 bg-white text-coolGray-700 text-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400" type="text" placeholder="Entrez votre code">
                                        <button wire:click="applyDiscount" class="inline-block text-sm text-purple-500 font-medium py-3 px-4 rounded-sm border border-purple-500 hover:bg-purple-500 hover:text-white transition duration-200">Appliquer</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-coolGray-700 font-medium mb-2 block">Commentaire (optionnel)</label>
                                    <textarea wire:model="orderComment" class="w-full mb-4 rounded-sm py-3 px-4 bg-white text-coolGray-700 text-sm border border-coolGray-200 outline-none focus:ring-1 ring-indigo-400 resize-none" rows="4" placeholder="Instructions spéciales..."></textarea>
                                    
                                    @error('general') 
                                    <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm">{{ $message }}</div>
                                    @enderror
                                    
                                    <button 
                                        wire:click="confirmOrder"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-75"
                                        class="inline-block w-full px-3 py-4 rounded-sm text-center text-white text-sm font-medium bg-purple-500 hover:bg-purple-600 transition duration-200 disabled:cursor-not-allowed"
                                        {{ count($cartItems) === 0 ? 'disabled' : '' }}
                                    >
                                        <span wire:loading.remove>Confirmer la commande</span>
                                        <span wire:loading>Traitement en cours...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
