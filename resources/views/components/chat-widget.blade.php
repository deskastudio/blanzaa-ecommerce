{{-- File: resources/views/components/chat-widget.blade.php --}}
{{-- Main Chat Widget - Complete Implementation --}}

{{-- Meta tags for authentication and CSRF --}}
<meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="chat-widget" class="fixed bottom-4 right-4 z-50 font-sans">
    {{-- Toggle Button --}}
    <button id="chat-toggle" onclick="toggleChat()" 
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
    
    {{-- Chat Window --}}
    <div id="chat-window" style="display: none;" class="absolute bottom-16 right-0 w-96 h-[500px] bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden">
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
                    <p id="support-status" class="text-xs text-blue-100">Connecting...</p>
                </div>
            </div>
            
            {{-- Close Button --}}
            <button onclick="closeChat()" 
                    class="text-blue-100 hover:text-white transition-colors p-1 rounded hover:bg-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Messages Container --}}
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
        </div>
        
        {{-- Input Area --}}
        <div class="p-4 border-t border-gray-200 bg-white">
            {{-- Input Form --}}
            <form id="chat-form" onsubmit="sendMessage(event)" class="space-y-3">
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
                    <span id="char-count" style="display: none;">0/500</span>
                </div>
            </form>
        </div>
        
        {{-- Debug Info (hidden in production) --}}
        <div style="display: none;" class="bg-gray-100 px-4 py-2 text-xs text-gray-600 rounded-b-lg border-t">
            <div class="flex justify-between items-center">
                <span>Response time:</span>
                <span id="debug-time" class="bg-gray-600 text-white px-2 py-1 rounded text-xs">-</span>
            </div>
        </div>
    </div>
</div>

{{-- Status Indicator (include dari file terpisah) --}}
@include('components.chat-widget.status-indicator')

{{-- Message Templates (include dari file terpisah) --}}
@include('components.chat-widget.message-item')

{{-- Main JavaScript --}}
<script>
console.log('🚀 Loading Chat Widget...');

