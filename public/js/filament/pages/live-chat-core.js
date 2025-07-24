// Core Chat Handler - Main Entry Point
// File: js/filament/pages/live-chat-core.js

(() => {
    ("use strict");

    class LiveChatCore {
        constructor() {
            this.debug = true;
            this.version = "4.0.0";

            // Selectors
            this.selectors = {
                modal: '[style*="z-index: 9999"], .chat-modal-overlay',
                messages: "#messages-container",
                debugBox: '[style*="background: lime"], .debug-box',
                sendButton: '[wire\\:click="sendMessage"]',
                messageInput: '[wire\\:model*="message"]',
                conversation: '[wire\\:click*="selectConversation"]',
            };

            // Core state
            this.state = {
                isInitialized: false,
                currentConversationId: null,
                modalOpenTime: null,
                lastLivewireUpdate: null,
                debug: true,
            };

            // Initialize components
            this.modal = null;
            this.scroll = null;
            this.button = null;
            this.events = null;
            this.observers = null;
            this.api = null;

            this.init();
        }

        async init() {
            if (this.state.isInitialized) {
                this.log("⚠️ Already initialized, skipping...");
                return;
            }

            this.log(`🚀 LiveChat Core v${this.version} initializing...`);

            try {
                // Load and initialize all components
                await this.initializeComponents();
                this.setupGlobalAPI();

                this.state.isInitialized = true;
                this.log("✅ LiveChat Core initialized successfully");
            } catch (error) {
                console.error("❌ Failed to initialize LiveChat Core:", error);
            }
        }

        async initializeComponents() {
            // Initialize all component classes
            this.modal = new ChatModalManager(this);
            this.scroll = new ChatScrollManager(this);
            this.button = new ChatButtonManager(this);
            this.events = new ChatEventManager(this);
            this.observers = new ChatObserverManager(this);
            this.api = new ChatAPIManager(this);

            // Start core systems
            this.events.setupEventListeners();
            this.observers.setupObservers();
            this.modal.startModalWatcher();

            // Check initial state
            this.checkInitialState();
        }

        checkInitialState() {
            this.log("🔍 Checking initial page state");

            const conversations = document.querySelectorAll(
                this.selectors.conversation
            );
            this.log("📋 Conversations found:", conversations.length);

            if (this.modal.isOpen()) {
                this.log("🎭 Modal already open on page load");
                const modalElement = this.modal.getElement();
                this.modal.handleOpened(modalElement);
            }
        }

        setupGlobalAPI() {
            const api = this.api.getPublicAPI();

            // Expose to global scope
            window.LiveChatAPI = api;
            window.LCA = api; // Short alias
            window.liveChatCore = this; // Direct access for debugging

            // Enhanced console help
            console.log(`
🎮 LiveChat API v${this.version} - Modular Architecture:

📞 Conversation Management:
- LCA.test.openConversation(1)     // Open conversation by ID
- LCA.test.closeModal()            // Close modal
- LCA.test.resetState()            // Reset modal state

🔧 Troubleshooting:
- LCA.scroll.forceToBottom()       // Force scroll to bottom
- LCA.button.checkState()          // Check button state
- LCA.button.reset()               // Reset button state
- LCA.scroll.toggle()              // Toggle auto-scroll

ℹ️ Information:
- LCA.info.system()                // System status
- LCA.info.modal()                 // Modal information
- LCA.info.scroll()                // Scroll position
- LCA.info.state()                 // Current state

🐛 Debug & Performance:
- LCA.debug.enable()               // Enable debug logging
- LCA.debug.disable()              // Disable debug logging
- LCA.debug.reset()                // Reset all systems
- LCA.debug.performance()          // Performance metrics

Type LCA.info.system() to see current status!
            `);
        }

        // Utility methods
        log(message, data = null) {
            if (this.debug) {
                const timestamp = new Date().toLocaleTimeString();
                console.log(`[${timestamp}] 💬 Core: ${message}`, data || "");
            }
        }

        isLivewireActive() {
            if (window.Livewire && window.Livewire.components) {
                const components = window.Livewire.components.componentsById;
                return Object.values(components).some(
                    (component) =>
                        component.loading && component.loading.length > 0
                );
            }
            return false;
        }

        getElement(selector) {
            if (typeof selector === "string" && this.selectors[selector]) {
                return document.querySelector(this.selectors[selector]);
            }
            return document.querySelector(selector);
        }

        getAllElements(selector) {
            if (typeof selector === "string" && this.selectors[selector]) {
                return document.querySelectorAll(this.selectors[selector]);
            }
            return document.querySelectorAll(selector);
        }

        // Cleanup
        destroy() {
            if (this.modal) this.modal.destroy();
            if (this.scroll) this.scroll.destroy();
            if (this.button) this.button.destroy();
            if (this.events) this.events.destroy();
            if (this.observers) this.observers.destroy();

            this.state.isInitialized = false;
            this.log("🧹 LiveChat Core destroyed");
        }
    }

    // Fast Mode Configuration for Live Chat
    // Add this to live-chat-core.js

    class FastModeConfig {
        static enable() {
            // 1. Faster modal detection
            window.LIVE_CHAT_CONFIG = {
                modalWatcher: 100, // 100ms instead of 500ms
                initialScrolls: [50, 150, 300], // Faster scroll attempts
                newMessageScroll: 10, // Almost instant
                buttonMonitoring: 200, // More frequent button checks
                enableInstantScroll: true, // Use scrollTop instead of smooth scroll
                enablePreloading: true, // Preload conversation data
                enableCaching: true, // Cache DOM elements
            };

            // 2. Override core methods for speed
            if (window.liveChatHandler) {
                window.liveChatHandler.modal.fastMode = true;
                window.liveChatHandler.scroll.fastMode = true;

                console.log("⚡ FAST MODE ENABLED");
                console.log("📊 Expected improvements:");
                console.log("  - Modal open: ~300ms (was ~800ms)");
                console.log("  - Auto-scroll: ~100ms (was ~500ms)");
                console.log("  - Button response: ~200ms (was ~500ms)");
            }
        }

        static disable() {
            window.LIVE_CHAT_CONFIG = {
                modalWatcher: 500,
                initialScrolls: [200, 500, 800, 1200, 2000],
                newMessageScroll: 100,
                buttonMonitoring: 500,
                enableInstantScroll: false,
                enablePreloading: false,
                enableCaching: false,
            };

            if (window.liveChatHandler) {
                window.liveChatHandler.modal.fastMode = false;
                window.liveChatHandler.scroll.fastMode = false;

                console.log("🐢 NORMAL MODE ENABLED");
            }
        }

        static ultra() {
            window.LIVE_CHAT_CONFIG = {
                modalWatcher: 50, // Super fast detection
                initialScrolls: [10, 50, 100], // Ultra fast scrolls
                newMessageScroll: 5, // Almost instant
                buttonMonitoring: 100, // Very frequent checks
                enableInstantScroll: true,
                enablePreloading: true,
                enableCaching: true,
            };

            console.log("🚀 ULTRA FAST MODE ENABLED");
            console.log("⚠️ Warning: May consume more CPU");
        }
    }

    // Auto-enable fast mode
    FastModeConfig.enable();

    // Console commands for testing
    window.fastMode = FastModeConfig.enable;
    window.normalMode = FastModeConfig.disable;
    window.ultraMode = FastModeConfig.ultra;

    // Auto-initialization
    function initializeLiveChat() {
        try {
            if (window.LiveChatAPI) {
                console.log("💬 LiveChat already initialized, skipping...");
                return window.LiveChatAPI;
            }

            new LiveChatCore();
            return window.LiveChatAPI;
        } catch (error) {
            console.error("❌ Failed to initialize LiveChat:", error);
            return null;
        }
    }

    // Smart initialization
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeLiveChat);
    } else {
        if (window.requestIdleCallback) {
            requestIdleCallback(initializeLiveChat);
        } else {
            setTimeout(initializeLiveChat, 100);
        }
    }

    // Expose for manual initialization
    window.initializeLiveChat = initializeLiveChat;
})();
