<?php

namespace App\Observers;

use App\Models\InstallmentPlan;
use App\Models\User;
use App\Notifications\InstallmentPlanCreatedNotification;
use Carbon\Carbon;

class InstallmentPlanObserver
{
    public function created(InstallmentPlan $plan): void
    {
        \Log::info('InstallmentPlanObserver::created déclenché', [
            'plan_id' => $plan->id,
            'order_id' => $plan->order_id,
            'has_order' => $plan->order !== null,
            'has_user' => $plan->order && $plan->order->user !== null,
            'queue_connection' => config('queue.default'),
        ]);

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
            \Log::info('Envoi notification InstallmentPlanCreatedNotification à l\'utilisateur', [
                'user_id' => $plan->order->user->id,
                'user_email' => $plan->order->user->email,
            ]);
            $plan->order->user->notify(new InstallmentPlanCreatedNotification($plan));
        }

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        \Log::info('Envoi notification InstallmentPlanCreatedNotification aux admins', [
            'admins_count' => $admins->count(),
        ]);
        foreach ($admins as $admin) {
            $admin->notify(new InstallmentPlanCreatedNotification($plan));
        }
    }
}





















