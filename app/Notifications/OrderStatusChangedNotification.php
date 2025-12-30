<?php

namespace App\Notifications;

use App\Models\Order;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class OrderStatusChangedNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $oldStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'paid' => 'Payée',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
        ];

        return (new MailMessage)
            ->subject('Statut de commande mis à jour')
            ->line('Le statut de votre commande #' . $this->order->id . ' a été mis à jour.')
            ->line('Nouveau statut: ' . ($statusLabels[$this->order->status] ?? $this->order->status));
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'paid' => 'Payée',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
        ];

        $isAdmin = $notifiable->role === 'admin';
        $newStatusLabel = $statusLabels[$this->order->status] ?? $this->order->status;
        
        $notification = Notification::make()
            ->title($isAdmin ? 'Statut de commande mis à jour' : 'Votre commande a été mise à jour')
            ->body('Commande #' . $this->order->id . ' - Nouveau statut: ' . $newStatusLabel);

        // Définir le type selon le statut
        if (in_array($this->order->status, ['delivered', 'paid'])) {
            $notification->success();
        } elseif ($this->order->status === 'cancelled') {
            $notification->danger();
        } elseif (in_array($this->order->status, ['processing', 'shipped'])) {
            $notification->info();
        } else {
            $notification->warning();
        }

        return $notification->getDatabaseMessage();
    }
}





















