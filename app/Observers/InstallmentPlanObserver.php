<?php

namespace App\Observers;

use App\Models\InstallmentPlan;
use App\Notifications\InstallmentPlanCreatedNotification;
use Carbon\Carbon;

class InstallmentPlanObserver
{
    public function created(InstallmentPlan $plan): void
    {
        // Create installments (excluding the first payment which is the deposit)
        // If number_of_installments is 12, we create 11 remaining installments
        $remainingInstallments = $plan->number_of_installments - 1;
        $installmentAmount = $remainingInstallments > 0 
            ? ($plan->total_amount - $plan->deposit_amount) / $remainingInstallments 
            : 0;
        $startDate = Carbon::now()->addDays($plan->interval_days);

        for ($i = 0; $i < $remainingInstallments; $i++) {
            $plan->installments()->create([
                'due_date' => $startDate->copy()->addDays($i * $plan->interval_days),
                'amount' => $installmentAmount,
                'status' => 'pending',
            ]);
        }

        // Send notification to user
        if ($plan->order && $plan->order->user) {
            $plan->order->user->notify(new InstallmentPlanCreatedNotification($plan));
        }
    }
}





















