<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstallmentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Installment $installment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->installment->plan;
        $remainingInstallments = $plan->installments()
            ->where('status', 'pending')
            ->where('id', '!=', $this->installment->id)
            ->count();

        return (new MailMessage)
            ->subject('Rappel : Échéance de paiement - ' . number_format($this->installment->amount, 0, ',', ' ') . ' FCFA')
            ->greeting('Bonjour ' . ($notifiable->first_name ?? $notifiable->name ?? 'Client') . ',')
            ->line('Nous vous rappelons qu\'une échéance de votre plan de paiement échelonné arrive à échéance.')
            ->line('**Détails de l\'échéance :**')
            ->line('• Montant à payer : **' . number_format($this->installment->amount, 0, ',', ' ') . ' FCFA**')
            ->line('• Date d\'échéance : **' . $this->installment->due_date->format('d/m/Y') . '**')
            ->line('• Commande : **#' . $plan->order_id . '**')
            ->line('• Échéances restantes : **' . $remainingInstallments . '**')
            ->line('Veuillez effectuer le paiement avant la date d\'échéance pour éviter tout retard.')
            ->action('Payer maintenant', url('/orders/' . $plan->order_id . '/installments/' . $this->installment->id . '/pay'))
            ->line('Si vous avez déjà effectué le paiement, veuillez ignorer ce message.')
            ->salutation('Cordialement, L\'équipe Capocop');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'installment_id' => $this->installment->id,
            'plan_id' => $this->installment->plan_id,
            'amount' => $this->installment->amount,
            'due_date' => $this->installment->due_date->toDateString(),
            'status' => $this->installment->status,
        ];
    }
}

