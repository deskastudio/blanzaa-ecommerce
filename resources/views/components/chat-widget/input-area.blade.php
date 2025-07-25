{{-- File: resources/views/components/chat-widget/input-area.blade.php --}}
{{-- Message Input Area --}}

<div class="p-4 border-t border-gray-200 bg-white">
    {{-- Input Form --}}
    <form id="chat-form" onsubmit="ChatWidget.sendMessage(event)" class="space-y-3">
        {{-- Main Input Row --}}
        <div class="flex space-x-2">
            <input type="text" 
                   id="message-input" 
                   placeholder="Type your message..." 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   maxlength="500" 
                   required 
                   autocomplete="off">
            
            <button type="submit" 
                    id="send-btn" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center min-w-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                {{-- Send Icon --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
        
        {{-- Input Info Row --}}
        <div class="flex justify-between items-center text-xs text-gray-500">
            <span>Press Enter to send</span>
            <span id="char-count" class="hidden">0/500</span>
        </div>
    </form>
    
    {{-- Connection Status --}}
    <div id="connection-status" class="hidden mt-2 text-center">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Reconnecting...
        </span>
    </div>
</div>

<script>
// Input Area JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    const charCount = document.getElementById('char-count');
    const sendBtn = document.getElementById('send-btn');
    
    // Character counter
    if (messageInput && charCount) {
        messageInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/500`;
            
            if (length > 0) {
                charCount.classList.remove('hidden');
            } else {
                charCount.classList.add('hidden');
            }
            
            // Color coding for character limit
            if (length > 450) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-500');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-gray-500');
            }
        });
    }
    
    // Auto-focus when chat opens
    messageInput?.addEventListener('focus', function() {
        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    
    // Enter key handling
    messageInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chat-form')?.dispatchEvent(new Event('submit'));
        }
    });
});
</script>

<style>
    /* Input focus effects */
    #message-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Send button loading state */
    #send-btn:disabled {
        background-color: #9ca3af !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 480px) {
        #chat-form .flex {
            gap: 8px;
        }
        
        #message-input {
            font-size: 16px; /* Prevent zoom on iOS */
        }
        
        #send-btn {
            min-width: 40px;
            padding: 8px;
        }
    }
</style>    