<?php

namespace App\Notifications;

use App\Models\InstallmentPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentPlanCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public InstallmentPlan $plan
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $firstInstallment = $this->plan->installments()->orderBy('due_date')->first();
        $monthlyAmount = ($this->plan->total_amount - $this->plan->deposit_amount) / $this->plan->number_of_installments;

        return (new MailMessage)
            ->subject('Plan de paiement échelonné créé - Commande #' . $this->plan->order->id)
            ->greeting('Bonjour ' . ($notifiable->first_name ?? $notifiable->name ?? 'Client') . ',')
            ->line('Votre plan de paiement échelonné a été créé avec succès pour votre commande.')
            ->line('**Détails du plan :**')
            ->line('• Montant total : **' . number_format($this->plan->total_amount, 0, ',', ' ') . ' FCFA**')
            ->line('• Acompte payé : **' . number_format($this->plan->deposit_amount, 0, ',', ' ') . ' FCFA**')
            ->line('• Nombre d\'échéances : **' . $this->plan->number_of_installments . ' mois**')
            ->line('• Montant par échéance : **' . number_format($monthlyAmount, 0, ',', ' ') . ' FCFA**')
            ->line('• Prochaine échéance : **' . ($firstInstallment ? $firstInstallment->due_date->format('d/m/Y') : 'N/A') . '**')
            ->line('Vous recevrez un rappel avant chaque échéance.')
            ->action('Voir mon plan de paiement', url('/orders/' . $this->plan->order_id))
            ->salutation('Cordialement, L\'équipe Capocop');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'plan_id' => $this->plan->id,
            'order_id' => $this->plan->order_id,
            'total_amount' => $this->plan->total_amount,
            'number_of_installments' => $this->plan->number_of_installments,
            'status' => $this->plan->status,
        ];
    }
}

