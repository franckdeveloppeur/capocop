<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        // Update order status when payment is successful
        if ($payment->status === 'success' && $payment->order) {
            $payment->order->update(['status' => 'paid']);
        }
    }

    public function updated(Payment $payment): void
    {
        // Update order status when payment status changes
        if ($payment->wasChanged('status') && $payment->order) {
            if ($payment->status === 'success') {
                $payment->order->update(['status' => 'paid']);
            } elseif ($payment->status === 'failed') {
                $payment->order->update(['status' => 'pending']);
            }
        }
    }
}





