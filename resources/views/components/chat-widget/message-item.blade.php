{{-- File: resources/views/components/chat-widget/message-item.blade.php --}}
{{-- Single Message Item Template --}}

{{-- This template will be used by JavaScript to render messages --}}
<template id="message-template">
    <div class="message-wrapper mb-3" data-message-id="">
        <!-- User Message Template -->
        <div class="user-message flex justify-end">
            <div class="max-w-xs">
                <div class="bg-blue-500 text-white px-4 py-2 rounded-lg rounded-br-sm shadow-sm">
                    <p class="text-sm message-text"></p>
                    <div class="flex justify-between items-center mt-1 text-xs text-blue-100">
                        <span class="message-time"></span>
                        <span class="message-status">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Admin Message Template -->
        <div class="admin-message flex justify-start">
            <div class="max-w-xs">
                <!-- Admin Avatar -->
                <div class="flex items-start space-x-2">
                    <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg rounded-bl-sm shadow-sm">
                            <div class="text-xs text-gray-500 mb-1 sender-name"></div>
                            <p class="text-sm text-gray-800 message-text"></p>
                            <div class="text-xs text-gray-500 mt-1 message-time"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Message Template -->
        <div class="system-message flex justify-center">
            <div class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs message-text"></div>
        </div>
    </div>
</template>

<script>
// Message Rendering JavaScript
window.MessageRenderer = {
    template: null,
    
    init() {
        this.template = document.getElementById('message-template');
        if (!this.template) {
            console.error('Message template not found');
            return false;
        }
        return true;
    },
    
    render(message) {
        if (!this.template) {
            console.error('Template not initialized');
            return null;
        }
        
        // Clone template
        const clone = this.template.content.cloneNode(true);
        const wrapper = clone.querySelector('.message-wrapper');
        
        // Set message ID
        wrapper.setAttribute('data-message-id', message.id);
        
        // Determine message type and show appropriate template
        let activeTemplate;
        if (message.type === 'system') {
            activeTemplate = clone.querySelector('.system-message');
            clone.querySelector('.user-message').remove();
            clone.querySelector('.admin-message').remove();
        } else if (message.is_from_admin) {
            activeTemplate = clone.querySelector('.admin-message');
            clone.querySelector('.user-message').remove();
            clone.querySelector('.system-message').remove();
            
            // Set sender name
            const senderName = activeTemplate.querySelector('.sender-name');
            if (senderName) {
                senderName.textContent = message.sender_name || 'Support';
            }
        } else {
            activeTemplate = clone.querySelector('.user-message');
            clone.querySelector('.admin-message').remove();
            clone.querySelector('.system-message').remove();
        }
        
        // Fill in message content
        const messageText = activeTemplate.querySelector('.message-text');
        if (messageText) {
            messageText.textContent = message.message;
        }
        
        // Fill in time
        const messageTime = activeTemplate.querySelector('.message-time');
        if (messageTime) {
            messageTime.textContent = message.formatted_time || this.formatTime(message.created_at);
        }
        
        // Handle optimistic messages (sending state)
        if (message.optimistic) {
            wrapper.classList.add('sending');
            const messageStatus = activeTemplate.querySelector('.message-status');
            if (messageStatus) {
                messageStatus.innerHTML = `
                    <svg class="w-3 h-3 inline animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
            }
        }
        
        // Add entrance animation
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(10px)';
        
        return wrapper;
    },
    
    formatTime(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        // If today, show time
        if (diff < 24 * 60 * 60 * 1000) {
            return date.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        }
        
        // If this week, show day
        if (diff < 7 * 24 * 60 * 60 * 1000) {
            return date.toLocaleDateString('id-ID', { weekday: 'short' });
        }
        
        // Otherwise show date
        return date.toLocaleDateString('id-ID', { 
            day: 'numeric', 
            month: 'short' 
        });
    },
    
    // Animation helper
    animateIn(element) {
        requestAnimationFrame(() => {
            element.style.transition = 'all 0.3s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        });
    },
    
    // Remove message
    remove(messageId) {
        const element = document.querySelector(`[data-message-id="${messageId}"]`);
        if (element) {
            element.style.transition = 'all 0.3s ease-out';
            element.style.opacity = '0';
            element.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                element.remove();
            }, 300);
        }
    },
    
    // Update message (for optimistic -> real)
    update(messageId, newData) {
        const element = document.querySelector(`[data-message-id="${messageId}"]`);
        if (element) {
            // Update ID
            element.setAttribute('data-message-id', newData.id);
            
            // Remove sending state
            element.classList.remove('sending');
            
            // Update time
            const timeElement = element.querySelector('.message-time');
            if (timeElement) {
                timeElement.textContent = newData.formatted_time || this.formatTime(newData.created_at);
            }
            
            // Update status icon
            const statusElement = element.querySelector('.message-status');
            if (statusElement) {
                statusElement.innerHTML = `
                    <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                `;
            }
        }
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    MessageRenderer.init();
});
</script>

<style>
    /* Message animations */
    .message-wrapper {
        transition: all 0.3s ease-out;
    }
    
    .message-wrapper.sending {
        opacity: 0.7;
    }
    
    /* Message bubble hover effects */
    .user-message .bg-blue-500:hover {
        background-color: #2563eb;
    }
    
    .admin-message .bg-white:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    /* System message styling */
    .system-message {
        margin: 8px 0;
    }
    
    /* Responsive adjustments */
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
    }
</style>