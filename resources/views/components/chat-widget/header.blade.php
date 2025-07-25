{{-- File: resources/views/components/chat-widget/header.blade.php --}}
{{-- Chat Window Header Component --}}

<div class="bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center flex-shrink-0">
    <div class="flex items-center space-x-3">
        {{-- Support Avatar --}}
        <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"/>
            </svg>
        </div>
        
        {{-- Support Info --}}
        <div>
            <h3 class="font-semibold text-sm">Customer Support</h3>
            <p id="support-status" class="text-xs text-blue-100">Connecting...</p>
        </div>
    </div>
    
    {{-- Close Button --}}
    <button onclick="ChatWidget.close()" 
            class="text-blue-100 hover:text-white transition-colors p-1 rounded hover:bg-blue-400">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<style>
/* Header responsive */
@media (max-width: 480px) {
    .rounded-t-lg {
        border-radius: 0 !important;
    }
    
    /* Larger touch targets on mobile */
    .p-4 {
        padding: 16px 20px;
    }
    
    .w-8.h-8 {
        width: 32px;
        height: 32px;
    }
}

/* Status text animations */
#support-status {
    transition: color 0.3s ease;
}

#support-status.status-online {
    color: #bbf7d0; /* green-200 */
}

#support-status.status-offline {
    color: #fecaca; /* red-200 */
}

#support-status.status-typing {
    color: #bfdbfe; /* blue-200 */
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Close button enhanced */
button:hover svg {
    transform: scale(1.1);
}

button:active svg {
    transform: scale(0.95);
}
</style>