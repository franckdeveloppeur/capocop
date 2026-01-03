<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Installment;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            \Filament\Actions\Action::make('activities')
                ->label(__('Activités'))
                ->icon('heroicon-o-clock')
                ->color('info')
                ->url(fn () => OrderResource::getUrl('activities', ['record' => $this->record])),
        ];
    }

    public static function markInstallmentAsPaid($order, $installment)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $installmentModel = Installment::findOrFail($installment);
        $orderModel = \App\Models\Order::findOrFail($order);
        
        // Vérifier que l'échéance appartient à cette commande
        if ($installmentModel->plan->order_id !== $orderModel->id) {
            return response()->json(['success' => false, 'message' => 'Échéance invalide'], 400);
        }

        $installmentModel->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Échéance marquée comme payée']);
    }

    public static function updateInstallmentStatus($order, $installment)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $installmentModel = Installment::findOrFail($installment);
        $orderModel = \App\Models\Order::findOrFail($order);
        
        // Vérifier que l'échéance appartient à cette commande
        if ($installmentModel->plan->order_id !== $orderModel->id) {
            return response()->json(['success' => false, 'message' => 'Échéance invalide'], 400);
        }

        $status = request()->input('status');
        $paidAt = request()->input('paid_at');

        $updateData = ['status' => $status];
        
        if ($status === 'paid' && $paidAt) {
            $updateData['paid_at'] = $paidAt;
        } elseif ($status !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $installmentModel->update($updateData);

        return response()->json(['success' => true, 'message' => 'Statut de l\'échéance mis à jour']);
    }
}
