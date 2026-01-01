<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class NewCapocopClientNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $order = $this->order;
        
        return (new MailMessage)
            ->subject('Bienvenue chez Capocop - Finalisez votre compte')
            ->view('emails.new-capocop-client', [
                'user' => $user,
                'order' => $order,
                'logoUrl' => asset('logo.png'),
            ]);
    }
}

