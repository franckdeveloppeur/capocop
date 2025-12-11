<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function viewcommandes()
    {
        $orders = \App\Models\Order::where('user_id', auth()->id())
            ->with(['items.product.media', 'items.variant', 'shipment', 'address'])
            ->latest()
            ->get();

        return view('orders.commandes', compact('orders'));
    }
}
