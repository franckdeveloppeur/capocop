@props(['installments', 'plan'])

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
                                    <span class="workflow-card-text-bold" style="font-weight: 700; font-size: 1.25rem; color: #111827;">{{ number_format($installment->amount, 0, ',', ' ') }} XOF</span>
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
</style>
