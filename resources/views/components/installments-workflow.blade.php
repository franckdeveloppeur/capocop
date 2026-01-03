@props(['installments', 'plan', 'isAdmin' => false, 'orderId' => null])

@php
    $sortedInstallments = $installments->sortBy('due_date');
    $paidCount = $installments->where('status', 'paid')->count();
@endphp

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
                                 data-is-next="{{ $isNext ? 'true' : 'false' }}"
                                 style="width: 5.5rem; height: 5.5rem; background: {{ $nodeBg }}; border: 4px solid {{ $nodeBorder }}; border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; position: relative; z-index: 10;">
                                @if($isNext)
                                    <div style="position: absolute; inset: -8px; border-radius: 1rem; border: 3px solid {{ $nodeBorder }}; opacity: 0.3; animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></div>
                                @endif
                                
                                <!-- Icon -->
                                <div style="color: white; margin-bottom: 0.25rem;">
                                    @if($isPaid)
                                        <svg style="width: 2.25rem; height: 2.25rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif($isOverdue)
                                        <svg style="width: 2.25rem; height: 2.25rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @else
                                        <svg class="workflow-clock-icon" style="width: 2.25rem; height: 2.25rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                
                                <!-- Number -->
                                <div style="color: white; font-weight: 800; font-size: 1.125rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ $index + 1 }}</div>
                            </div>
                        </div>

                        <!-- Node Info Card -->
                        <div class="workflow-card" 
                             data-is-next="{{ $isNext ? 'true' : 'false' }}"
                             data-status="{{ $installment->status }}"
                             style="width: 18rem; background: {{ $cardBg }}; border-radius: 0.875rem; padding: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border: 2px solid {{ $cardBorder }}; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                            <div class="workflow-card-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 2px solid {{ $cardBorder }};">
                                <span class="workflow-card-title" style="font-size: 0.875rem; font-weight: 700; color: {{ $textColor }}; letter-spacing: 0.025em;">Échéance {{ $index + 1 }}</span>
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
                            <div class="workflow-card-content" style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                                <div class="workflow-card-item" style="display: flex; align-items: center; gap: 0.625rem;">
                                    <svg class="workflow-icon" style="width: 1.125rem; height: 1.125rem; flex-shrink: 0; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="workflow-card-text" style="font-weight: 500; color: #374151;">{{ $installment->due_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="workflow-card-item" style="display: flex; align-items: center; gap: 0.625rem;">
                                    <svg class="workflow-icon" style="width: 1.125rem; height: 1.125rem; flex-shrink: 0; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="workflow-card-text-bold" style="font-weight: 700; font-size: 1.25rem; color: #111827;">{{ number_format($installment->amount, 0, ',', ' ') }} XAF</span>
                                </div>
                                @if($installment->paid_at)
                                    <div class="workflow-card-divider" style="padding-top: 0.75rem; margin-top: 0.5rem; border-top: 2px solid {{ $cardBorder }};">
                                        <div style="display: flex; align-items: center; gap: 0.625rem; color: #059669; font-size: 0.875rem; font-weight: 600;">
                                            <svg style="width: 1.125rem; height: 1.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Payé le {{ $installment->paid_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($isAdmin && $installment->status !== 'paid')
                                    <!-- Actions Admin -->
                                    <div class="workflow-card-actions" style="padding-top: 0.75rem; margin-top: 0.75rem; border-top: 2px solid {{ $cardBorder }}; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <button 
                                            type="button" 
                                            id="mark-paid-btn-{{ $installment->id }}"
                                            data-installment-id="{{ $installment->id }}"
                                            data-order-id="{{ $orderId }}"
                                            data-action="mark-paid"
                                            class="workflow-action-btn mark-paid-button" 
                                            style="flex: 1; min-width: 120px; padding: 0.5rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.375rem; position: relative;"
                                            onmouseover="if(!this.disabled) this.style.background='#059669'" 
                                            onmouseout="if(!this.disabled) this.style.background='#10b981'">
                                            <svg id="mark-paid-icon-{{ $installment->id }}" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span id="mark-paid-text-{{ $installment->id }}">Marquer payée</span>
                                            <svg id="mark-paid-spinner-{{ $installment->id }}" style="display: none; width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            type="button" 
                                            id="update-status-btn-{{ $installment->id }}"
                                            data-installment-id="{{ $installment->id }}"
                                            data-installment-status="{{ $installment->status }}"
                                            data-order-id="{{ $orderId }}"
                                            data-action="update-status"
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

