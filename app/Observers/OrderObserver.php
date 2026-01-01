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
        \Log::info('OrderObserver::created déclenché', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'has_user' => $order->user !== null,
            'queue_connection' => config('queue.default'),
        ]);

        // Send notification to user
        if ($order->user) {
            \Log::info('Envoi notification OrderCreatedNotification à l\'utilisateur', [
                'user_id' => $order->user->id,
                'user_email' => $order->user->email,
            ]);
            $order->user->notify(new OrderCreatedNotification($order));
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        \Log::info('Envoi notification OrderCreatedNotification aux admins', [
            'admins_count' => $admins->count(),
        ]);
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

