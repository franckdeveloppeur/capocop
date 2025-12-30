<?php

namespace App\Notifications;

use App\Models\Order;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class OrderCreatedNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle commande #' . $this->order->id)
            ->line('Votre commande a été créée avec succès.')
            ->line('Montant total: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' XOF')
            ->action('Voir la commande', url('/orders/' . $this->order->id));
    }

    public function toDatabase(object $notifiable): array
    {
        $isAdmin = $notifiable->role === 'admin';
        
        return Notification::make()
            ->title($isAdmin ? 'Nouvelle commande créée' : 'Commande créée avec succès')
            ->body('Commande #' . $this->order->id . ' - Montant: ' . number_format($this->order->total_amount, 0, ',', ' ') . ' XOF')
            ->success()
            ->getDatabaseMessage();
    }
}





















