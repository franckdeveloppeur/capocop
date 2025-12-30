<?php

namespace App\Notifications;

use App\Models\OrderReturn;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class OrderReturnNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public OrderReturn $orderReturn,
        public string $eventType = 'created',
        public ?string $oldStatus = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderReturn = $this->orderReturn;
        $order = $orderReturn->order;
        $orderItem = $orderReturn->orderItem;
        $product = $orderItem->product ?? null;
        $productName = $product->title ?? 'Produit supprimé';

        $statusLabels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'processing' => 'En traitement',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        $reasonLabels = [
            'defective' => 'Produit défectueux',
            'wrong_item' => 'Mauvais article reçu',
            'not_as_described' => 'Ne correspond pas à la description',
            'damaged' => 'Article endommagé',
            'size_issue' => 'Problème de taille',
            'color_issue' => 'Problème de couleur',
            'other' => 'Autre raison',
        ];

        $isAdmin = $notifiable->role === 'admin';

        if ($this->eventType === 'created') {
            $subject = $isAdmin 
                ? 'Nouvelle demande de retour - Commande #' . $order->id
                : 'Demande de retour enregistrée - Commande #' . $order->id;

            $message = (new MailMessage)
                ->subject($subject)
                ->line($isAdmin 
                    ? 'Une nouvelle demande de retour a été créée pour la commande #' . $order->id
                    : 'Votre demande de retour a été enregistrée avec succès.')
                ->line('Produit: ' . $productName)
                ->line('Raison: ' . ($reasonLabels[$orderReturn->reason] ?? $orderReturn->reason))
                ->line('Montant de remboursement: ' . number_format($orderReturn->refund_amount, 0, ',', ' ') . ' XOF');

            if ($isAdmin) {
                $message->line('Client: ' . ($orderReturn->user->name ?? 'N/A'))
                    ->line('Description: ' . $orderReturn->description)
                    ->action('Voir la demande de retour', url('/admin/order-returns/' . $orderReturn->id));
            } else {
                $message->line('Votre demande sera traitée par notre équipe dans les plus brefs délais.')
                    ->action('Voir ma commande', url('/myAccount/orders/' . $order->id));
            }

            return $message;
        } elseif ($this->eventType === 'status_changed') {
            $newStatusLabel = $statusLabels[$orderReturn->status] ?? $orderReturn->status;
            $oldStatusLabel = $this->oldStatus ? ($statusLabels[$this->oldStatus] ?? $this->oldStatus) : null;

            $subject = $isAdmin
                ? 'Statut de retour mis à jour - Commande #' . $order->id
                : 'Mise à jour de votre demande de retour - Commande #' . $order->id;

            $message = (new MailMessage)
                ->subject($subject)
                ->line('Le statut de la demande de retour pour la commande #' . $order->id . ' a été mis à jour.')
                ->line('Produit: ' . $productName);

            if ($oldStatusLabel) {
                $message->line('Ancien statut: ' . $oldStatusLabel);
            }

            $message->line('Nouveau statut: ' . $newStatusLabel);

            // Ajouter des informations spécifiques selon le statut
            if ($orderReturn->status === 'approved') {
                $message->line('Votre demande de retour a été approuvée. Le remboursement sera traité sous peu.');
            } elseif ($orderReturn->status === 'rejected') {
                $message->line('Votre demande de retour a été rejetée.');
                if ($orderReturn->admin_notes) {
                    $message->line('Note: ' . $orderReturn->admin_notes);
                }
            } elseif ($orderReturn->status === 'completed') {
                $message->line('Votre retour a été traité et le remboursement a été effectué.');
            }

            if ($isAdmin) {
                $message->action('Voir la demande de retour', url('/admin/order-returns/' . $orderReturn->id));
            } else {
                $message->action('Voir ma commande', url('/myAccount/orders/' . $order->id));
            }

            return $message;
        }

        return (new MailMessage)
            ->subject('Notification de retour')
            ->line('Une mise à jour concernant votre retour a été effectuée.');
    }

    public function toDatabase(object $notifiable): array
    {
        $orderReturn = $this->orderReturn;
        $order = $orderReturn->order;
        $orderItem = $orderReturn->orderItem;
        $product = $orderItem->product ?? null;
        $productName = $product->title ?? 'Produit supprimé';

        $statusLabels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'processing' => 'En traitement',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        $reasonLabels = [
            'defective' => 'Produit défectueux',
            'wrong_item' => 'Mauvais article reçu',
            'not_as_described' => 'Ne correspond pas à la description',
            'damaged' => 'Article endommagé',
            'size_issue' => 'Problème de taille',
            'color_issue' => 'Problème de couleur',
            'other' => 'Autre raison',
        ];

        $isAdmin = $notifiable->role === 'admin';

        if ($this->eventType === 'created') {
            $notification = Notification::make()
                ->title($isAdmin 
                    ? 'Nouvelle demande de retour'
                    : 'Demande de retour enregistrée')
                ->body('Commande #' . substr($order->id, 0, 8) . ' - ' . $productName . ' - ' . number_format($orderReturn->refund_amount, 0, ',', ' ') . ' XOF');

            if ($isAdmin) {
                $notification->warning()
                    ->icon('heroicon-o-arrow-path');
            } else {
                $notification->info()
                    ->icon('heroicon-o-check-circle');
            }

            return $notification->getDatabaseMessage();
        } elseif ($this->eventType === 'status_changed') {
            $newStatusLabel = $statusLabels[$orderReturn->status] ?? $orderReturn->status;

            $notification = Notification::make()
                ->title($isAdmin 
                    ? 'Statut de retour mis à jour'
                    : 'Mise à jour de votre retour')
                ->body('Commande #' . substr($order->id, 0, 8) . ' - ' . $productName . ' - Statut: ' . $newStatusLabel);

            // Définir le type selon le statut
            if ($orderReturn->status === 'approved' || $orderReturn->status === 'completed') {
                $notification->success()
                    ->icon('heroicon-o-check-circle');
            } elseif ($orderReturn->status === 'rejected' || $orderReturn->status === 'cancelled') {
                $notification->danger()
                    ->icon('heroicon-o-x-circle');
            } elseif ($orderReturn->status === 'processing') {
                $notification->info()
                    ->icon('heroicon-o-arrow-path');
            } else {
                $notification->warning()
                    ->icon('heroicon-o-clock');
            }

            return $notification->getDatabaseMessage();
        }

        return Notification::make()
            ->title('Notification de retour')
            ->body('Une mise à jour concernant votre retour a été effectuée.')
            ->info()
            ->getDatabaseMessage();
    }
}
