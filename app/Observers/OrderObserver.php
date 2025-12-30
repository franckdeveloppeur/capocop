<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusChangedNotification;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Send notification to user
        if ($order->user) {
            $order->user->notify(new OrderCreatedNotification($order));
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new OrderCreatedNotification($order));
        }
    }

    public function updated(Order $order): void
    {
        // Send notification on status change
        if ($order->wasChanged('status') && $order->user) {
            $oldStatus = $order->getOriginal('status');
            $order->user->notify(new OrderStatusChangedNotification($order, $oldStatus));

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new OrderStatusChangedNotification($order, $oldStatus));
            }
        }
    }
}

