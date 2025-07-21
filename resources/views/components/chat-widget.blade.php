<!-- Chat Widget Component -->
<div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle" onclick="toggleChat()" 
            class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 flex items-center justify-center">
        <svg id="chat-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 19l1.98-5.874A8.955 8.955 0 013 9c0-4.418 3.582-8 8-8s8 3.582 8 8z">
            </path>
        </svg>
        <span id="unread-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center hidden">0</span>
    </button>
    
    <!-- Chat Box -->
    <div id="chat-box" class="hidden mb-4 bg-white rounded-lg shadow-xl w-80 h-96 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">Customer Support</h3>
            <button onclick="closeChat()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
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
        
        <!-- Loading Indicator -->
        <div id="chat-loading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                <p class="text-gray-600 text-sm mt-2">Connecting...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Chat Widget JavaScript
let chatWidget = {
    isOpen: false,
    conversationId: null,
    pollingInterval: null,
    
    // Initialize chat widget
    init() {
        // Start polling when authenticated
        @auth
            this.getOrCreateConversation();
        @endauth
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
                this.loadMessages();
                this.startPolling();
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
        }
    },
    
    // Load messages
    async loadMessages() {
        if (!this.conversationId) return;
        
        try {
            const response = await fetch(`/chat/messages?conversation_id=${this.conversationId}`);
            
            if (response.ok) {
                const data = await response.json();
                this.displayMessages(data.messages);
                this.updateUnreadBadge();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    },
    
    // Display messages in chat
    displayMessages(messages) {
        const messagesContainer = document.getElementById('chat-messages');
        messagesContainer.innerHTML = '';
        
        if (messages.length === 0) {
            messagesContainer.innerHTML = `
                <div class="text-center text-gray-500 text-sm">
                    <p>Hello! How can we help you today?</p>
                </div>
            `;
            return;
        }
        
        messages.forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${message.is_from_admin ? 'justify-start' : 'justify-end'}`;
            
            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                    message.is_from_admin 
                        ? 'bg-gray-100 text-gray-800' 
                        : 'bg-blue-500 text-white'
                }">
                    <p class="text-sm">${this.escapeHtml(message.message)}</p>
                    <p class="text-xs opacity-75 mt-1">${message.formatted_time}</p>
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
        });
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    },
    
    // Start polling for new messages
    startPolling() {
        if (this.pollingInterval) return;
        
        this.pollingInterval = setInterval(() => {
            if (this.conversationId) {
                this.loadMessages();
            }
        }, 5000); // Poll every 5 seconds
    },
    
    // Stop polling
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    },
    
    // Update unread badge
    updateUnreadBadge() {
        // Badge logic can be implemented based on unread count
        const badge = document.getElementById('unread-badge');
        // For now, hide badge when chat is open
        if (this.isOpen) {
            badge.classList.add('hidden');
        }
    },
    
    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Global functions for onclick handlers
function toggleChat() {
    const chatBox = document.getElementById('chat-box');
    const chatIcon = document.getElementById('chat-icon');
    
    if (chatWidget.isOpen) {
        closeChat();
    } else {
        openChat();
    }
}

function openChat() {
    const chatBox = document.getElementById('chat-box');
    chatBox.classList.remove('hidden');
    chatWidget.isOpen = true;
    
    @auth
        if (!chatWidget.conversationId) {
            chatWidget.getOrCreateConversation();
        } else {
            chatWidget.loadMessages();
        }
    @else
        // Show login message for guests
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
}

async function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const message = messageInput.value.trim();
    
    if (!message || !chatWidget.conversationId) return;
    
    // Disable input while sending
    messageInput.disabled = true;
    sendButton.disabled = true;
    
    try {
        const response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                conversation_id: chatWidget.conversationId,
                message: message
            })
        });
        
        if (response.ok) {
            messageInput.value = '';
            chatWidget.loadMessages(); // Reload messages to show new message
        } else {
            console.error('Error sending message');
        }
    } catch (error) {
        console.error('Error sending message:', error);
    } finally {
        // Re-enable input
        messageInput.disabled = false;
        sendButton.disabled = false;
        messageInput.focus();
    }
}

// Initialize chat widget when page loads
document.addEventListener('DOMContentLoaded', function() {
    chatWidget.init();
    
    // Stop polling when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            chatWidget.stopPolling();
        } else if (chatWidget.conversationId) {
            chatWidget.startPolling();
        }
    });
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    chatWidget.stopPolling();
});
</script>

<style>
/* Chat widget animations */
#chat-widget #chat-box {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Custom scrollbar for chat messages */
#chat-messages::-webkit-scrollbar {
    width: 4px;
}

#chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

#chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>