<?php

namespace App\Notifications;

use App\Models\Payment;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class PaymentSuccessNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        \Log::info('PaymentSuccessNotification::toMail appelé - Email sera envoyé', [
            'payment_id' => $this->payment->id,
            'notifiable_id' => $notifiable->id,
            'notifiable_email' => $notifiable->email,
            'queue_connection' => config('queue.default'),
        ]);

        return (new MailMessage)
            ->subject('Paiement réussi')
            ->line('Votre paiement de ' . number_format($this->payment->amount, 0, ',', ' ') . ' XAF a été effectué avec succès.')
            ->line('Référence: ' . $this->payment->transaction_ref);
    }

    public function toDatabase(object $notifiable): array
    {
        $isAdmin = $notifiable->role === 'admin';
        
        return Notification::make()
            ->title($isAdmin ? 'Paiement réussi reçu' : 'Paiement réussi')
            ->body('Paiement de ' . number_format($this->payment->amount, 0, ',', ' ') . ' XAF effectué avec succès. Référence: ' . $this->payment->transaction_ref)
            ->success()
            ->getDatabaseMessage();
    }
}





















