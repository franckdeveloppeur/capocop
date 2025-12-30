<?php

namespace App\Observers;

use App\Models\OrderReturn;
use App\Models\User;
use App\Notifications\OrderReturnNotification;

class OrderReturnObserver
{
    /**
     * Handle the OrderReturn "created" event.
     */
    public function created(OrderReturn $orderReturn): void
    {
        // Charger les relations nécessaires
        $orderReturn->load(['order', 'orderItem.product', 'user']);

        // Envoyer une notification à l'utilisateur qui a créé le retour
        if ($orderReturn->user) {
            $orderReturn->user->notify(new OrderReturnNotification($orderReturn, 'created'));
        }

        // Envoyer une notification à tous les administrateurs
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new OrderReturnNotification($orderReturn, 'created'));
        }
    }

    /**
     * Handle the OrderReturn "updated" event.
     */
    public function updated(OrderReturn $orderReturn): void
    {
        // Charger les relations nécessaires
        $orderReturn->load(['order', 'orderItem.product', 'user']);

        // Envoyer une notification uniquement si le statut a changé
        if ($orderReturn->wasChanged('status')) {
            $oldStatus = $orderReturn->getOriginal('status');
            
            // Envoyer une notification à l'utilisateur
            if ($orderReturn->user) {
                $orderReturn->user->notify(new OrderReturnNotification($orderReturn, 'status_changed', $oldStatus));
            }

            // Envoyer une notification à tous les administrateurs
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new OrderReturnNotification($orderReturn, 'status_changed', $oldStatus));
            }
        }
    }

    /**
     * Handle the OrderReturn "deleted" event.
     */
    public function deleted(OrderReturn $orderReturn): void
    {
        //
    }

    /**
     * Handle the OrderReturn "restored" event.
     */
    public function restored(OrderReturn $orderReturn): void
    {
        //
    }

    /**
     * Handle the OrderReturn "force deleted" event.
     */
    public function forceDeleted(OrderReturn $orderReturn): void
    {
        //
    }
}
