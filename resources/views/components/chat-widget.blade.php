<!-- Fixed Chat Widget - No Duplicates -->
<div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Toggle Button -->
    <button id="chat-toggle" onclick="toggleChat()" 
            class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-full shadow-lg transition-colors relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 19l1.98-5.874A8.955 8.955 0 313 9c0-4.418 3.582-8 8-8s8 3.582 8 8z">
            </path>
        </svg>
        <span id="unread-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center hidden">0</span>
    </button>
    
    <!-- Chat Box -->
    <div id="chat-box" class="hidden mb-4 bg-white rounded-lg shadow-xl w-80 h-96 flex flex-col">
        <!-- Header -->
        <div class="bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">Support Chat</h3>
            <div class="flex items-center space-x-2">
                <span id="debug-time" class="text-xs bg-blue-600 px-2 py-1 rounded">0ms</span>
                <button onclick="closeChat()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Messages -->
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto">
            <div class="text-center text-gray-500 text-sm">
                <p>Hello! How can we help you today?</p>
            </div>
        </div>
        
        <!-- Input -->
        <div class="p-4 border-t">
            <form id="chat-form" onsubmit="sendMessage(event)" class="flex space-x-2">
                <input type="text" id="message-input" placeholder="Type your message..." 
                       class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="500" required>
                <button type="submit" id="send-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// FIXED Chat Widget - Prevent Duplicates & Handle Auth