<style>
    /* Light mode styles */
    .installments-workflow-wrapper {
        background: linear-gradient(to bottom right, #f9fafb, #f3f4f6);
        border: 2px solid #d1d5db;
    }

    .workflow-node:hover {
        transform: scale(1.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }

    .workflow-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .workflow-card[data-is-next="true"] {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .workflow-node[data-is-next="true"] {
        animation: pulse-node 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .workflow-clock-icon {
        animation: spin 2s linear infinite;
    }

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

    @keyframes pulse-node {
        0%, 100% {
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
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
    
    /* Spinner animation pour les boutons */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Dark mode styles */
    @media (prefers-color-scheme: dark) {
        .installments-workflow-wrapper {
            background: linear-gradient(to bottom right, #1f2937, #111827) !important;
            border-color: #374151 !important;
        }

        .workflow-card {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .workflow-card[data-is-next="true"] {
            background: #1e3a8a !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2), 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
        }

        .workflow-card-header {
            border-bottom-color: #374151 !important;
        }

        .workflow-card-title {
            color: #e5e7eb !important;
        }

        .workflow-card-text {
            color: #d1d5db !important;
        }

        .workflow-card-text-bold {
            color: #f9fafb !important;
        }

        .workflow-icon {
            color: #9ca3af !important;
        }

        .workflow-card-divider {
            border-top-color: #374151 !important;
        }
    }

    /* Filament dark mode support */
    .dark .installments-workflow-wrapper,
    [data-theme="dark"] .installments-workflow-wrapper {
        background: linear-gradient(to bottom right, #1f2937, #111827) !important;
        border-color: #374151 !important;
    }

    .dark .workflow-card,
    [data-theme="dark"] .workflow-card {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }

    .dark .workflow-card[data-is-next="true"],
    [data-theme="dark"] .workflow-card[data-is-next="true"] {
        background: #1e3a8a !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2), 0 10px 15px -3px rgba(0, 0, 0, 0.3) !important;
    }

    .dark .workflow-card-header,
    [data-theme="dark"] .workflow-card-header {
        border-bottom-color: #374151 !important;
    }

    .dark .workflow-card-title,
    [data-theme="dark"] .workflow-card-title {
        color: #e5e7eb !important;
    }

    .dark .workflow-card-text,
    [data-theme="dark"] .workflow-card-text {
        color: #d1d5db !important;
    }

    .dark .workflow-card-text-bold,
    [data-theme="dark"] .workflow-card-text-bold {
        color: #f9fafb !important;
    }

    .dark .workflow-icon,
    [data-theme="dark"] .workflow-icon {
        color: #9ca3af !important;
    }

    .dark .workflow-card-divider,
    [data-theme="dark"] .workflow-card-divider {
        border-top-color: #374151 !important;
    }

    /* Badge dark mode */
    .dark .workflow-badge[style*="background: #d1fae5"],
    [data-theme="dark"] .workflow-badge[style*="background: #d1fae5"] {
        background: #064e3b !important;
        color: #6ee7b7 !important;
    }

    .dark .workflow-badge[style*="background: #fee2e2"],
    [data-theme="dark"] .workflow-badge[style*="background: #fee2e2"] {
        background: #7f1d1d !important;
        color: #fca5a5 !important;
    }

    .dark .workflow-badge[style*="background: #dbeafe"],
    [data-theme="dark"] .workflow-badge[style*="background: #dbeafe"] {
        background: #1e3a8a !important;
        color: #93c5fd !important;
    }

    .dark .workflow-badge[style*="background: #fef3c7"],
    [data-theme="dark"] .workflow-badge[style*="background: #fef3c7"] {
        background: #78350f !important;
        color: #fde047 !important;
    }

    /* Empty state dark mode */
    .dark .workflow-empty-icon,
    [data-theme="dark"] .workflow-empty-icon {
        color: #6b7280 !important;
    }

    .dark .workflow-empty-text,
    [data-theme="dark"] .workflow-empty-text {
        color: #9ca3af !important;
    }

    /* Scrollbar styling */
    .installments-workflow-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .installments-workflow-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .installments-workflow-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }

    .installments-workflow-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    .dark .installments-workflow-wrapper::-webkit-scrollbar-thumb {
        background-color: #475569;
    }

    .dark .installments-workflow-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #64748b;
    }

    /* Admin Actions Styles */
    .workflow-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .workflow-action-btn:active {
        transform: translateY(0);
    }
</style>

@if($isAdmin)
<!-- Modal pour modifier le statut -->
<div id="statusModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; max-width: 28rem; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: #111827;">Modifier le Statut de l'Échéance</h3>
        <form id="statusForm" onsubmit="return updateStatus(event)">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Statut</label>
                <select name="status" id="statusSelect" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                    <option value="pending">En attente</option>
                    <option value="paid">Payée</option>
                    <option value="overdue">En retard</option>
                </select>
            </div>
            <div id="paidAtContainer" style="margin-bottom: 1rem; display: none;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Date de Paiement</label>
                <input type="datetime-local" name="paid_at" id="paidAtInput" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeStatusModal()" style="padding: 0.5rem 1rem; background: #e5e7eb; color: #374151; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Annuler</button>
                <button type="submit" id="statusFormSubmit" style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endif

{!! '<script>' !!}
(function() {
    'use strict';
    
    // Initialiser l'objet global
    window.installmentWorkflow = window.installmentWorkflow || { currentInstallmentId: null };
    
    // Fonction pour marquer comme payée
    window.handleMarkAsPaid = function(installmentId, orderId, buttonElement) {
        console.log('handleMarkAsPaid called', { installmentId, orderId, buttonElement });
        
        try {
            if (!installmentId || !orderId) {
                console.error('Missing data:', { installmentId, orderId });
                alert('Erreur: Données manquantes');
                return false;
            }
            
            console.log('Showing confirmation dialog...');
            const confirmed = confirm('Êtes-vous sûr de vouloir marquer cette échéance comme payée ?');
            console.log('Confirmation result:', confirmed);
            
            if (!confirmed) {
                console.log('User cancelled');
                return false;
            }
            
            // Afficher le spinner
            console.log('Updating UI...');
            if (buttonElement) {
                buttonElement.disabled = true;
                buttonElement.style.opacity = '0.6';
                const icon = document.getElementById('mark-paid-icon-' + installmentId);
                const text = document.getElementById('mark-paid-text-' + installmentId);
                const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                if (icon) icon.style.display = 'none';
                if (text) text.textContent = 'Traitement...';
                if (spinner) spinner.style.display = 'block';
            }
            
            const url = '/capocopadmin/orders/' + orderId + '/installments/' + installmentId + '/mark-paid';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            console.log('Making fetch request to:', url);
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => {
                console.log('Response received:', response.status, response.statusText);
                if (!response.ok) {
                    const errorText = response.statusText || 'Network response was not ok';
                    console.error('Response not OK:', response.status, errorText);
                    return response.text().then(text => {
                        console.error('Response body:', text);
                        throw new Error(errorText + ': ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Succès: ' + (data.message || 'Échéance marquée comme payée avec succès'));
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    console.error('Request failed:', data);
                    if (buttonElement) {
                        buttonElement.disabled = false;
                        buttonElement.style.opacity = '1';
                        const icon = document.getElementById('mark-paid-icon-' + installmentId);
                        const text = document.getElementById('mark-paid-text-' + installmentId);
                        const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                        if (icon) icon.style.display = 'block';
                        if (text) text.textContent = 'Marquer payée';
                        if (spinner) spinner.style.display = 'none';
                    }
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.style.opacity = '1';
                    const icon = document.getElementById('mark-paid-icon-' + installmentId);
                    const text = document.getElementById('mark-paid-text-' + installmentId);
                    const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                    if (icon) icon.style.display = 'block';
                    if (text) text.textContent = 'Marquer payée';
                    if (spinner) spinner.style.display = 'none';
                }
                alert('Erreur: Une erreur est survenue lors du traitement. Vérifiez la console pour plus de détails.');
            });
            
            return false;
        } catch (error) {
            console.error('handleMarkAsPaid error:', error);
            alert('Erreur: ' + error.message);
            return false;
        }
    };
    
    // Fonction pour ouvrir le modal
    window.handleUpdateStatus = function(installmentId, currentStatus, orderId) {
        console.log('handleUpdateStatus called', { installmentId, currentStatus, orderId });
        
        if (!installmentId || !orderId) {
            alert('Erreur: Données manquantes');
            return false;
        }
        
        window.installmentWorkflow.currentInstallmentId = installmentId;
        window.installmentWorkflow.orderId = orderId;
        
        const modal = document.getElementById('statusModal');
        const statusSelect = document.getElementById('statusSelect');
        const paidAtContainer = document.getElementById('paidAtContainer');
        const paidAtInput = document.getElementById('paidAtInput');
        
        if (!modal || !statusSelect) {
            alert('Erreur: Modal non trouvé');
            return false;
        }
        
        statusSelect.value = currentStatus || 'pending';
        
        if (currentStatus === 'paid') {
            paidAtContainer.style.display = 'block';
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            paidAtInput.value = now.toISOString().slice(0, 16);
        } else {
            paidAtContainer.style.display = 'none';
        }
        
        // Réinitialiser l'event listener du select
        const newStatusSelect = statusSelect.cloneNode(true);
        statusSelect.parentNode.replaceChild(newStatusSelect, statusSelect);
        const newSelect = document.getElementById('statusSelect');
        if (newSelect) {
            newSelect.addEventListener('change', function() {
                if (this.value === 'paid') {
                    paidAtContainer.style.display = 'block';
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    paidAtInput.value = now.toISOString().slice(0, 16);
                } else {
                    paidAtContainer.style.display = 'none';
                }
            });
        }
        
        modal.style.display = 'flex';
        return false;
    };
    
    // Fonction pour mettre à jour le statut
    window.updateStatus = function(event) {
        if (event) event.preventDefault();
        
        if (!window.installmentWorkflow || !window.installmentWorkflow.currentInstallmentId || !window.installmentWorkflow.orderId) {
            alert('Erreur: Données manquantes');
            return false;
        }
        
        const statusSelect = document.getElementById('statusSelect');
        const paidAtInput = document.getElementById('paidAtInput');
        const status = statusSelect ? statusSelect.value : null;
        const paidAt = status === 'paid' && paidAtInput ? paidAtInput.value : null;
        
        const submitButton = document.getElementById('statusFormSubmit');
        if (!submitButton) {
            alert('Erreur: Bouton de soumission non trouvé');
            return false;
        }
        
        const originalText = submitButton.textContent;
        
        // Afficher le spinner
        submitButton.disabled = true;
        submitButton.style.opacity = '0.6';
        submitButton.style.cursor = 'not-allowed';
        submitButton.innerHTML = '<svg style="display: inline-block; width: 1rem; height: 1rem; animation: spin 1s linear infinite; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Traitement...';
        
        const url = '/capocopadmin/orders/' + window.installmentWorkflow.orderId + '/installments/' + window.installmentWorkflow.currentInstallmentId + '/update-status';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: status,
                paid_at: paidAt
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Succès: ' + (data.message || 'Statut de l\'échéance mis à jour avec succès'));
                window.closeStatusModal();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalText;
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            submitButton.style.cursor = 'pointer';
            submitButton.textContent = originalText;
            alert('Erreur: Une erreur est survenue lors du traitement');
        });
        
        return false;
    };
    
    // Fonction pour fermer le modal
    window.closeStatusModal = function() {
        const modal = document.getElementById('statusModal');
        if (modal) {
            modal.style.display = 'none';
        }
        window.installmentWorkflow.currentInstallmentId = null;
    };
    
    // Fermer le modal en cliquant en dehors
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('statusModal');
        if (modal && e.target === modal) {
            window.closeStatusModal();
        }
    });
    
    // Utiliser la délégation d'événements pour gérer tous les clics sur les boutons
    // Cela fonctionne même si les boutons sont ajoutés dynamiquement
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.workflow-action-btn');
        if (!button) return;
        
        const action = button.getAttribute('data-action');
        const installmentId = button.getAttribute('data-installment-id');
        const orderId = button.getAttribute('data-order-id');
        const status = button.getAttribute('data-installment-status');
        
        console.log('Button clicked:', { action, installmentId, orderId, status, button });
        
        if (action === 'mark-paid' && installmentId && orderId) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mark as paid action triggered');
            if (window.handleMarkAsPaid) {
                window.handleMarkAsPaid(installmentId, orderId, button);
            } else {
                console.error('handleMarkAsPaid function not found!');
                alert('Erreur: Fonction non disponible. Veuillez recharger la page.');
            }
        } else if (action === 'update-status' && installmentId && orderId) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Update status action triggered');
            if (window.handleUpdateStatus) {
                window.handleUpdateStatus(installmentId, status, orderId);
            } else {
                console.error('handleUpdateStatus function not found!');
                alert('Erreur: Fonction non disponible. Veuillez recharger la page.');
            }
        }
    });
    
    // Initialiser et vérifier que tout est prêt
    function initWorkflow() {
        console.log('Initializing installment workflow...');
        
        const markPaidButtons = document.querySelectorAll('.mark-paid-button');
        const updateStatusButtons = document.querySelectorAll('.update-status-button');
        
        console.log('Found buttons:', {
            markPaid: markPaidButtons.length,
            updateStatus: updateStatusButtons.length
        });
        
        console.log('Installment workflow functions loaded', {
            handleMarkAsPaid: typeof window.handleMarkAsPaid,
            handleUpdateStatus: typeof window.handleUpdateStatus,
            updateStatus: typeof window.updateStatus,
            closeStatusModal: typeof window.closeStatusModal
        });
        
        // Vérifier que les fonctions sont disponibles
        if (typeof window.handleMarkAsPaid === 'undefined') {
            console.error('handleMarkAsPaid is not defined!');
        }
        if (typeof window.handleUpdateStatus === 'undefined') {
            console.error('handleUpdateStatus is not defined!');
        }
    }
    
    // Attacher les listeners après le chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWorkflow);
    } else {
        initWorkflow();
        setTimeout(initWorkflow, 100);
        setTimeout(initWorkflow, 500);
    }
})();
{!! '</script>' !!}
