{{-- File: resources/views/components/chat-widget.blade.php --}}
{{-- Final Alpine.js Chat Widget - Complete & Fixed --}}

<div id="chat-widget" 
     x-data="chatWidget()" 
     x-init="init()"
     class="fixed bottom-4 right-4 z-50 font-sans"
     x-show="isAuthenticated">
    
    {{-- Toggle Button --}}
    <button @click="toggle()" 
            class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-4 shadow-lg transition-all duration-300 relative group">
        {{-- Chat Icon --}}
        <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.289L3 21l2.281-5.094A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
        </svg>
        
        {{-- Unread Badge --}}
        <span x-show="unreadCount > 0 && !isOpen" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold animate-pulse">
        </span>
        
        {{-- Pulse Ring Animation --}}
        <div class="absolute inset-0 rounded-full bg-blue-400 opacity-75 animate-ping" 
             x-show="!isOpen"></div>
    </button>
    
    {{-- Chat Window --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute bottom-16 right-0 w-96 h-[500px] bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden flex flex-col">
        
        {{-- Header --}}
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
                    <p x-text="connectionStatus" class="text-xs text-blue-100"></p>
                </div>
            </div>
            
            {{-- Close Button --}}
            <button @click="close()" 
                    class="text-blue-100 hover:text-white transition-colors p-1 rounded hover:bg-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Messages Container --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" 
             x-ref="messagesContainer">
            
            {{-- Welcome Message --}}
            <div x-show="messages.length === 0" class="text-center text-gray-500 text-sm">
                <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                    <div class="mb-2">
                        <span class="text-2xl">👋</span>
                    </div>
                    <p class="font-medium text-gray-700 mb-1">Welcome to our support chat!</p>
                    <p class="text-xs text-gray-500">How can we help you today?</p>
                </div>
            </div>
            
            {{-- Messages List --}}
            <template x-for="message in messages" :key="message.id">
                <div class="message-wrapper mb-3" 
                     :class="message.is_from_admin ? 'admin-message' : 'user-message'">
                    
                    {{-- Admin Message --}}
                    <div x-show="message.is_from_admin" class="flex justify-start">
                        <div class="max-w-xs">
                            <div class="flex items-start space-x-2">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm shadow-sm">
                                        <div x-text="message.sender_name" class="text-xs text-gray-500 mb-1"></div>
                                        <p x-text="message.message" class="text-sm text-gray-800"></p>
                                        <div x-text="message.formatted_time" class="text-xs text-gray-500 mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- User Message --}}
                    <div x-show="!message.is_from_admin" class="flex justify-end">
                        <div class="max-w-xs">
                            <div class="bg-blue-500 text-white px-4 py-2 rounded-lg rounded-br-sm shadow-sm">
                                <p x-text="message.message" class="text-sm"></p>
                                <div class="flex justify-between items-center mt-1 text-xs text-blue-100">
                                    <span x-text="message.formatted_time"></span>
                                    <span class="message-status">✓</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="flex items-start space-x-2">
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
        </div>
        
        {{-- Input Area --}}
        <div class="p-4 border-t border-gray-200 bg-white">
            <form @submit.prevent="sendChatMessage()" class="space-y-3">
                <div class="flex space-x-2">
                    <input type="text" 
                           x-model="messageText"
                           x-ref="messageInput"
                           @keydown.enter="sendChatMessage()"
                           placeholder="Type your message..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           maxlength="500" 
                           :disabled="isSending"
                           autocomplete="off">
                    
                    <button type="submit" 
                            :disabled="!messageText.trim() || isSending"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center justify-center min-w-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>Press Enter to send</span>
                    <span x-show="messageText.length > 0" 
                          x-text="`${messageText.length}/500`"
                          :class="messageText.length > 450 ? 'text-red-500' : 'text-gray-500'"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function chatWidget() {
    return {
        // State
        isOpen: false,
        isAuthenticated: false,
        conversationId: null,
        messages: [],
        messageText: '',
        unreadCount: 0,
        isSending: false,
        isLoading: false,
        isTyping: false,
        connectionStatus: 'Connecting...',
        lastMessageId: 0,
        polling: null,
        
        // Initialize
        async init() {
            console.log('🚀 Alpine Chat Widget initializing...');
            
            // Check authentication
            const authMeta = document.querySelector('meta[name="user-authenticated"]');
            this.isAuthenticated = authMeta && authMeta.content === 'true';
            
            if (!this.isAuthenticated) {
                console.log('❌ User not authenticated');
                return;
            }
            
            // Load conversation
            await this.loadConversation();
            
            // Start polling
            this.startPolling();
            
            console.log('✅ Alpine Chat Widget ready!');
        },
        
        // Load conversation and messages
        async loadConversation() {
            if (this.isLoading) return;
            this.isLoading = true;
            this.connectionStatus = 'Connecting...';
            
            try {
                console.log('🔄 Loading conversation...');
                
                const response = await fetch('/chat/conversation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    this.conversationId = data.conversation_id;
                    this.messages = data.messages || [];
                    this.unreadCount = data.unread_count || 0;
                    this.connectionStatus = 'Online';
                    
                    if (this.messages.length > 0) {
                        this.lastMessageId = Math.max(...this.messages.map(m => m.id));
                    }
                    
                    console.log('✅ Conversation loaded:', this.conversationId);
                    
                    // Auto scroll
                    this.$nextTick(() => this.scrollToBottom());
                } else {
                    throw new Error('Failed to load conversation');
                }
            } catch (error) {
                console.error('❌ Load error:', error);
                this.connectionStatus = 'Connection Error';
            } finally {
                this.isLoading = false;
            }
        },
        
        // Send message (RENAMED TO AVOID CONFLICT)
        async sendChatMessage() {
            const message = this.messageText.trim();
            if (!message || this.isSending || !this.conversationId) return;
            
            console.log('📤 Sending message:', message);
            this.isSending = true;
            
            // Add optimistic message
            const tempMessage = {
                id: 'temp_' + Date.now(),
                message: message,
                sender_name: 'You',
                is_from_admin: false,
                formatted_time: 'Sending...',
                temp: true
            };
            
            this.messages.push(tempMessage);
            this.messageText = '';
            
            // Auto scroll
            this.$nextTick(() => this.scrollToBottom());
            
            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        conversation_id: this.conversationId,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove temp message and add real one
                    this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                    this.messages.push(data.data);
                    this.lastMessageId = Math.max(this.lastMessageId, data.data.id);
                    
                    console.log('✅ Message sent successfully');
                } else {
                    throw new Error('Send failed');
                }
            } catch (error) {
                console.error('❌ Send error:', error);
                
                // Remove failed message and restore text
                this.messages = this.messages.filter(m => m.id !== tempMessage.id);
                this.messageText = message;
                
                alert('Failed to send message. Please try again.');
            } finally {
                this.isSending = false;
                this.$refs.messageInput.focus();
            }
        },
        
        // Polling for new messages (REDUCED FREQUENCY)
        async pollMessages() {
            if (!this.conversationId || this.isLoading) return;
            
            try {
                const response = await fetch(
                    `/chat/messages?conversation_id=${this.conversationId}&after_id=${this.lastMessageId}`,
                    {
                        signal: AbortSignal.timeout(10000) // 10s timeout for polling
                    }
                );
                
                const data = await response.json();
                
                if (data.success && data.messages && data.messages.length > 0) {
                    console.log(`📨 Got ${data.messages.length} new messages`);
                    
                    data.messages.forEach(message => {
                        this.messages.push(message);
                        this.lastMessageId = Math.max(this.lastMessageId, message.id);
                    });
                    
                    // Update unread count if chat closed
                    if (!this.isOpen) {
                        const newAdminMessages = data.messages.filter(m => m.is_from_admin);
                        this.unreadCount += newAdminMessages.length;
                    }
                    
                    // Auto scroll if chat open
                    if (this.isOpen) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                }
            } catch (error) {
                // Silent fail for polling
                if (error.name !== 'TimeoutError') {
                    console.log('📨 Polling error (silent):', error.message);
                }
            }
        },
        
        // Start polling (REDUCED TO 15s INTERVAL)
        startPolling() {
            this.stopPolling();
            this.polling = setInterval(() => {
                if (!document.hidden) {
                    this.pollMessages();
                }
            }, 15000); // Changed from 5s to 15s to reduce spam
            console.log('🔄 Polling started (15s interval)');
        },
        
        // Stop polling
        stopPolling() {
            if (this.polling) {
                clearInterval(this.polling);
                this.polling = null;
            }
        },
        
        // Toggle chat window
        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },
        
        // Open chat
        async open() {
            this.isOpen = true;
            this.unreadCount = 0;
            
            // Focus input after transition
            setTimeout(() => {
                this.$refs.messageInput?.focus();
                this.scrollToBottom();
            }, 300);
            
            // Mark messages as read
            this.markAsRead();
            
            console.log('💬 Chat opened');
        },
        
        // Close chat
        close() {
            this.isOpen = false;
            console.log('💬 Chat closed');
        },
        
        // Mark messages as read
        async markAsRead() {
            if (!this.conversationId) return;
            
            try {
                await fetch('/chat/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        conversation_id: this.conversationId
                    })
                });
            } catch (error) {
                console.error('❌ Mark read error:', error);
            }
        },
        
        // Scroll to bottom
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        
        // Get CSRF token
        getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        },
        
        // Cleanup on destroy
        destroy() {
            this.stopPolling();
        }
    }
}

// Auto cleanup on page unload
window.addEventListener('beforeunload', () => {
    // Alpine will handle cleanup automatically
});

console.log('✅ Alpine Chat Widget loaded - Final Version');
</script>

<style>
/* Chat widget styles */
#chat-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Message animations */
.message-wrapper {
    animation: messageSlideIn 0.3s ease-out;
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Scrollbar */
[x-ref="messagesContainer"]::-webkit-scrollbar {
    width: 4px;
}

[x-ref="messagesContainer"]::-webkit-scrollbar-track {
    background: transparent;
}

[x-ref="messagesContainer"]::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 2px;
}

/* Mobile responsive */
@media (max-width: 480px) {
    #chat-widget .w-96 {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
    }
}
</style>