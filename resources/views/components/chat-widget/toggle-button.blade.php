{{-- File: resources/views/components/chat-widget/toggle-button.blade.php --}}
{{-- Chat Toggle Button Component --}}

<button id="chat-toggle" onclick="ChatWidget.toggle()" 
        class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 shadow-lg transition-all duration-300 relative group">
    {{-- Chat Icon --}}
    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.289L3 21l2.281-5.094A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
    </svg>
    
    {{-- Unread Badge --}}
    <span id="unread-badge" style="display: none;" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 items-center justify-center font-bold animate-pulse">
        0
    </span>
    
    {{-- Pulse Ring Animation --}}
    <div class="absolute inset-0 rounded-full bg-blue-400 opacity-75 animate-ping"></div>
</button>

<style>
/* Toggle button animations */
#chat-toggle {
    position: relative;
    z-index: 10;
}

#chat-toggle:hover {
    transform: scale(1.05);
}

#chat-toggle:active {
    transform: scale(0.95);
}

/* Badge display utilities */
#unread-badge.badge-show {
    display: flex !important;
}

#unread-badge.badge-hidden {
    display: none !important;
}

/* Disable ping animation when chat is open */
.chat-open #chat-toggle .animate-ping {
    animation: none;
}

/* Unread badge animation */
#unread-badge {
    animation: badgeBounce 0.5s ease-out;
}

@keyframes badgeBounce {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Mobile touch adjustments */
@media (max-width: 480px) {
    #chat-toggle {
        padding: 14px;
        bottom: 20px;
        right: 20px;
    }
    
    #chat-toggle svg {
        width: 20px;
        height: 20px;
    }
}
</style>