const chat = {
    isOpen: false,
    conversationId: null,
    lastMessageId: 0,
    messages: new Map(), // Use Map to prevent duplicates by ID
    polling: null,
    isLoading: false,
    hasLoadedInitial: false,
    
    async init() {
        @auth
            await this.getConversation();
            // Only start polling after conversation is ready
            if (this.conversationId) {
                this.startPolling();
            }
        @else
            // Hide widget if not authenticated
            console.log('❌ User not authenticated - chat disabled');
            document.getElementById('chat-widget').style.display = 'none';
        @endauth
    },
    
    async getConversation() {
        if (this.isLoading) return;
        this.isLoading = true;
        
        try {
            const response = await fetch('/chat/conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            
            // PERBAIKAN: Check content type to avoid JSON parse error
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('❌ Response is not JSON, user might not be authenticated');
                return;
            }
            
            if (response.ok) {
                const data = await response.json();
                this.conversationId = data.conversation_id;
                console.log('✅ Conversation ready:', this.conversationId);
                
                // Load unread count immediately
                await this.updateUnreadBadge();
            } else {
                console.error('❌ Conversation request failed:', response.status);
            }
        } catch (error) {
            console.error('❌ Conversation error:', error);
        } finally {
            this.isLoading = false;
        }
    },
    
    async sendMessage(message) {
        if (!message.trim() || !this.conversationId || this.isLoading) return false;
        
        const startTime = performance.now();
        this.isLoading = true;
        
        // Disable input
        const input = document.getElementById('message-input');
        const button = document.getElementById('send-btn');
        input.disabled = true;
        button.disabled = true;
        
        // Show optimistic message
        const tempId = 'temp_' + Date.now();
        this.addMessageToMap({
            id: tempId,
            message: message,
            is_from_admin: false,
            formatted_time: 'Sending...',
            optimistic: true
        });
        
        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    message: message
                })
            });
            
            // PERBAIKAN: Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            if (response.ok) {
                const data = await response.json();
                
                // Remove optimistic message
                this.messages.delete(tempId);
                
                if (data.success && data.data) {
                    // Add real message
                    this.addMessageToMap({
                        id: data.data.id,
                        message: data.data.message,
                        sender_name: data.data.sender_name,
                        is_from_admin: data.data.is_from_admin,
                        formatted_time: data.data.formatted_time
                    });
                    
                    this.lastMessageId = Math.max(this.lastMessageId, data.data.id);
                }
                
                this.renderMessages();
                this.updateDebugTime(performance.now() - startTime);
                return true;
            }
        } catch (error) {
            console.error('❌ Send error:', error);
            // Remove optimistic message on error
            this.messages.delete(tempId);
            this.renderMessages();
        } finally {
            this.isLoading = false;
            input.disabled = false;
            button.disabled = false;
        }
        
        return false;
    },
    
    async loadNewMessages() {
        if (!this.conversationId || this.isLoading) return;
        
        try {
            const afterId = this.hasLoadedInitial ? this.lastMessageId : 0;
            const response = await fetch(
                `/chat/messages?conversation_id=${this.conversationId}&after_id=${afterId}`,
                { credentials: 'same-origin' }
            );
            
            // PERBAIKAN: Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('❌ Messages response is not JSON');
                return;
            }
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.messages && data.messages.length > 0) {
                    console.log(`📨 Loading ${data.messages.length} messages (after_id: ${afterId})`);
                    
                    let hasNewMessages = false;
                    
                    data.messages.forEach(msg => {
                        // Only add if not already exists
                        if (!this.messages.has(msg.id)) {
                            this.addMessageToMap(msg);
                            this.lastMessageId = Math.max(this.lastMessageId, msg.id);
                            hasNewMessages = true;
                        }
                    });
                    
                    if (hasNewMessages) {
                        this.renderMessages();
                        
                        // Show notification if chat is closed
                        if (!this.isOpen) {
                            const adminMessages = data.messages.filter(m => m.is_from_admin);
                            if (adminMessages.length > 0) {
                                this.showNotification(adminMessages[adminMessages.length - 1]);
                            }
                        }
                        
                        this.updateUnreadBadge();
                    }
                } else {
                    console.log('📨 No new messages');
                }
                
                this.hasLoadedInitial = true;
                this.updateDebugTime(data.debug_time || 0);
            }
        } catch (error) {
            console.error('❌ Load messages error:', error);
        }
    },
    
    // Add message to Map (prevents duplicates)
    addMessageToMap(message) {
        this.messages.set(message.id, message);
        
        // Keep only last 50 messages to prevent memory issues
        if (this.messages.size > 50) {
            const sortedKeys = Array.from(this.messages.keys()).sort((a, b) => {
                // Handle temp IDs
                if (typeof a === 'string' && a.startsWith('temp_')) return 1;
                if (typeof b === 'string' && b.startsWith('temp_')) return -1;
                return a - b;
            });
            
            // Remove oldest messages
            const toRemove = sortedKeys.slice(0, this.messages.size - 50);
            toRemove.forEach(key => this.messages.delete(key));
        }
    },
    
    async updateUnreadBadge() {
        if (!this.conversationId) return;
        
        try {
            const response = await fetch(
                `/chat/unread-count?conversation_id=${this.conversationId}`,
                { credentials: 'same-origin' }
            );
            
            // PERBAIKAN: Check content type untuk unread count
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('❌ Unread count response is not JSON');
                return;
            }
            
            if (response.ok) {
                const data = await response.json();
                const badge = document.getElementById('unread-badge');
                
                if (data.count > 0 && !this.isOpen) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('❌ Unread count error:', error);
        }
    },
    
    renderMessages() {
        const container = document.getElementById('chat-messages');
        const shouldScrollToBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 10;
        
        container.innerHTML = '';
        
        if (this.messages.size === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 text-sm"><p>Hello! How can we help you today?</p></div>';
            return;
        }
        
        // Convert Map to Array and sort by ID
        const sortedMessages = Array.from(this.messages.values()).sort((a, b) => {
            // Handle temp IDs
            if (typeof a.id === 'string' && a.id.startsWith('temp_')) return 1;
            if (typeof b.id === 'string' && b.id.startsWith('temp_')) return -1;
            return a.id - b.id;
        });
        
        sortedMessages.forEach(msg => {
            const div = document.createElement('div');
            div.className = `mb-3 flex ${msg.is_from_admin ? 'justify-start' : 'justify-end'}`;
            
            const bgColor = msg.is_from_admin ? 'bg-gray-100 text-gray-800' : 
                           msg.optimistic ? 'bg-blue-400 text-white opacity-75' : 
                           'bg-blue-500 text-white';
            
            div.innerHTML = `
                <div class="max-w-xs px-3 py-2 rounded-lg ${bgColor}">
                    ${msg.is_from_admin ? `<div class="text-xs text-gray-500 mb-1">${msg.sender_name || 'Support'}</div>` : ''}
                    <p class="text-sm">${this.escapeHtml(msg.message)}</p>
                    <p class="text-xs opacity-75 mt-1">${msg.formatted_time}</p>
                </div>
            `;
            
            container.appendChild(div);
        });
        
        // Maintain scroll position
        if (shouldScrollToBottom) {
            container.scrollTop = container.scrollHeight;
        }
    },
    
    showNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification('New Message', {
                body: message.message,
                icon: '/favicon.ico'
            });
            
            notification.onclick = () => {
                window.focus();
                this.openChat();
            };
            
            setTimeout(() => notification.close(), 5000);
        }
    },
    
    async openChat() {
        document.getElementById('chat-box').classList.remove('hidden');
        this.isOpen = true;
        
        // Load initial messages if not loaded
        if (!this.hasLoadedInitial) {
            await this.loadNewMessages();
        }
        
        await this.markAsRead();
        this.updateUnreadBadge();
        
        setTimeout(() => {
            document.getElementById('message-input').focus();
        }, 100);
    },
    
    closeChat() {
        document.getElementById('chat-box').classList.add('hidden');
        this.isOpen = false;
    },
    
    async markAsRead() {
        if (!this.conversationId) return;
        
        try {
            await fetch('/chat/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    conversation_id: this.conversationId
                })
            });
        } catch (error) {
            console.error('❌ Mark read error:', error);
        }
    },
    
    startPolling() {
        this.stopPolling();
        
        // Reduce polling frequency to prevent spam
        this.polling = setInterval(() => {
            if (!document.hidden) {
                this.loadNewMessages();
            }
        }, 15000); // Every 15 seconds
        
        console.log('🔄 Polling started (15s interval)');
    },
    
    stopPolling() {
        if (this.polling) {
            clearInterval(this.polling);
            this.polling = null;
            console.log('⏹️ Polling stopped');
        }
    },
    
    updateDebugTime(ms) {
        const debugEl = document.getElementById('debug-time');
        if (debugEl) {
            const roundedMs = Math.round(ms);
            debugEl.textContent = roundedMs + 'ms';
            
            // Color coding
            debugEl.className = 'text-xs px-2 py-1 rounded';
            if (roundedMs < 100) {
                debugEl.className += ' bg-green-600';
            } else if (roundedMs < 500) {
                debugEl.className += ' bg-yellow-600';
            } else {
                debugEl.className += ' bg-red-600';
            }
        }
    },
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Global functions
async function toggleChat() {
    if (chat.isOpen) {
        chat.closeChat();
    } else {
        await chat.openChat();
    }
}

async function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    const success = await chat.sendMessage(message);
    if (success) {
        input.value = '';
    }
    
    input.focus();
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    chat.init();
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});

// Handle page visibility for better battery usage
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        chat.stopPolling();
    } else if (chat.conversationId) {
        chat.startPolling();
        // Immediate refresh when page becomes visible
        setTimeout(() => chat.loadNewMessages(), 1000);
    }
});

// Cleanup
window.addEventListener('beforeunload', () => {
    chat.stopPolling();
});
</script>
<style>
#chat-box {
    animation: slideUp 0.2s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

#chat-messages::-webkit-scrollbar {
    width: 4px;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 2px;
}

/* Loading state */
#send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

#message-input:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>