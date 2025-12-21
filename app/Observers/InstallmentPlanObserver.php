<?php

namespace App\Observers;

use App\Models\InstallmentPlan;
use App\Notifications\InstallmentPlanCreatedNotification;
use Carbon\Carbon;

class InstallmentPlanObserver
{
    public function created(InstallmentPlan $plan): void
    {
        // Create installments
        $installmentAmount = ($plan->total_amount - $plan->deposit_amount) / $plan->number_of_installments;
        $startDate = Carbon::now()->addDays($plan->interval_days);

        for ($i = 0; $i < $plan->number_of_installments; $i++) {
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


