// Simple ChatWidget object
window.ChatWidget = {
    isOpen: false,
    conversationId: null,
    lastMessageId: 0,
    isLoading: false,
    polling: null,
    
    elements: {
        widget: null,
        toggle: null,
        window: null,
        messagesList: null,
        messagesContainer: null,
        messageInput: null,
        sendBtn: null,
        unreadBadge: null,
        typingIndicator: null
    },
    
    // Initialize
    init() {
        console.log('🔧 Initializing ChatWidget...');
        
        // Check authentication
        const authMeta = document.querySelector('meta[name="user-authenticated"]');
        if (!authMeta || authMeta.content !== 'true') {
            console.log('❌ User not authenticated');
            this.hideWidget();
            return;
        }
        
        // Cache elements
        this.cacheElements();
        
        // Initialize components
        this.setupEventListeners();
        
        // Get conversation
        this.getConversation();
        
        console.log('✅ ChatWidget initialized');
    },
    
    // Cache DOM elements
    cacheElements() {
        this.elements.widget = document.getElementById('chat-widget');
        this.elements.toggle = document.getElementById('chat-toggle');
        this.elements.window = document.getElementById('chat-window');
        this.elements.messagesList = document.getElementById('messages-list');
        this.elements.messagesContainer = document.getElementById('messages-container');
        this.elements.messageInput = document.getElementById('message-input');
        this.elements.sendBtn = document.getElementById('send-btn');
        this.elements.unreadBadge = document.getElementById('unread-badge');
        this.elements.typingIndicator = document.getElementById('typing-indicator');
        
        console.log('📦 Elements cached');
    },
    
    // Hide widget
    hideWidget() {
        if (this.elements.widget) {
            this.elements.widget.style.display = 'none';
        }
    },
    
    // Setup event listeners
    setupEventListeners() {
        // Input character counter
        if (this.elements.messageInput) {
            this.elements.messageInput.addEventListener('input', () => {
                this.updateCharCounter();
            });
            
            // Enter key handling
            this.elements.messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }
        
        console.log('🎧 Event listeners setup');
    },
    
    // Update character counter
    updateCharCounter() {
        const input = this.elements.messageInput;
        const counter = document.getElementById('char-count');
        
        if (input && counter) {
            const length = input.value.length;
            counter.textContent = `${length}/500`;
            
            if (length > 0) {
                counter.style.display = 'block';
            } else {
                counter.style.display = 'none';
            }
            
            // Color coding
            if (length > 450) {
                counter.style.color = '#ef4444';
            } else {
                counter.style.color = '#6b7280';
            }
        }
    },
    
    // Get conversation
    async getConversation() {
        try {
            console.log('🔄 Getting conversation...');
            
            const response = await fetch('/chat/conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.conversation_id) {
                this.conversationId = data.conversation_id;
                console.log('✅ Conversation ready:', this.conversationId);
                
                // Start polling
                this.startPolling();
                
                // Update badge
                this.updateUnreadBadge();
            } else {
                console.error('❌ Failed to get conversation:', data);
            }
        } catch (error) {
            console.error('❌ Conversation error:', error);
        }
    },
    
    // Send message
    async sendMessage(event) {
        if (event) {
            event.preventDefault();
        }
        
        const message = this.elements.messageInput?.value?.trim();
        if (!message || !this.conversationId || this.isLoading) {
            return;
        }
        
        console.log('📤 Sending message:', message);
        
        this.isLoading = true;
        this.setInputState(false);
        
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
                // Clear input
                this.elements.messageInput.value = '';
                this.updateCharCounter();
                
                // Add message to UI
                if (data.data) {
                    this.addMessage(data.data);
                    this.lastMessageId = Math.max(this.lastMessageId, data.data.id);
                }
                
                console.log('✅ Message sent successfully');
            } else {
                console.error('❌ Failed to send message:', data);
                alert('Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('❌ Send error:', error);
            alert('Network error. Please check your connection.');
        } finally {
            this.isLoading = false;
            this.setInputState(true);
        }
    },
    
    // Add message to UI
    addMessage(message) {
        if (!this.elements.messagesList) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-wrapper mb-3 ${message.is_from_admin ? 'admin-message' : 'user-message'}`;
        messageDiv.setAttribute('data-message-id', message.id);
        
        if (message.is_from_admin) {
            messageDiv.innerHTML = `
                <div class="flex justify-start">
                    <div class="max-w-xs">
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm shadow-sm">
                                    <div class="text-xs text-gray-500 mb-1">${message.sender_name || 'Support'}</div>
                                    <p class="text-sm text-gray-800">${this.escapeHtml(message.message)}</p>
                                    <div class="text-xs text-gray-500 mt-1">${message.formatted_time}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="flex justify-end">
                    <div class="max-w-xs">
                        <div class="bg-blue-500 text-white px-4 py-2 rounded-lg rounded-br-sm shadow-sm">
                            <p class="text-sm">${this.escapeHtml(message.message)}</p>
                            <div class="flex justify-between items-center mt-1 text-xs text-blue-100">
                                <span>${message.formatted_time}</span>
                                <span>
                                    <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        this.elements.messagesList.appendChild(messageDiv);
        this.scrollToBottom();
    },
    
    // Load new messages
    async loadMessages() {
        if (!this.conversationId || this.isLoading) return;
        
        try {
            const response = await fetch(`/chat/messages?conversation_id=${this.conversationId}&after_id=${this.lastMessageId}`);
            const data = await response.json();
            
            if (data.success && data.messages && data.messages.length > 0) {
                console.log(`📨 Loaded ${data.messages.length} new messages`);
                
                data.messages.forEach(msg => {
                    this.addMessage(msg);
                    this.lastMessageId = Math.max(this.lastMessageId, msg.id);
                });
                
                // Update badge if closed
                if (!this.isOpen) {
                    this.updateUnreadBadge();
                }
            }
        } catch (error) {
            console.error('❌ Load messages error:', error);
        }
    },
    
    // Start polling
    startPolling() {
        this.stopPolling();
        
        this.polling = setInterval(() => {
            if (!document.hidden) {
                this.loadMessages();
            }
        }, 15000);
        
        console.log('🔄 Polling started');
    },
    
    // Stop polling
    stopPolling() {
        if (this.polling) {
            clearInterval(this.polling);
            this.polling = null;
        }
    },
    
    // Update unread badge
    async updateUnreadBadge() {
        if (!this.conversationId || !this.elements.unreadBadge) return;
        
        try {
            const response = await fetch(`/chat/unread-count?conversation_id=${this.conversationId}`);
            const data = await response.json();
            
            const count = data.count || 0;
            
            if (count > 0 && !this.isOpen) {
                this.elements.unreadBadge.textContent = count > 99 ? '99+' : count;
                this.elements.unreadBadge.style.display = 'flex';
            } else {
                this.elements.unreadBadge.style.display = 'none';
            }
        } catch (error) {
            console.error('❌ Unread count error:', error);
        }
    },
    
    // Toggle chat
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    },
    
    // Open chat
    async open() {
        if (!this.elements.window) return;
        
        this.elements.window.style.display = 'flex';
        this.elements.window.style.flexDirection = 'column';
        this.isOpen = true;
        
        // Focus input
        setTimeout(() => {
            if (this.elements.messageInput) {
                this.elements.messageInput.focus();
            }
        }, 300);
        
        // Mark as read
        this.markAsRead();
        
        // Update badge
        this.updateUnreadBadge();
        
        console.log('💬 Chat opened');
    },
    
    // Close chat
    close() {
        if (!this.elements.window) return;
        
        this.elements.window.style.display = 'none';
        this.isOpen = false;
        
        console.log('💬 Chat closed');
    },
    
    // Mark as read
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
    
    // Set input state
    setInputState(enabled) {
        if (this.elements.messageInput) {
            this.elements.messageInput.disabled = !enabled;
        }
        if (this.elements.sendBtn) {
            this.elements.sendBtn.disabled = !enabled;
        }
    },
    
    // Scroll to bottom
    scrollToBottom() {
        if (this.elements.messagesContainer) {
            setTimeout(() => {
                this.elements.messagesContainer.scrollTop = this.elements.messagesContainer.scrollHeight;
            }, 100);
        }
    },
    
    // Get CSRF token
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    },
    
    // Escape HTML
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Global functions
function toggleChat() {
    window.ChatWidget.toggle();
}

