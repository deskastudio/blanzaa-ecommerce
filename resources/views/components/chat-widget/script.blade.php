{{-- File: resources/views/components/chat-widget/scripts.blade.php --}}
{{-- Main Chat Widget JavaScript Logic --}}

<script>
// Display utilities to handle CSS conflicts
window.DisplayUtils = {
    show(element, displayType = 'block') {
        if (!element) return;
        element.style.display = displayType;
    },
    
    hide(element) {
        if (!element) return;
        element.style.display = 'none';
    },
    
    toggle(element, displayType = 'block') {
        if (!element) return;
        const isHidden = element.style.display === 'none';
        if (isHidden) {
            this.show(element, displayType);
        } else {
            this.hide(element);
        }
        return !isHidden;
    }
};

// Main Chat Widget Object
window.ChatWidget = {
    // Core properties
    isOpen: false,
    conversationId: null,
    lastMessageId: 0,
    messages: new Map(),
    polling: null,
    isLoading: false,
    hasLoadedInitial: false,
    isAuthenticated: false,
    
    // Configuration
    config: {
        pollingInterval: 15000, // 15 seconds
        maxMessages: 100,
        apiTimeout: 10000,
        retryAttempts: 3,
        debugMode: false
    },
    
    // Elements cache
    elements: {},
    
    // Initialize chat widget
    async init() {
        console.log('🚀 ChatWidget initializing...');
        
        // Check authentication
        this.isAuthenticated = this.checkAuthentication();
        if (!this.isAuthenticated) {
            console.log('❌ User not authenticated - hiding chat widget');
            this.hideWidget();
            return;
        }
        
        // Cache DOM elements
        this.cacheElements();
        
        // Initialize components
        if (!this.initializeComponents()) {
            console.error('❌ Failed to initialize components');
            return;
        }
        
        // Get or create conversation
        await this.getOrCreateConversation();
        
        // Start polling if conversation exists
        if (this.conversationId) {
            this.startPolling();
            StatusIndicator.showOnline();
        }
        
        // Setup event listeners
        this.setupEventListeners();
        
        console.log('✅ ChatWidget initialized successfully');
    },
    
    // Check if user is authenticated
    checkAuthentication() {
        const authMeta = document.querySelector('meta[name="user-authenticated"]');
        return authMeta && authMeta.content === 'true';
    },
    
    // Cache DOM elements for performance
    cacheElements() {
        this.elements = {
            widget: document.getElementById('chat-widget'),
            toggle: document.getElementById('chat-toggle'),
            window: document.getElementById('chat-window'),
            messagesList: document.getElementById('messages-list'),
            messagesContainer: document.getElementById('messages-container'),
            messageInput: document.getElementById('message-input'),
            sendBtn: document.getElementById('send-btn'),
            unreadBadge: document.getElementById('unread-badge'),
            welcomeMessage: document.getElementById('welcome-message'),
            typingIndicator: document.getElementById('typing-indicator'),
            debugTime: document.getElementById('debug-time'),
            chatForm: document.getElementById('chat-form')
        };
        
        // Check for missing elements
        const missingElements = Object.entries(this.elements)
            .filter(([key, element]) => !element)
            .map(([key]) => key);
            
        if (missingElements.length > 0) {
            console.warn('⚠️ Missing elements:', missingElements);
        }
    },
    
    // Initialize sub-components
    initializeComponents() {
        try {
            // Initialize Message Renderer
            if (window.MessageRenderer && !MessageRenderer.init()) {
                console.error('Failed to initialize MessageRenderer');
                return false;
            }
            
            // Initialize Status Indicator
            if (window.StatusIndicator && !StatusIndicator.init()) {
                console.error('Failed to initialize StatusIndicator');
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('Component initialization error:', error);
            return false;
        }
    },
    
    // Hide widget for unauthenticated users
    hideWidget() {
        if (this.elements.widget) {
            DisplayUtils.hide(this.elements.widget);
        }
    },
    
    // Get or create conversation
    async getOrCreateConversation() {
        if (this.isLoading) return;
        this.isLoading = true;
        
        try {
            console.log('🔄 Getting conversation...');
            
            const response = await this.apiCall('/chat/conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });
            
            if (response.success && response.conversation_id) {
                this.conversationId = response.conversation_id;
                console.log('✅ Conversation ready:', this.conversationId);
                
                // Update badge immediately
                await this.updateUnreadBadge();
                
                return true;
            } else {
                console.error('❌ Invalid conversation response:', response);
                return false;
            }
        } catch (error) {
            console.error('❌ Conversation error:', error);
            StatusIndicator.showOffline();
            return false;
        } finally {
            this.isLoading = false;
        }
    },
    
    // Send message
    async sendMessage(event) {
        if (event) {
            event.preventDefault();
        }
        
        const message = this.elements.messageInput?.value?.trim();
        if (!message || !this.conversationId || this.isLoading) {
            return false;
        }
        
        const startTime = performance.now();
        this.isLoading = true;
        
        // Disable input
        this.setInputState(false);
        
        // Show optimistic message
        const tempId = 'temp_' + Date.now();
        const optimisticMessage = {
            id: tempId,
            message: message,
            is_from_admin: false,
            formatted_time: 'Sending...',
            optimistic: true,
            created_at: new Date().toISOString()
        };
        
        this.addMessage(optimisticMessage);
        
        // Clear input immediately for better UX
        this.elements.messageInput.value = '';
        
        try {
            const response = await this.apiCall('/chat/send', {
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
            
            if (response.success && response.data) {
                // Remove optimistic message
                this.removeMessage(tempId);
                
                // Add real message
                const realMessage = {
                    id: response.data.id,
                    message: response.data.message,
                    sender_name: response.data.sender_name,
                    is_from_admin: response.data.is_from_admin,
                    formatted_time: response.data.formatted_time,
                    created_at: new Date().toISOString()
                };
                
                this.addMessage(realMessage);
                this.lastMessageId = Math.max(this.lastMessageId, response.data.id);
                
                // Update debug time
                this.updateDebugTime(performance.now() - startTime);
                
                console.log('✅ Message sent successfully');
                return true;
            } else {
                throw new Error('Invalid send response');
            }
        } catch (error) {
            console.error('❌ Send error:', error);
            
            // Remove optimistic message on error
            this.removeMessage(tempId);
            
            // Restore message in input for user to retry
            this.elements.messageInput.value = message;
            
            // Show error notification
            this.showErrorMessage('Failed to send message. Please try again.');
            
            StatusIndicator.showOffline();
            return false;
        } finally {
            this.isLoading = false;
            this.setInputState(true);
        }
    },
    
    // Load new messages
    async loadNewMessages() {
        if (!this.conversationId || this.isLoading) return;
        
        try {
            const afterId = this.hasLoadedInitial ? this.lastMessageId : 0;
            
            const response = await this.apiCall(
                `/chat/messages?conversation_id=${this.conversationId}&after_id=${afterId}`
            );
            
            if (response.success && response.messages && response.messages.length > 0) {
                console.log(`📨 Loading ${response.messages.length} messages (after_id: ${afterId})`);
                
                let hasNewMessages = false;
                
                response.messages.forEach(msg => {
                    if (!this.messages.has(msg.id)) {
                        this.addMessage(msg);
                        this.lastMessageId = Math.max(this.lastMessageId, msg.id);
                        hasNewMessages = true;
                    }
                });
                
                if (hasNewMessages) {
                    // Show notification if chat is closed
                    if (!this.isOpen) {
                        const adminMessages = response.messages.filter(m => m.is_from_admin);
                        if (adminMessages.length > 0) {
                            this.showNotification(adminMessages[adminMessages.length - 1]);
                        }
                    }
                    
                    // Update badge
                    await this.updateUnreadBadge();
                }
                
                this.hasLoadedInitial = true;
                this.updateDebugTime(response.debug_time || 0);
                
                // Update status
                StatusIndicator.showOnline();
            } else {
                console.log('📨 No new messages');
            }
        } catch (error) {
            console.error('❌ Load messages error:', error);
            StatusIndicator.showOffline();
        }
    },
    
    // Add message to display
    addMessage(message) {
        // Add to messages map
        this.messages.set(message.id, message);
        
        // Clean up old messages
        this.cleanupMessages();
        
        // Render message
        this.renderMessage(message);
        
        // Auto-scroll if needed
        this.autoScroll();
    },
    
    // Remove message
    removeMessage(messageId) {
        this.messages.delete(messageId);
        
        if (window.MessageRenderer) {
            MessageRenderer.remove(messageId);
        }
    },
    
    // Render single message
    renderMessage(message) {
        if (!window.MessageRenderer || !this.elements.messagesList) return;
        
        const messageElement = MessageRenderer.render(message);
        if (messageElement) {
            this.elements.messagesList.appendChild(messageElement);
            
            // Animate in
            requestAnimationFrame(() => {
                MessageRenderer.animateIn(messageElement);
            });
        }
    },
    
    // Clean up old messages
    cleanupMessages() {
        if (this.messages.size > this.config.maxMessages) {
            const sortedKeys = Array.from(this.messages.keys()).sort((a, b) => {
                if (typeof a === 'string' && a.startsWith('temp_')) return 1;
                if (typeof b === 'string' && b.startsWith('temp_')) return -1;
                return a - b;
            });
            
            const toRemove = sortedKeys.slice(0, this.messages.size - this.config.maxMessages);
            toRemove.forEach(key => {
                this.removeMessage(key);
            });
        }
    },
    
    // Auto-scroll to bottom
    autoScroll() {
        if (!this.elements.messagesContainer) return;
        
        const container = this.elements.messagesContainer;
        const shouldScroll = container.scrollTop + container.clientHeight >= container.scrollHeight - 50;
        
        if (shouldScroll || !this.hasLoadedInitial) {
            setTimeout(() => {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: this.hasLoadedInitial ? 'smooth' : 'auto'
                });
            }, 100);
        }
    },
    
    // Scroll to bottom manually
    scrollToBottom() {
        if (!this.elements.messagesContainer) return;
        
        this.elements.messagesContainer.scrollTo({
            top: this.elements.messagesContainer.scrollHeight,
            behavior: 'smooth'
        });
    },
    
    // Update unread badge (FIXED)
    async updateUnreadBadge() {
        if (!this.conversationId || !this.elements.unreadBadge) return;
        
        try {
            const response = await this.apiCall(
                `/chat/unread-count?conversation_id=${this.conversationId}`
            );
            
            const count = response.count || 0;
            
            if (count > 0 && !this.isOpen) {
                this.elements.unreadBadge.textContent = count > 99 ? '99+' : count;
                DisplayUtils.show(this.elements.unreadBadge, 'flex');
                this.elements.unreadBadge.classList.add('badge-show');
            } else {
                DisplayUtils.hide(this.elements.unreadBadge);
                this.elements.unreadBadge.classList.add('badge-hidden');
            }
        } catch (error) {
            console.error('❌ Unread count error:', error);
        }
    },
    
    // Toggle chat window (FIXED)
    async toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            await this.open();
        }
    },
    
    // Open chat window (FIXED)
    async open() {
        if (!this.elements.window) return;
        
        this.elements.window.classList.remove('window-hidden');
        this.elements.window.classList.add('show', 'window-show');
        DisplayUtils.show(this.elements.window, 'flex');
        this.isOpen = true;
        
        // Add class to body for mobile handling
        document.body.classList.add('chat-open');
        
        // Load initial messages if not loaded
        if (!this.hasLoadedInitial) {
            await this.loadNewMessages();
        }
        
        // Mark messages as read
        await this.markAsRead();
        
        // Update badge
        this.updateUnreadBadge();
        
        // Focus input
        setTimeout(() => {
            this.elements.messageInput?.focus();
        }, 300);
        
        console.log('💬 Chat opened');
    },
    
    // Close chat window (FIXED)
    close() {
        if (!this.elements.window) return;
        
        this.elements.window.classList.add('hide');
        this.isOpen = false;
        
        // Remove class from body
        document.body.classList.remove('chat-open');
        
        setTimeout(() => {
            DisplayUtils.hide(this.elements.window);
            this.elements.window.classList.add('window-hidden');
            this.elements.window.classList.remove('show', 'hide', 'window-show');
        }, 300);
        
        console.log('💬 Chat closed');
    },
    
    // Show/hide typing indicator (FIXED)
    showTyping() {
        if (this.elements.typingIndicator) {
            DisplayUtils.show(this.elements.typingIndicator, 'flex');
            this.elements.typingIndicator.classList.add('typing-show');
            this.elements.typingIndicator.classList.remove('typing-hidden');
        }
    },
    
    hideTyping() {
        if (this.elements.typingIndicator) {
            DisplayUtils.hide(this.elements.typingIndicator);
            this.elements.typingIndicator.classList.add('typing-hidden');
            this.elements.typingIndicator.classList.remove('typing-show');
        }
    },
    
    // Mark messages as read
    async markAsRead() {
        if (!this.conversationId) return;
        
        try {
            await this.apiCall('/chat/mark-read', {
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
    
    // Start polling for new messages
    startPolling() {
        this.stopPolling();
        
        this.polling = setInterval(() => {
            if (!document.hidden && this.conversationId) {
                this.loadNewMessages();
            }
        }, this.config.pollingInterval);
        
        console.log(`🔄 Polling started (${this.config.pollingInterval / 1000}s interval)`);
    },
    
    // Stop polling
    stopPolling() {
        if (this.polling) {
            clearInterval(this.polling);
            this.polling = null;
            console.log('⏹️ Polling stopped');
        }
    },
    
    // Setup event listeners
    setupEventListeners() {
        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopPolling();
            } else if (this.conversationId) {
                this.startPolling();
                // Immediate refresh when page becomes visible
                setTimeout(() => this.loadNewMessages(), 1000);
            }
        });
        
        // Before unload cleanup
        window.addEventListener('beforeunload', () => {
            this.stopPolling();
        });
        
        // Notification permission request
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    },
    
    // Show browser notification
    showNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification('New Message from Support', {
                body: message.message,
                icon: '/favicon.ico',
                tag: 'chat-message'
            });
            
            notification.onclick = () => {
                window.focus();
                this.open();
                notification.close();
            };
            
            setTimeout(() => notification.close(), 5000);
        }
    },
    
    // Show error message in chat
    showErrorMessage(text) {
        const errorMessage = {
            id: 'error_' + Date.now(),
            message: text,
            type: 'system',
            formatted_time: new Date().toLocaleTimeString(),
            created_at: new Date().toISOString()
        };
        
        this.addMessage(errorMessage);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            this.removeMessage(errorMessage.id);
        }, 5000);
    },
    
    // Set input state (enabled/disabled)
    setInputState(enabled) {
        if (this.elements.messageInput) {
            this.elements.messageInput.disabled = !enabled;
        }
        if (this.elements.sendBtn) {
            this.elements.sendBtn.disabled = !enabled;
        }
    },
    
    // Update debug time display
    updateDebugTime(ms) {
        if (!this.elements.debugTime) return;
        
        const roundedMs = Math.round(ms);
        this.elements.debugTime.textContent = roundedMs + 'ms';
        
        // Color coding
        this.elements.debugTime.className = 'text-xs px-2 py-1 rounded';
        if (roundedMs < 100) {
            this.elements.debugTime.classList.add('bg-green-600');
        } else if (roundedMs < 500) {
            this.elements.debugTime.classList.add('bg-yellow-600');
        } else {
            this.elements.debugTime.classList.add('bg-red-600');
        }
    },
    
    // Get CSRF token
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    },
    
    // Generic API call wrapper
    async apiCall(url, options = {}) {
        const defaultOptions = {
            credentials: 'same-origin',
            timeout: this.config.apiTimeout
        };
        
        const response = await fetch(url, { ...defaultOptions, ...options });
        
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON - possible authentication issue');
        }
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return await response.json();
    },
    
    // Escape HTML for security
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    ChatWidget.init();
});

// Expose global functions for backward compatibility
window.toggleChat = () => ChatWidget.toggle();
window.sendMessage = (event) => ChatWidget.sendMessage(event);
window.closeChat = () => ChatWidget.close();

// Console debugging helpers
window.chatDebug = {
    widget: ChatWidget,
    open: () => ChatWidget.open(),
    close: () => ChatWidget.close(),
    send: (msg) => {
        ChatWidget.elements.messageInput.value = msg;
        ChatWidget.sendMessage();
    },
    loadMessages: () => ChatWidget.loadNewMessages(),
    status: () => console.log(ChatWidget),
    clearMessages: () => {
        ChatWidget.messages.clear();
        if (ChatWidget.elements.messagesList) {
            ChatWidget.elements.messagesList.innerHTML = '';
        }
    }
};

console.log('💬 Chat Widget loaded. Type "chatDebug" for debugging helpers.');
</script>