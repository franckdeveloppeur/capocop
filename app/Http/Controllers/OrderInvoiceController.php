<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderInvoiceController extends Controller
{
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur peut accéder à cette facture
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Vous n\'avez pas accès à cette facture.');
        }

        // Charger les relations nécessaires
        $order->load([
            'user',
            'address',
            'items.product.media',
            'items.variant',
            'shop',
            'payments',
            'installmentPlan.installments'
        ]);

        return view('orders.invoice', compact('order'));
    }
}
