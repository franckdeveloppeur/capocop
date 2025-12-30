<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class InstallmentPaidNotification extends BaseNotification implements ShouldQueue
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
        $paidCount = $plan->installments()->where('status', 'paid')->count();
        $totalCount = $plan->number_of_installments;
        $remainingCount = $totalCount - $paidCount;
        $nextInstallment = $plan->installments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        return (new MailMessage)
            ->subject('Paiement reçu - Échéance #' . $paidCount . '/' . $totalCount)
            ->greeting('Bonjour ' . ($notifiable->first_name ?? $notifiable->name ?? 'Client') . ',')
            ->line('Nous avons bien reçu votre paiement pour cette échéance.')
            ->line('**Détails du paiement :**')
            ->line('• Montant payé : **' . number_format($this->installment->amount, 0, ',', ' ') . ' FCFA**')
            ->line('• Date de paiement : **' . ($this->installment->paid_at ? $this->installment->paid_at->format('d/m/Y à H:i') : now()->format('d/m/Y à H:i')) . '**')
            ->line('• Commande : **#' . $plan->order_id . '**')
            ->line('**Progression :**')
            ->line('• Échéances payées : **' . $paidCount . '/' . $totalCount . '**')
            ->line('• Échéances restantes : **' . $remainingCount . '**')
            ->when($nextInstallment, function ($mail) use ($nextInstallment) {
                return $mail->line('• Prochaine échéance : **' . $nextInstallment->due_date->format('d/m/Y') . '** (' . number_format($nextInstallment->amount, 0, ',', ' ') . ' FCFA)');
            })
            ->when($remainingCount === 0, function ($mail) {
                return $mail->line('')
                    ->line('🎉 **Félicitations !** Vous avez terminé de payer toutes vos échéances.');
            })
            ->action('Voir mon plan de paiement', url('/orders/' . $plan->order_id))
            ->salutation('Cordialement, L\'équipe Capocop');
    }

    public function toDatabase(object $notifiable): array
    {
        $plan = $this->installment->plan;
        $paidCount = $plan->installments()->where('status', 'paid')->count();
        $totalCount = $plan->number_of_installments;
        $isAdmin = $notifiable->role === 'admin';

        return \Filament\Notifications\Notification::make()
            ->title($isAdmin ? 'Échéance payée' : 'Votre échéance a été payée')
            ->body('Échéance #' . $paidCount . '/' . $totalCount . ' - ' . number_format($this->installment->amount, 0, ',', ' ') . ' FCFA - Commande #' . $plan->order_id)
            ->success()
            ->getDatabaseMessage();
    }
}

