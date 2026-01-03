<div>
@php
    $sortedInstallments = $installments ? $installments->sortBy('due_date') : collect();
    $paidCount = $installments ? $installments->where('status', 'paid')->count() : 0;
    // Utiliser le nombre prévu d'échéances au lieu du nombre créé
    $totalCount = $plan ? ($plan->number_of_installments ?? $sortedInstallments->count()) : $sortedInstallments->count();
@endphp

@if(!$order || !$plan || $sortedInstallments->isEmpty())
    <div class="installments-workflow-wrapper" style="padding: 2.5rem 2rem; border-radius: 0.75rem; overflow-x: auto; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
        <div class="workflow-empty-state" style="text-align: center; padding: 3rem 0;">
            <svg class="workflow-empty-icon" style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="workflow-empty-text" style="color: #6b7280; font-weight: 500;">Aucune échéance définie</p>
        </div>
    </div>
@else
<div class="installments-workflow-wrapper" style="padding: 2.5rem 2rem; border-radius: 0.75rem; overflow-x: auto; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
    @if($sortedInstallments->isEmpty())
        <div class="workflow-empty-state" style="text-align: center; padding: 3rem 0;">
            <svg class="workflow-empty-icon" style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="workflow-empty-text" style="color: #6b7280; font-weight: 500;">Aucune échéance définie</p>
        </div>
    @else
        <div style="display: flex; align-items: flex-start; gap: 2rem; min-width: max-content; padding: 1rem 0;">
            @foreach($sortedInstallments as $index => $installment)
                @php
                    $isPaid = $installment->status === 'paid';
                    $isOverdue = $installment->status === 'overdue';
                    $isPending = $installment->status === 'pending';
                    $isNext = $isPending && $paidCount === $index;
                    $isLast = $index === $sortedInstallments->count() - 1;
                    // Numéro d'échéance basé sur l'index (commence à 1)
                    $installmentNumber = $index + 1;
                    
                    // Couleurs pour light mode
                    $nodeBg = $isPaid ? '#10b981' : ($isOverdue ? '#ef4444' : ($isNext ? '#3b82f6' : '#eab308'));
                    $nodeBorder = $isPaid ? '#059669' : ($isOverdue ? '#dc2626' : ($isNext ? '#2563eb' : '#ca8a04'));
                    $lineColor = $isPaid ? '#34d399' : ($isOverdue ? '#f87171' : ($isNext ? '#60a5fa' : '#9ca3af'));
                    $cardBg = $isNext ? '#eff6ff' : '#ffffff';
                    $cardBorder = $isNext ? '#93c5fd' : '#e5e7eb';
                    $textColor = $isPaid ? '#059669' : ($isOverdue ? '#dc2626' : ($isNext ? '#2563eb' : '#ca8a04'));
                    $badgeBg = $isPaid ? '#d1fae5' : ($isOverdue ? '#fee2e2' : ($isNext ? '#dbeafe' : '#fef3c7'));
                    $badgeText = $isPaid ? '#065f46' : ($isOverdue ? '#991b1b' : ($isNext ? '#1e40af' : '#92400e'));
                @endphp

                <div style="display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0;">
                    <!-- Connector Line (before node) -->
                    @if($index > 0)
                        @php
                            $prevInstallment = $sortedInstallments->values()[$index - 1];
                            $prevIsPaid = $prevInstallment->status === 'paid';
                            $prevIsOverdue = $prevInstallment->status === 'overdue';
                            $prevLineColor = $prevIsPaid ? '#34d399' : ($prevIsOverdue ? '#f87171' : '#9ca3af');
                        @endphp
                        <div class="workflow-connector" style="height: 5px; width: 3rem; background: {{ $prevLineColor }}; border-radius: 9999px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); flex-shrink: 0;"></div>
                    @endif

                    <!-- Workflow Node Container -->
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; position: relative;">
                        <!-- Workflow Node -->
                        <div class="workflow-node-wrapper" style="position: relative;">
                            <div class="workflow-node"
                                 data-status="{{ $installment->status }}"
                                 style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: {{ $nodeBg }}; border: 3px solid {{ $nodeBorder }}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; flex-shrink: 0; position: relative; z-index: 1; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                                @if($isPaid)
                                    <svg class="workflow-icon" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($isOverdue)
                                    <svg class="workflow-icon" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                @elseif($isNext)
                                    <svg class="workflow-icon workflow-clock-icon" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="workflow-icon" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @endif
                            </div>
                            @if($isNext)
                                <span class="workflow-node-ring" style="position: absolute; inset: 0; border-radius: 9999px; border: 3px solid {{ $nodeBorder }}; animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                            @endif
                        </div>

                        <!-- Node Info Card -->
                        <div class="workflow-card"
                             data-is-next="{{ $isNext ? 'true' : 'false' }}"
                             data-status="{{ $installment->status }}"
                             style="width: 18rem; background: {{ $cardBg }}; border-radius: 0.875rem; padding: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 2px solid {{ $cardBorder }}; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                            <div class="workflow-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 2px solid {{ $cardBorder }};">
                                <span class="workflow-card-title" style="font-size: 0.875rem; font-weight: 700; color: {{ $textColor }}; letter-spacing: 0.025em;">Échéance {{ $installmentNumber }}/{{ $totalCount }}</span>
                                <span class="workflow-badge" style="padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeText }}; text-transform: uppercase; letter-spacing: 0.05em;">
                                    @if($isPaid)
                                        ✓ Payée
                                    @elseif($isOverdue)
                                        ⚠ En retard
                                    @else
                                        {{ $isNext ? '→ Prochaine' : '⏳ En attente' }}
                                    @endif
                                </span>
                            </div>
                            <div class="workflow-card-content" style="font-size: 0.875rem; color: #4b5563;">
                                <p class="workflow-card-text" style="margin-bottom: 0.5rem;">
                                    <span class="workflow-card-text-bold" style="font-weight: 600; color: #1f2937;">Date d'échéance:</span> {{ $installment->due_date->format('d/m/Y') }}
                                </p>
                                <p class="workflow-card-text" style="margin-bottom: 0.5rem;">
                                    <span class="workflow-card-text-bold" style="font-weight: 600; color: #1f2937;">Montant:</span> {{ number_format($installment->amount, 0, ',', ' ') }} XAF
                                </p>
                                @if($installment->paid_at)
                                    <p class="workflow-card-text" style="margin-bottom: 0.5rem;">
                                        <span class="workflow-card-text-bold" style="font-weight: 600; color: #1f2937;">Payé le:</span> {{ $installment->paid_at->format('d/m/Y à H:i') }}
                                    </p>
                                @endif
                                @if($installment->payment && $installment->payment->transaction_ref)
                                    <p class="workflow-card-text">
                                        <span class="workflow-card-text-bold" style="font-weight: 600; color: #1f2937;">Réf. transaction:</span> {{ $installment->payment->transaction_ref }}
                                    </p>
                                @endif
                            </div>

                            @if($isAdmin && !$isPaid)
                                <div class="workflow-card-divider" style="border-top: 1px solid #e5e7eb; margin-top: 1rem; padding-top: 1rem;"></div>
                                <div class="workflow-actions" style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                                    <button
                                        type="button"
                                        wire:click="markAsPaid('{{ $installment->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="markAsPaid('{{ $installment->id }}')"
                                        class="workflow-action-btn mark-paid-button"
                                        style="flex: 1; min-width: 120px; padding: 0.5rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.375rem; position: relative;"
                                        onmouseover="if(!this.disabled) this.style.background='#059669'"
                                        onmouseout="if(!this.disabled) this.style.background='#10b981'">
                                        <svg wire:loading.remove wire:target="markAsPaid('{{ $installment->id }}')" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <svg wire:loading wire:target="markAsPaid('{{ $installment->id }}')" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="markAsPaid('{{ $installment->id }}')">Marquer payée</span>
                                        <span wire:loading wire:target="markAsPaid('{{ $installment->id }}')">Traitement...</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="openStatusModal('{{ $installment->id }}')"
                                        class="workflow-action-btn update-status-button"
                                        style="flex: 1; min-width: 120px; padding: 0.5rem 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.375rem;"
                                        onmouseover="if(!this.disabled) this.style.background='#2563eb'"
                                        onmouseout="if(!this.disabled) this.style.background='#3b82f6'">
                                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Modifier statut
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Connector Line (after node) -->
                    @if(!$isLast)
                        <div class="workflow-connector" style="height: 5px; width: 3rem; background: {{ $lineColor }}; border-radius: 9999px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); flex-shrink: 0;"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif

@if($isAdmin)
<!-- Modal pour modifier le statut -->
@if($showStatusModal)
    <div wire:click="closeStatusModal" style="position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center;">
        <div wire:click.stop style="background: white; border-radius: 0.75rem; padding: 1.5rem; max-width: 28rem; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">Modifier le Statut de l'Échéance</h3>
            <form wire:submit="updateStatus">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Statut</label>
                    <select wire:model="status" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                        <option value="pending">En attente</option>
                        <option value="paid">Payée</option>
                        <option value="overdue">En retard</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem; display: {{ $status === 'paid' ? 'block' : 'none' }};">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Date de Paiement</label>
                    <input type="datetime-local" wire:model="paidAt" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" wire:click="closeStatusModal" style="padding: 0.5rem 1rem; background: #e5e7eb; color: #374151; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Annuler</button>
                    <button type="submit" wire:loading.attr="disabled" style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                        <span wire:loading.remove>Enregistrer</span>
                        <span wire:loading>Traitement...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endif

<style>
    @keyframes pulse-ring {
        0%, 100% {
            opacity: 0.3;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.05);
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
</style>
</div>
