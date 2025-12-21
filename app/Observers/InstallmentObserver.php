<?php

namespace App\Observers;

use App\Models\Installment;
use App\Notifications\InstallmentPaidNotification;
use Carbon\Carbon;

class InstallmentObserver
{
    public function updated(Installment $installment): void
    {
        // Update plan status when installment is paid
        if ($installment->wasChanged('status') && $installment->status === 'paid') {
            $plan = $installment->plan;
            $allPaid = $plan->installments()->where('status', '!=', 'paid')->count() === 0;
            
            if ($allPaid) {
                $plan->update(['status' => 'completed']);
            }

            // Send payment confirmation notification
            if ($plan->order && $plan->order->user) {
                $plan->order->user->notify(new InstallmentPaidNotification($installment));
            }
        }

        // Mark as overdue if past due date
        if ($installment->status === 'pending' && $installment->due_date < now()) {
            $installment->update(['status' => 'overdue']);
        }
    }

    public function created(Installment $installment): void
    {
        // Schedule reminder notification 3 days before due date
        if ($installment->due_date && $installment->status === 'pending') {
            $reminderDate = Carbon::parse($installment->due_date)->subDays(3);
            
            if ($reminderDate->isFuture() && $installment->plan && $installment->plan->order && $installment->plan->order->user) {
                $installment->plan->order->user->notify(
                    (new \App\Notifications\InstallmentDueNotification($installment))
                        ->delay($reminderDate)
                );
            }
        }
    }
}


















