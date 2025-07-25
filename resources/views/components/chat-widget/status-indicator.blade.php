{{-- File: resources/views/components/chat-widget/status-indicator.blade.php --}}
{{-- Status Indicator Component --}}

<div id="status-indicator" class="fixed bottom-4 right-24 z-40">
    {{-- Online Status --}}
    <div id="status-online" class="hidden bg-green-500 text-white px-3 py-1 rounded-full text-xs shadow-lg animate-pulse">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-white rounded-full"></div>
            <span>Support Online</span>
        </div>
    </div>
    
    {{-- Offline Status --}}
    <div id="status-offline" class="hidden bg-gray-500 text-white px-3 py-1 rounded-full text-xs shadow-lg">
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-white rounded-full opacity-50"></div>
            <span>Support Offline</span>
        </div>
    </div>
    
    {{-- Typing Status --}}
    <div id="status-typing" class="hidden bg-blue-500 text-white px-3 py-1 rounded-full text-xs shadow-lg">
        <div class="flex items-center space-x-2">
            <div class="flex space-x-1">
                <div class="w-1 h-1 bg-white rounded-full animate-bounce"></div>
                <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-1 h-1 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
            <span>Support is typing...</span>
        </div>
    </div>
    
    {{-- Connection Status --}}
    <div id="status-connection" class="hidden bg-yellow-500 text-white px-3 py-1 rounded-full text-xs shadow-lg">
        <div class="flex items-center space-x-2">
            <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Reconnecting...</span>
        </div>
    </div>
</div>

<script>
// Status Indicator JavaScript
window.StatusIndicator = {
    currentStatus: 'offline',
    statusElements: {},
    hideTimeout: null,
    
    init() {
        this.statusElements = {
            online: document.getElementById('status-online'),
            offline: document.getElementById('status-offline'),
            typing: document.getElementById('status-typing'),
            connection: document.getElementById('status-connection')
        };
        
        // Check if elements exist
        for (const [key, element] of Object.entries(this.statusElements)) {
            if (!element) {
                console.warn(`Status element '${key}' not found`);
            }
        }
        
        // Set initial status
        this.setStatus('offline');
        
        // Monitor connection
        this.monitorConnection();
        
        console.log('📡 Status Indicator initialized');
        return true;
    },
    
    setStatus(status, options = {}) {
        const { duration = null, autoHide = false } = options;
        
        // Hide all status indicators first
        Object.values(this.statusElements).forEach(el => {
            if (el) el.classList.add('hidden');
        });
        
        // Clear any existing timeout
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
        
        // Show the requested status
        const targetElement = this.statusElements[status];
        if (targetElement) {
            targetElement.classList.remove('hidden');
            this.currentStatus = status;
            
            // Auto-hide after duration
            if (duration) {
                this.hideTimeout = setTimeout(() => {
                    this.hide();
                }, duration);
            }
            
            // Update header status text
            this.updateHeaderStatus(status);
            
            console.log(`📡 Status changed to: ${status}`);
        } else {
            console.warn(`📡 Unknown status: ${status}`);
        }
    },
    
    updateHeaderStatus(status) {
        const headerStatus = document.getElementById('support-status');
        if (headerStatus) {
            const statusTexts = {
                online: 'Online',
                offline: 'Offline',
                typing: 'Typing...',
                connection: 'Connecting...'
            };
            
            headerStatus.textContent = statusTexts[status] || 'Unknown';
            
            // Update header status color
            headerStatus.className = 'text-xs';
            switch (status) {
                case 'online':
                    headerStatus.classList.add('text-green-100');
                    break;
                case 'typing':
                    headerStatus.classList.add('text-blue-100');
                    break;
                case 'connection':
                    headerStatus.classList.add('text-yellow-100');
                    break;
                default:
                    headerStatus.classList.add('text-blue-100');
            }
        }
    },
    
    hide() {
        Object.values(this.statusElements).forEach(el => {
            if (el) el.classList.add('hidden');
        });
        
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
    },
    
    // Show online status
    showOnline() {
        this.setStatus('online');
    },
    
    // Show offline status
    showOffline() {
        this.setStatus('offline');
    },
    
    // Show typing indicator
    showTyping(duration = 5000) {
        this.setStatus('typing', { duration });
    },
    
    // Show connection status
    showConnecting() {
        this.setStatus('connection');
    },
    
    // Monitor network connection
    monitorConnection() {
        // Online/offline detection
        window.addEventListener('online', () => {
            console.log('📡 Network: Online');
            this.showOnline();
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                if (this.currentStatus === 'online') {
                    this.hide();
                }
            }, 3000);
        });
        
        window.addEventListener('offline', () => {
            console.log('📡 Network: Offline');
            this.showOffline();
        });
        
        // Page visibility
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                // Page became visible, check connection
                this.checkApiConnection();
            }
        });
    },
    
    // Check API connection
    async checkApiConnection() {
        try {
            this.showConnecting();
            
            const response = await fetch('/chat/unread-count?conversation_id=1', {
                method: 'GET',
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                this.showOnline();
                
                // Auto-hide after 2 seconds
                setTimeout(() => {
                    if (this.currentStatus === 'online') {
                        this.hide();
                    }
                }, 2000);
            } else {
                this.showOffline();
            }
        } catch (error) {
            console.error('📡 Connection check failed:', error);
            this.showOffline();
        }
    },
    
    // Simulate typing (for testing)
    simulateTyping() {
        this.showTyping(3000);
    },
    
    // Get current status
    getStatus() {
        return this.currentStatus;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    StatusIndicator.init();
    
    // Initial connection check after 1 second
    setTimeout(() => {
        StatusIndicator.checkApiConnection();
    }, 1000);
});

// Expose for console testing
window.testStatus = {
    online: () => StatusIndicator.showOnline(),
    offline: () => StatusIndicator.showOffline(),
    typing: () => StatusIndicator.showTyping(),
    connecting: () => StatusIndicator.showConnecting(),
    hide: () => StatusIndicator.hide(),
    check: () => StatusIndicator.checkApiConnection()
};
</script>

<style>
    /* Status indicator positioning */
    #status-indicator {
        pointer-events: none;
    }
    
    #status-indicator > div {
        pointer-events: auto;
        transition: all 0.3s ease-in-out;
    }
    
    /* Animation for status changes */
    #status-indicator > div:not(.hidden) {
        animation: statusSlideIn 0.3s ease-out;
    }
    
    @keyframes statusSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Typing animation */
    @keyframes bounce {
        0%, 80%, 100% {
            transform: scale(0);
        }
        40% {
            transform: scale(1);
        }
    }
    
    /* Mobile responsive */
    @media (max-width: 480px) {
        #status-indicator {
            right: 16px;
            bottom: 16px;
        }
        
        #status-indicator > div {
            font-size: 10px;
            padding: 6px 12px;
        }
    }
    
    /* Hide when chat is open on mobile */
    @media (max-width: 480px) {
        .chat-open #status-indicator {
            display: none;
        }
    }
</style>