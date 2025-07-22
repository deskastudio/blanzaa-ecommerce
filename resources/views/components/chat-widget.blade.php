<!-- Chat Widget dengan Notifikasi Admin -->
<div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle" onclick="toggleChat()" 
            class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center relative">
        <svg id="chat-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 19l1.98-5.874A8.955 8.955 0 013 9c0-4.418 3.582-8 8-8s8 3.582 8 8z">
            </path>
        </svg>
        <span id="unread-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center hidden animate-pulse">0</span>
    </button>
    
    <!-- Chat Box -->
    <div id="chat-box" class="hidden mb-4 bg-white rounded-lg shadow-xl w-80 h-96 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">Customer Support</h3>
            <div class="flex items-center space-x-2">
                <span id="debug-time" class="text-xs bg-blue-600 px-2 py-1 rounded">0ms</span>
                <button onclick="closeChat()" class="text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3">
            <div class="text-center text-gray-500 text-sm">
                <p>Hello! How can we help you today?</p>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="p-4 border-t">
            <form id="chat-form" onsubmit="sendMessage(event)" class="flex space-x-2">
                <input type="text" id="message-input" placeholder="Type your message..." 
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       maxlength="1000" required>
                <button type="submit" id="send-button"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
// Chat Widget dengan Notifikasi
let chatWidget = {
    isOpen: false,
    conversationId: null,
    pollingInterval: null,
    lastMessageId: 0,
    allMessages: [],
    isRefreshing: false,
    lastRefreshTime: 0,
    refreshDebounceMs: 1000,
    lastKnownMessageCount: 0, // Track untuk deteksi pesan baru
    
    // Initialize
    async init() {
        @auth
            await this.getOrCreateConversation();
            this.requestNotificationPermission();
        @endauth
    },
    
    // Request notification permission
    requestNotificationPermission() {
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    },
    
    // Show browser notification
    showBrowserNotification(message, senderName) {
        if ("Notification" in window && Notification.permission === "granted") {
            const notification = new Notification(`New message from ${senderName}`, {
                body: message.length > 50 ? message.substring(0, 50) + '...' : message,
                icon: '/favicon.ico',
                tag: 'chat-message'
            });
            
            notification.onclick = function() {
                window.focus();
                openChat();
                notification.close();
            };
            
            // Auto close after 5 seconds
            setTimeout(() => notification.close(), 5000);
        }
    },
    
    // Show toast notification (persistent)
    showToastNotification(message, senderName) {
        const container = document.getElementById('notification-container');
        const toast = document.createElement('div');
        toast.className = 'bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full persistent-notification';
        
        const toastId = 'toast-' + Date.now();
        toast.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 rounded-full p-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 19l1.98-5.874A8.955 8.955 0 313 9c0-4.418 3.582-8 8-8s8 3.582 8 8z">
                        </path>
                    </svg>
                </div>
                <div class="flex-1 cursor-pointer" onclick="chatWidget.handleNotificationClick('${toastId}')">
                    <p class="font-semibold text-sm">${senderName}</p>
                    <p class="text-sm opacity-90">${message.length > 40 ? message.substring(0, 40) + '...' : message}</p>
                    <p class="text-xs opacity-75 mt-1">Click to open chat</p>
                </div>
                <button onclick="chatWidget.closeToast('${toastId}')" class="text-white hover:text-gray-200 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        toast.id = toastId;
        container.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Make it pulse every 3 seconds to grab attention
        setInterval(() => {
            if (document.getElementById(toastId)) {
                toast.classList.add('animate-bounce');
                setTimeout(() => {
                    toast.classList.remove('animate-bounce');
                }, 1000);
            }
        }, 5000);
        
        // NO AUTO-REMOVAL - stays until chat opened or manually closed
    },
    
    // Handle notification click
    handleNotificationClick(toastId) {
        openChat();
        this.clearAllNotifications(); // Clear all notifications when chat opened
    },
    
    // Clear all persistent notifications
    clearAllNotifications() {
        const notifications = document.querySelectorAll('.persistent-notification');
        notifications.forEach(notification => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
    },
    
    // Close toast notification
    closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    },
    
    // Get or create conversation
    async getOrCreateConversation() {
        try {
            const response = await fetch('/chat/conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.conversationId = data.conversation_id;
                
                await this.refreshMessages();
                this.startPolling();
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
        }
    },
    
    // Refresh messages with notification detection
    async refreshMessages() {
        if (this.isRefreshing) {
            return;
        }
        
        const now = Date.now();
        if (now - this.lastRefreshTime < this.refreshDebounceMs) {
            return;
        }
        
        if (!this.conversationId) return;
        
        this.isRefreshing = true;
        this.lastRefreshTime = now;
        const startTime = performance.now();
        
        try {
            const url = `/chat/messages?conversation_id=${this.conversationId}&after_id=0`;
            const response = await fetch(url);
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.messages) {
                    const newMessageCount = data.messages.length;
                    const previousMessageCount = this.lastKnownMessageCount;
                    
                    // Detect new messages
                    if (newMessageCount > previousMessageCount && previousMessageCount > 0) {
                        const newMessages = data.messages.slice(previousMessageCount);
                        
                        // Check for admin messages
                        const newAdminMessages = newMessages.filter(msg => msg.is_from_admin);
                        
                        if (newAdminMessages.length > 0) {
                            // Show notifications for new admin messages
                            newAdminMessages.forEach(msg => {
                                this.showNotification(msg.message, msg.sender_name);
                            });
                            
                            // Update unread badge if chat is closed
                            if (!this.isOpen) {
                                this.updateUnreadBadge(newAdminMessages.length);
                            }
                        }
                    }
                    
                    // Update local state
                    this.allMessages = data.messages;
                    this.lastKnownMessageCount = newMessageCount;
                    
                    if (this.allMessages.length > 0) {
                        this.lastMessageId = Math.max(...this.allMessages.map(m => m.id));
                    }
                    
                    // Re-render if chat is open
                    if (this.isOpen) {
                        this.renderAllMessages();
                        this.updateUnreadBadge(0); // Clear badge when chat is open
                    }
                }
                
                const endTime = performance.now();
                this.updateDebugTime(endTime - startTime);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        } finally {
            this.isRefreshing = false;
        }
    },
    
    // Show notification (both toast and browser)
    showNotification(message, senderName) {
        // Show toast notification
        this.showToastNotification(message, senderName);
        
        // Show browser notification if page is not visible
        if (document.hidden) {
            this.showBrowserNotification(message, senderName);
        }
        
        // Play notification sound (optional)
        this.playNotificationSound();
    },
    
    // Play notification sound
    playNotificationSound() {
        try {
            // Create audio element for notification sound
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmMbBjiS2e/CdSgFL4PQ8tiKOQgZab7t4ptNEQtVqOLwtW'); 
            audio.volume = 0.3;
            audio.play().catch(() => {}); // Ignore errors if autoplay blocked
        } catch (error) {
            // Ignore audio errors
        }
    },
    
    // Update unread badge
    updateUnreadBadge(additionalCount = 0) {
        const badge = document.getElementById('unread-badge');
        
        if (this.isOpen) {
            badge.classList.add('hidden');
            return;
        }
        
        // Count unread admin messages
        const unreadCount = this.allMessages.filter(msg => 
            msg.is_from_admin && !msg.is_read
        ).length + additionalCount;
        
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.remove('hidden');
            badge.classList.add('animate-pulse');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('animate-pulse');
        }
    },
    
    // Render all messages
    renderAllMessages() {
        const container = document.getElementById('chat-messages');
        container.innerHTML = '';
        
        if (this.allMessages.length === 0) {
            container.innerHTML = `
                <div class="text-center text-gray-500 text-sm">
                    <p>Hello! How can we help you today?</p>
                </div>
            `;
            return;
        }
        
        const fragment = document.createDocumentFragment();
        
        this.allMessages.forEach(message => {
            const div = document.createElement('div');
            div.className = `flex ${message.is_from_admin ? 'justify-start' : 'justify-end'}`;
            
            const isNew = message.is_from_admin && !message.is_read;
            const messageClass = message.is_from_admin 
                ? `bg-gray-100 text-gray-800 ${isNew ? 'ring-2 ring-blue-300' : ''}` 
                : 'bg-blue-500 text-white';
            
            div.innerHTML = `
                <div class="max-w-xs px-3 py-2 rounded-lg ${messageClass}">
                    ${message.is_from_admin ? '<div class="text-xs text-gray-500 mb-1">Support Agent</div>' : ''}
                    <p class="text-sm">${this.escapeHtml(message.message)}</p>
                    <p class="text-xs opacity-75 mt-1">${message.formatted_time}</p>
                    ${isNew ? '<div class="text-xs text-blue-600 font-semibold mt-1">New</div>' : ''}
                </div>
            `;
            
            fragment.appendChild(div);
        });
        
        container.appendChild(fragment);
        container.scrollTop = container.scrollHeight;
    },
    
    // Send message
    async sendMessage(message) {
        if (!message.trim() || !this.conversationId) return false;
        
        const startTime = performance.now();
        this.showOptimisticMessage(message);
        
        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    message: message
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                const endTime = performance.now();
                this.updateDebugTime(endTime - startTime);
                
                this.removeOptimisticMessage();
                
                if (data.data) {
                    const newMessage = {
                        id: data.data.id,
                        message: data.data.message,
                        is_from_admin: data.data.is_from_admin,
                        formatted_time: data.data.formatted_time,
                        sender_name: data.data.sender_name
                    };
                    
                    this.allMessages.push(newMessage);
                    this.lastMessageId = Math.max(this.lastMessageId, newMessage.id);
                    this.lastKnownMessageCount = this.allMessages.length;
                    
                    this.renderAllMessages();
                }
                
                return true;
            } else {
                this.removeOptimisticMessage();
                return false;
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.removeOptimisticMessage();
            return false;
        }
    },
    
    // Show optimistic message
    showOptimisticMessage(message) {
        const container = document.getElementById('chat-messages');
        const div = document.createElement('div');
        div.className = 'flex justify-end optimistic-message';
        
        div.innerHTML = `
            <div class="max-w-xs px-3 py-2 rounded-lg bg-blue-400 text-white opacity-75">
                <p class="text-sm">${this.escapeHtml(message)}</p>
                <p class="text-xs opacity-75 mt-1">Sending...</p>
            </div>
        `;
        
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    },
    
    // Remove optimistic message
    removeOptimisticMessage() {
        const optimistic = document.querySelector('.optimistic-message');
        if (optimistic) {
            optimistic.remove();
        }
    },
    
    // Update debug time
    updateDebugTime(timeMs) {
        const debugElement = document.getElementById('debug-time');
        if (debugElement) {
            debugElement.textContent = Math.round(timeMs) + 'ms';
        }
    },
    
    // Start polling
    startPolling() {
        this.stopPolling();
        
        this.pollingInterval = setInterval(() => {
            if (!document.hidden) { // Poll when page is visible
                this.refreshMessages();
            }
        }, 10000); // Poll every 10 seconds
    },
    
    // Stop polling
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
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
    if (chatWidget.isOpen) {
        closeChat();
    } else {
        openChat();
    }
}

