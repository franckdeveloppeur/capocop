<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Send notification to user
        if ($order->user) {
            // Notification will be sent via queue
        }
    }

    public function updated(Order $order): void
    {
        // Send notification on status change
        if ($order->wasChanged('status')) {
            // Handle status change notifications
        }
    }
}

