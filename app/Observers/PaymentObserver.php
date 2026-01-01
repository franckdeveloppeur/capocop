<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentSuccessNotification;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        \Log::info('PaymentObserver::created déclenché', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'has_order' => $payment->order !== null,
            'queue_connection' => config('queue.default'),
        ]);

        // Update order status when payment is successful
        if ($payment->status === 'success' && $payment->order) {
            $payment->order->update(['status' => 'paid']);

            // Send notification to user
            if ($payment->order->user) {
                \Log::info('Envoi notification PaymentSuccessNotification à l\'utilisateur', [
                    'user_id' => $payment->order->user->id,
                    'user_email' => $payment->order->user->email,
                ]);
                $payment->order->user->notify(new PaymentSuccessNotification($payment));
            }

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            \Log::info('Envoi notification PaymentSuccessNotification aux admins', [
                'admins_count' => $admins->count(),
            ]);
            foreach ($admins as $admin) {
                $admin->notify(new PaymentSuccessNotification($payment));
            }
        }
    }

    public function updated(Payment $payment): void
    {
        // Update order status when payment status changes
        if ($payment->wasChanged('status') && $payment->order) {
            if ($payment->status === 'success') {
                $payment->order->update(['status' => 'paid']);

                // Send notification to user
                if ($payment->order->user) {
                    $payment->order->user->notify(new PaymentSuccessNotification($payment));
                }

                // Send notification to all admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new PaymentSuccessNotification($payment));
                }
            } elseif ($payment->status === 'failed') {
                $payment->order->update(['status' => 'pending']);
            }
        }
    }
}





















