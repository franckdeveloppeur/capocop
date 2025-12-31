@php
    // Protection contre la duplication du composant HTML
    if (isset($GLOBALS['toast_notification_rendered'])) {
        return;
    }
    $GLOBALS['toast_notification_rendered'] = true;
@endphp

<script>
(function() {
    'use strict';
    
    // Protection contre la duplication
    if (window.toastNotificationLoaded) {
        return;
    }
    window.toastNotificationLoaded = true;

    // Helper function to show toast
    window.showToast = function(type, title, message, duration = 4000) {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type, title, message, duration }
        }));
    };

    // Attendre que le DOM soit chargé
    function initToastNotification() {
        // Enregistrer le composant Alpine
        if (typeof Alpine !== 'undefined') {
            Alpine.data('toastNotification', () => {
                return {
                    toasts: [],
                    
                    init() {
                        // Écouter les événements toast
                        window.addEventListener('toast', (e) => {
                            if (e.detail) {
                                this.show(e.detail.type, e.detail.title, e.detail.message, e.detail.duration);
                            }
                        });
                        
                        // Écouter les événements Livewire
                        if (typeof Livewire !== 'undefined' && typeof Livewire.on === 'function') {
                            Livewire.on('toast', (...args) => {
                                const data = args[0] || {};
                                if (data && typeof data === 'object') {
                                    this.show(
                                        data.type || 'success',
                                        data.title || '',
                                        data.message || ''
                                    );
                                }
                            });
                        }
                    },
                    
                    show(type = 'success', title = '', message = '', duration = 4000) {
                        const now = Date.now();
                        const duplicate = this.toasts.find(t => 
                            t.type === type && 
                            t.title === title && 
                            t.message === message &&
                            (now - t.createdAt) < 500
                        );
                        
                        if (duplicate) {
                            return;
                        }
                        
                        const id = now + Math.random();
                        const toast = {
                            id,
                            type,
                            title,
                            message,
                            show: true,
                            progress: 100,
                            duration,
                            createdAt: now
                        };
                        
                        this.toasts.push(toast);
                        
                        // Animation de la barre de progression
                        const progressInterval = 50;
                        const totalSteps = duration / progressInterval;
                        let currentStep = 0;
                        
                        const interval = setInterval(() => {
                            currentStep++;
                            toast.progress = Math.max(0, 100 - (currentStep / totalSteps) * 100);
                            
                            if (currentStep >= totalSteps) {
                                clearInterval(interval);
                                this.remove(id);
                            }
                        }, progressInterval);
                        
                        setTimeout(() => {
                            clearInterval(interval);
                            this.remove(id);
                        }, duration);
                    },
                    
                    remove(id) {
                        const index = this.toasts.findIndex(t => t.id === id);
                        if (index > -1) {
                            this.toasts[index].show = false;
                            setTimeout(() => {
                                this.toasts.splice(index, 1);
                            }, 200);
                        }
                    }
                };
            });
        }
    }

    // Initialiser quand Alpine est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Alpine !== 'undefined') {
                initToastNotification();
            } else {
                document.addEventListener('alpine:init', initToastNotification);
            }
        });
    } else {
        if (typeof Alpine !== 'undefined') {
            initToastNotification();
        } else {
            document.addEventListener('alpine:init', initToastNotification);
        }
    }
})();
</script>

<div 
    x-data="toastNotification"
    x-init="init()"
    id="toast-notification-container"
    class="fixed top-4 right-4 z-[9999] space-y-3"
    style="pointer-events: none;"
>
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-x-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-w-sm w-full bg-white shadow-2xl rounded-xl pointer-events-auto ring-1 ring-gray-200 overflow-hidden backdrop-blur-sm"
            style="pointer-events: auto;"
        >
            <div class="p-5">
                <div class="flex items-start gap-4">
                    <!-- Icône selon le type -->
                    <div class="flex-shrink-0">
                        <div 
                            class="flex items-center justify-center h-12 w-12 rounded-full shadow-lg"
                            :class="{
                                'bg-green-500': toast.type === 'success',
                                'bg-red-500': toast.type === 'error',
                                'bg-blue-500': toast.type === 'info'
                            }"
                        >
                            <!-- Success Icon -->
                            <svg 
                                x-show="toast.type === 'success'"
                                class="h-6 w-6 text-white" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor" 
                                stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <!-- Error Icon -->
                            <svg 
                                x-show="toast.type === 'error'"
                                class="h-6 w-6 text-white" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor" 
                                stroke-width="3"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <!-- Info Icon -->
                            <svg 
                                x-show="toast.type === 'info'"
                                class="h-6 w-6 text-white" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor" 
                                stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Contenu du toast -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 leading-tight" x-text="toast.title"></p>
                        <p class="mt-1.5 text-sm text-gray-600 leading-relaxed" x-text="toast.message"></p>
                    </div>

                    <!-- Bouton de fermeture -->
                    <button
                        @click="remove(toast.id)"
                        type="button"
                        class="flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 rounded-lg p-1.5 transition-all duration-150 hover:bg-gray-100"
                    >
                        <span class="sr-only">Fermer</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Barre de progression -->
            <div class="h-1.5 bg-gray-100">
                <div 
                    class="h-full transition-all duration-100 ease-linear rounded-b-xl"
                    :class="{
                        'bg-green-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                        'bg-blue-500': toast.type === 'info'
                    }"
                    :style="`width: ${toast.progress}%`"
                ></div>
            </div>
        </div>
    </template>
</div>