function sendMessage(event) {
    window.ChatWidget.sendMessage(event);
}

function closeChat() {
    window.ChatWidget.close();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.ChatWidget.init();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    window.ChatWidget.stopPolling();
});

console.log('✅ Chat Widget script loaded');
</script>

{{-- Styles --}}
<style>
/* Chat widget specific styles */
#chat-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Chat window animations */
#chat-window {
    z-index: 9999;
}

/* Toggle button pulse animation */
#chat-toggle {
    animation: chatPulse 2s infinite;
}

@keyframes chatPulse {
    0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
}

/* Disable pulse when chat is open */
.chat-open #chat-toggle {
    animation: none;
}

/* Badge animation */
#unread-badge {
    animation: badgeBounce 0.5s ease-out;
}

@keyframes badgeBounce {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Messages area scrollbar */
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

/* Mobile responsive */
@media (max-width: 480px) {
    #chat-widget {
        position: fixed;
        bottom: 0;
        right: 0;
        left: 0;
        width: 100%;
    }
    
    #chat-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 10000;
    }
    
    #chat-window {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        max-width: none !important;
        max-height: none !important;
        border: none !important;
    }
}

/* Input focus effects */
#message-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Send button loading state */
#send-btn:disabled {
    background-color: #9ca3af !important;
}
</style>