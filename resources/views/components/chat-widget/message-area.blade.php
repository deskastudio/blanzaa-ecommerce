{{-- File: resources/views/components/chat-widget/messages-area.blade.php --}}
{{-- Messages Display Area Component --}}

<div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
    {{-- Welcome Message --}}
    <div id="welcome-message" class="text-center text-gray-500 text-sm">
        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
            <div class="mb-2">
                <span class="text-2xl">👋</span>
            </div>
            <p class="font-medium text-gray-700 mb-1">Welcome to our support chat!</p>
            <p class="text-xs text-gray-500">How can we help you today?</p>
        </div>
    </div>
    
    {{-- Messages List --}}
    <div id="messages-list" class="space-y-3">
        {{-- Messages will be dynamically added here by JavaScript --}}
    </div>
    
    {{-- Typing Indicator --}}
    <div id="typing-indicator" style="display: none;" class="items-start space-x-2">
        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm shadow-sm">
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>
    
    {{-- Scroll to bottom button --}}
    <div id="scroll-to-bottom" style="display: none;" class="fixed bottom-24 right-8 z-10">
        <button onclick="ChatWidget.scrollToBottom()" 
                class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 shadow-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </button>
    </div>
</div>

<style>
/* Messages area styling */
#messages-container {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
#messages-container::-webkit-scrollbar {
    width: 4px;
}

#messages-container::-webkit-scrollbar-track {
    background: transparent;
}

#messages-container::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 2px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Welcome message animation */
#welcome-message {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Typing indicator display utilities */
#typing-indicator.typing-show {
    display: flex !important;
}

#typing-indicator.typing-hidden {
    display: none !important;
}

/* Typing indicator animation */
#typing-indicator .animate-bounce:nth-child(1) { animation-delay: 0s; }
#typing-indicator .animate-bounce:nth-child(2) { animation-delay: 0.1s; }
#typing-indicator .animate-bounce:nth-child(3) { animation-delay: 0.2s; }

/* Scroll button utilities */
#scroll-to-bottom.scroll-show {
    display: block !important;
}

#scroll-to-bottom.scroll-hidden {
    display: none !important;
}

/* Message wrapper styling that works with message-item component */
.message-wrapper {
    margin-bottom: 12px;
    transition: all 0.3s ease-out;
}

.message-wrapper.sending {
    opacity: 0.7;
}

.user-message {
    display: flex;
    justify-content: flex-end;
}

.admin-message {
    display: flex;
    justify-content: flex-start;
}

.system-message {
    display: flex;
    justify-content: center;
    margin: 8px 0;
}

/* Message bubble hover effects */
.user-message .bg-blue-500:hover {
    background-color: #2563eb;
}

.admin-message .bg-white:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Mobile responsive */
@media (max-width: 480px) {
    .max-w-xs {
        max-width: 85%;
    }
    
    .message-text {
        font-size: 14px;
    }
    
    .message-time {
        font-size: 10px;
    }
    
    #scroll-to-bottom {
        bottom: 100px;
        right: 20px;
    }
}
</style>