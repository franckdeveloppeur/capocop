<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderCreatedNotification;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Send notification to user
        if ($order->user) {
            $order->user->notify(new OrderCreatedNotification($order));
        }
    }

    public function updated(Order $order): void
    {
        // Send notification on status change
        if ($order->wasChanged('status') && $order->user) {
            $oldStatus = $order->getOriginal('status');
            $order->user->notify(new \App\Notifications\OrderStatusChangedNotification($order, $oldStatus));
        }
    }
}

