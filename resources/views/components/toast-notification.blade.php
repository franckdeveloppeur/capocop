<div 
    x-data="toastNotification"
    x-init="init()"
    class="fixed top-4 right-4 z-50 space-y-3"
    style="pointer-events: none;"
>
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
            style="pointer-events: auto;"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0" x-show="toast.type === 'success'">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-shrink-0" x-show="toast.type === 'error'">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-shrink-0" x-show="toast.type === 'info'">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900" x-text="toast.title"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="toast.message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="remove(toast.id)"
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                class="h-full transition-all duration-300 ease-linear"
                                :class="{
                                    'bg-green-500': toast.type === 'success',
                                    'bg-red-500': toast.type === 'error',
                                    'bg-blue-500': toast.type === 'info'
                                }"
                                :style="`width: ${toast.progress}%`"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toastNotification', () => ({
        toasts: [],
        
        init() {
            // Listen for toast events
            window.addEventListener('toast', (e) => {
                this.show(e.detail.type, e.detail.title, e.detail.message, e.detail.duration);
            });
        },
        
        show(type = 'success', title = '', message = '', duration = 3000) {
            const id = Date.now() + Math.random();
            const toast = {
                id,
                type,
                title,
                message,
                show: true,
                progress: 100,
                duration
            };
            
            this.toasts.push(toast);
            
            // Auto remove after duration
            const interval = setInterval(() => {
                if (toast.progress > 0) {
                    toast.progress -= (100 / (duration / 50));
                } else {
                    clearInterval(interval);
                    this.remove(id);
                }
            }, 50);
            
            // Remove after duration
            setTimeout(() => {
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
    }));
});

// Helper function to show toast
window.showToast = function(type, title, message, duration = 3000) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { type, title, message, duration }
    }));
};
</script>

