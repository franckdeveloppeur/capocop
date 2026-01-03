<?php

namespace App\Observers;

use App\Models\Installment;
use App\Models\User;
use App\Notifications\InstallmentPaidNotification;
use App\Notifications\InstallmentDueNotification;
use App\Notifications\InstallmentUpdatedNotification;
use Carbon\Carbon;

class InstallmentObserver
{
    public function updated(Installment $installment): void
    {
        $plan = $installment->plan;
        $changes = [];
        $wasStatusChanged = false;
        $statusChangedToPaid = false;

        // Détecter les changements
        if ($installment->wasChanged('status')) {
            $wasStatusChanged = true;
            $oldStatus = $installment->getOriginal('status');
            $newStatus = $installment->status;
            
            $changes['status'] = [
                'old' => $oldStatus,
                'new' => $newStatus,
            ];

            // Si le statut est passé à "paid", utiliser la notification spécifique
            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                $statusChangedToPaid = true;
            }
        }

        if ($installment->wasChanged('due_date')) {
            $changes['due_date'] = [
                'old' => $installment->getOriginal('due_date'),
                'new' => $installment->due_date,
            ];
        }

        if ($installment->wasChanged('amount')) {
            $changes['amount'] = [
                'old' => $installment->getOriginal('amount'),
                'new' => $installment->amount,
            ];
        }

        if ($installment->wasChanged('paid_at')) {
            $changes['paid_at'] = [
                'old' => $installment->getOriginal('paid_at'),
                'new' => $installment->paid_at,
            ];
        }

        // Si le statut est passé à "paid", utiliser la notification spécifique
        if ($statusChangedToPaid) {
            $allPaid = $plan->installments()->where('status', '!=', 'paid')->count() === 0;
            
            if ($allPaid) {
                $plan->update(['status' => 'completed']);
            }

            // Send payment confirmation notification to user
            if ($plan->order && $plan->order->user) {
                \Log::info('Envoi notification InstallmentPaidNotification à l\'utilisateur', [
                    'user_id' => $plan->order->user->id,
                    'user_email' => $plan->order->user->email,
                    'installment_id' => $installment->id,
                ]);
                $plan->order->user->notify(new InstallmentPaidNotification($installment));
            }

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            \Log::info('Envoi notification InstallmentPaidNotification aux admins', [
                'admins_count' => $admins->count(),
                'installment_id' => $installment->id,
            ]);
            foreach ($admins as $admin) {
                $admin->notify(new InstallmentPaidNotification($installment));
            }
        } 
        // Sinon, envoyer une notification de modification générale
        elseif (!empty($changes)) {
            \Log::info('InstallmentObserver::updated - Modifications détectées', [
                'installment_id' => $installment->id,
                'changes' => array_keys($changes),
                'has_plan' => $plan !== null,
                'has_order' => $plan && $plan->order !== null,
                'has_user' => $plan && $plan->order && $plan->order->user !== null,
            ]);

            // Send update notification to user
            if ($plan && $plan->order && $plan->order->user) {
                \Log::info('Envoi notification InstallmentUpdatedNotification à l\'utilisateur', [
                    'user_id' => $plan->order->user->id,
                    'user_email' => $plan->order->user->email,
                    'installment_id' => $installment->id,
                    'changes' => array_keys($changes),
                ]);
                $plan->order->user->notify(new InstallmentUpdatedNotification($installment, $changes));
            }

            // Send notification to all admins
            $admins = User::where('role', 'admin')->get();
            \Log::info('Envoi notification InstallmentUpdatedNotification aux admins', [
                'admins_count' => $admins->count(),
                'installment_id' => $installment->id,
                'changes' => array_keys($changes),
            ]);
            foreach ($admins as $admin) {
                $admin->notify(new InstallmentUpdatedNotification($installment, $changes));
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
                // Send notification to user
                $installment->plan->order->user->notify(
                    (new InstallmentDueNotification($installment))->delay($reminderDate)
                );

                // Also notify admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(
                        (new InstallmentDueNotification($installment))->delay($reminderDate)
                    );
                }
            }
        }
    }
}





















