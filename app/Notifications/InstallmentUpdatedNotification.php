<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;
use Filament\Notifications\Notification;

class InstallmentUpdatedNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Installment $installment,
        public array $changes = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->installment->plan;
        $order = $plan->order;
        $isAdmin = $notifiable->role === 'admin';

        $mail = (new MailMessage)
            ->subject($isAdmin 
                ? 'Échéance modifiée - Commande #' . substr($order->id, 0, 8)
                : 'Modification de votre échéance - Commande #' . substr($order->id, 0, 8))
            ->greeting('Bonjour ' . ($notifiable->first_name ?? $notifiable->name ?? 'Client') . ',');

        if ($isAdmin) {
            $mail->line('Une échéance a été modifiée pour la commande #' . substr($order->id, 0, 8) . '.');
        } else {
            $mail->line('Votre échéance a été modifiée pour votre commande.');
        }

        $mail->line('**Détails de l\'échéance :**')
            ->line('• Montant : **' . number_format($this->installment->amount, 0, ',', ' ') . ' XAF**')
            ->line('• Date d\'échéance : **' . $this->installment->due_date->format('d/m/Y') . '**')
            ->line('• Statut : **' . $this->getStatusLabel($this->installment->status) . '**');

        // Afficher les changements si disponibles
        if (!empty($this->changes)) {
            $mail->line('**Modifications apportées :**');
            foreach ($this->changes as $field => $change) {
                $oldValue = $change['old'] ?? 'N/A';
                $newValue = $change['new'] ?? 'N/A';
                
                $fieldLabel = match($field) {
                    'status' => 'Statut',
                    'due_date' => 'Date d\'échéance',
                    'amount' => 'Montant',
                    'paid_at' => 'Date de paiement',
                    default => ucfirst(str_replace('_', ' ', $field)),
                };

                if ($field === 'status') {
                    $oldValue = $this->getStatusLabel($oldValue);
                    $newValue = $this->getStatusLabel($newValue);
                } elseif ($field === 'due_date' || $field === 'paid_at') {
                    $oldValue = $oldValue !== 'N/A' ? date('d/m/Y', strtotime($oldValue)) : 'N/A';
                    $newValue = $newValue !== 'N/A' ? date('d/m/Y', strtotime($newValue)) : 'N/A';
                } elseif ($field === 'amount') {
                    $oldValue = $oldValue !== 'N/A' ? number_format($oldValue, 0, ',', ' ') . ' XAF' : 'N/A';
                    $newValue = $newValue !== 'N/A' ? number_format($newValue, 0, ',', ' ') . ' XAF' : 'N/A';
                }

                $mail->line('• ' . $fieldLabel . ' : **' . $oldValue . '** → **' . $newValue . '**');
            }
        }

        if ($this->installment->paid_at) {
            $mail->line('• Date de paiement : **' . $this->installment->paid_at->format('d/m/Y à H:i') . '**');
        }

        $mail->action('Voir la commande', url('/capocopadmin/orders/' . $order->id))
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->salutation('Cordialement, L\'équipe Capocop');

        return $mail;
    }

    public function toDatabase(object $notifiable): array
    {
        $plan = $this->installment->plan;
        $order = $plan->order;
        $isAdmin = $notifiable->role === 'admin';

        $statusLabel = $this->getStatusLabel($this->installment->status);
        $changesText = '';

        if (!empty($this->changes)) {
            $changeLabels = [];
            foreach ($this->changes as $field => $change) {
                $fieldLabel = match($field) {
                    'status' => 'Statut',
                    'due_date' => 'Date',
                    'amount' => 'Montant',
                    'paid_at' => 'Date de paiement',
                    default => ucfirst(str_replace('_', ' ', $field)),
                };
                $changeLabels[] = $fieldLabel;
            }
            $changesText = ' (' . implode(', ', $changeLabels) . ')';
        }

        return Notification::make()
            ->title($isAdmin 
                ? 'Échéance modifiée - Commande #' . substr($order->id, 0, 8)
                : 'Votre échéance a été modifiée')
            ->body('Échéance #' . substr($this->installment->id, 0, 8) . ' - ' . number_format($this->installment->amount, 0, ',', ' ') . ' XAF - Statut: ' . $statusLabel . $changesText)
            ->info()
            ->getDatabaseMessage();
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'paid' => 'Payée',
            'overdue' => 'En retard',
            default => $status,
        };
    }
}