async function openChat() {
    const chatBox = document.getElementById('chat-box');
    chatBox.classList.remove('hidden');
    chatWidget.isOpen = true;
    
    @auth
        if (!chatWidget.conversationId) {
            await chatWidget.getOrCreateConversation();
        } else {
            if (chatWidget.allMessages.length > 0) {
                chatWidget.renderAllMessages();
            }
            setTimeout(() => {
                chatWidget.refreshMessages();
            }, 1000);
        }
        chatWidget.startPolling();
        
        // Clear unread badge and ALL notifications when opening chat
        chatWidget.updateUnreadBadge(0);
        chatWidget.clearAllNotifications();
    @else
        document.getElementById('chat-messages').innerHTML = `
            <div class="text-center text-gray-500 text-sm">
                <p>Please <a href="/login" class="text-blue-500 hover:underline">login</a> to start a conversation.</p>
            </div>
        `;
    @endauth
}

function closeChat() {
    const chatBox = document.getElementById('chat-box');
    chatBox.classList.add('hidden');
    chatWidget.isOpen = false;
    chatWidget.stopPolling();
}

async function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('message-input');
    const button = document.getElementById('send-button');
    const message = input.value.trim();
    
    if (!message) return;
    
    input.disabled = true;
    button.disabled = true;
    
    const success = await chatWidget.sendMessage(message);
    
    if (success) {
        input.value = '';
    }
    
    input.disabled = false;
    button.disabled = false;
    input.focus();
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    chatWidget.init();
});

// Handle visibility change
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        chatWidget.stopPolling();
    } else if (chatWidget.conversationId) {
        // Resume polling when page becomes visible
        setTimeout(() => {
            chatWidget.startPolling();
        }, 2000);
    }
});

// Cleanup
window.addEventListener('beforeunload', () => {
    chatWidget.stopPolling();
});
</script>

<style>
#chat-widget #chat-box {
    animation: slideUp 0.2s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

#chat-messages::-webkit-scrollbar {
    width: 4px;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 2px;
}

.optimistic-message {
    animation: pulse 1s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.75; }
    50% { opacity: 0.5; }
}

/* Notification animations */
#notification-container > div {
    transition: all 0.3s ease-in-out;
}

/* Persistent notification styling */
.persistent-notification {
    border-left: 4px solid #1e40af;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.persistent-notification:hover {
    transform: scale(1.02);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
}

/* Attention-grabbing bounce animation */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
    40%, 43% { transform: translateY(-10px); }
    70% { transform: translateY(-5px); }
    90% { transform: translateY(-2px); }
}

.animate-bounce {
    animation: bounce 1s ease-in-out;
}

/* Unread badge pulse animation */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* New message highlight */
.ring-2 {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
</style>