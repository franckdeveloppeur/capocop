<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Installment;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class InstallmentsWorkflow extends Component
{
    public ?Order $order = null;
    public $installments;
    public $plan;
    public $isAdmin = false;
    
    public $showStatusModal = false;
    public $currentInstallmentId = null;
    public $status = 'pending';
    public $paidAt = null;

    public function mount(?Model $record = null)
    {
        if (!$record instanceof Order) {
            throw new \Exception('Record must be an Order instance');
        }
        
        $this->order = $record;
        $this->plan = $this->order->installmentPlan;
        $this->installments = $this->plan ? $this->plan->installments->sortBy('due_date') : collect();
        $this->isAdmin = Auth::user()?->role === 'admin';
    }

    public function markAsPaid($installmentId)
    {
        if (!$this->isAdmin) {
            Notification::make()
                ->title('Accès refusé')
                ->danger()
                ->send();
            return;
        }

        $installment = Installment::findOrFail($installmentId);
        
        // Vérifier que l'échéance appartient à cette commande
        if ($installment->plan->order_id !== $this->order->id) {
            Notification::make()
                ->title('Échéance invalide')
                ->danger()
                ->send();
            return;
        }

        $installment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Rafraîchir les données
        $this->order->refresh();
        $this->plan = $this->order->installmentPlan;
        $this->plan->refresh();
        $this->plan->load('installments');
        $this->installments = $this->plan->installments->sortBy('due_date');

        Notification::make()
            ->title('Succès')
            ->body('Échéance marquée comme payée avec succès')
            ->success()
            ->send();
    }

    public function openStatusModal($installmentId)
    {
        if (!$this->isAdmin) {
            return;
        }

        $installment = Installment::findOrFail($installmentId);
        
        // Vérifier que l'échéance appartient à cette commande
        if ($installment->plan->order_id !== $this->order->id) {
            Notification::make()
                ->title('Échéance invalide')
                ->danger()
                ->send();
            return;
        }

        $this->currentInstallmentId = $installmentId;
        $this->status = $installment->status;
        $this->paidAt = $installment->paid_at ? $installment->paid_at->format('Y-m-d\TH:i') : null;
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        if (!$this->isAdmin || !$this->currentInstallmentId) {
            return;
        }

        $installment = Installment::findOrFail($this->currentInstallmentId);
        
        // Vérifier que l'échéance appartient à cette commande
        if ($installment->plan->order_id !== $this->order->id) {
            Notification::make()
                ->title('Échéance invalide')
                ->danger()
                ->send();
            return;
        }

        $updateData = ['status' => $this->status];
        
        if ($this->status === 'paid' && $this->paidAt) {
            $updateData['paid_at'] = $this->paidAt;
        } elseif ($this->status !== 'paid') {
            $updateData['paid_at'] = null;
        }

        $installment->update($updateData);

        // Rafraîchir les données
        $this->order->refresh();
        $this->plan = $this->order->installmentPlan;
        $this->plan->refresh();
        $this->plan->load('installments');
        $this->installments = $this->plan->installments->sortBy('due_date');

        $this->showStatusModal = false;
        $this->currentInstallmentId = null;
        $this->status = 'pending';
        $this->paidAt = null;

        Notification::make()
            ->title('Succès')
            ->body('Statut de l\'échéance mis à jour avec succès')
            ->success()
            ->send();
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->currentInstallmentId = null;
        $this->status = 'pending';
        $this->paidAt = null;
    }

    public function render()
    {
        return view('livewire.installments-workflow');
    }
}
