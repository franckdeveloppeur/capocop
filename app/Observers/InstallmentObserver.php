<?php

namespace App\Observers;

use App\Models\Installment;

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
        }

        // Mark as overdue if past due date
        if ($installment->status === 'pending' && $installment->due_date < now()) {
            $installment->update(['status' => 'overdue']);
        }
    }
}







