<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
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

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ];
    }
}






