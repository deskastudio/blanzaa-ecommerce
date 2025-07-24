// Chat Event Manager
// File: js/filament/pages/chat-events.js

class ChatEventManager {
    constructor(core) {
        this.core = core;
        this.throttledHandleLivewireUpdate = this.throttle(
            this.handleLivewireUpdate.bind(this),
            150
        );
        this.debouncedScrollHandler = this.debounce(
            this.handleScroll.bind(this),
            100
        );
    }

    setupEventListeners() {
        const eventOptions = { passive: true };

        // Livewire events
        document.addEventListener(
            "livewire:load",
            () => {
                this.core.log("Livewire loaded");
                this.setupLivewireHooks();
            },
            eventOptions
        );

        document.addEventListener(
            "livewire:updated",
            () => {
                this.core.state.lastLivewireUpdate = Date.now();
                this.throttledHandleLivewireUpdate();
            },
            eventOptions
        );

        // Click events
        document.addEventListener("click", this.handleClicks.bind(this));

        // Keyboard events
        document.addEventListener("keydown", this.handleKeyboard.bind(this));

        // Scroll events
        document.addEventListener(
            "scroll",
            this.debouncedScrollHandler,
            eventOptions
        );

        this.core.log("🎧 Event listeners setup complete");
    }

    setupLivewireHooks() {
        if (window.Livewire) {
            window.Livewire.hook("message.processed", () => {
                this.throttledHandleLivewireUpdate();
            });

            window.Livewire.hook("component.initialized", () => {
                this.core.log("🔌 Livewire component initialized");
            });
        }
    }

    handleClicks(e) {
        // Conversation selection
        const conversationElement = e.target.closest(
            this.core.selectors.conversation
        );
        if (conversationElement) {
            const wireClick = conversationElement.getAttribute("wire:click");
            const conversationId = wireClick.match(/\d+/)?.[0];
            this.handleConversationSelect(conversationId);
            return;
        }

        // Send button
        if (e.target.closest(this.core.selectors.sendButton)) {
            this.core.button.handleSendClick();
            return;
        }

        // Modal close button
        if (
            e.target.closest('[wire\\:click*="closeModal"]') ||
            e.target.closest("[data-modal-close]")
        ) {
            this.handleModalClose();
            return;
        }
    }

    handleKeyboard(e) {
        if (e.key === "Escape" && this.core.modal.isOpen()) {
            this.handleModalClose();
        }

        // Keyboard shortcuts
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case "Enter":
                    if (this.core.modal.isOpen()) {
                        const sendBtn = this.core.button.getElement();
                        if (sendBtn && !sendBtn.disabled) {
                            sendBtn.click();
                            e.preventDefault();
                        }
                    }
                    break;
            }
        }
    }

    handleScroll(e) {
        this.core.scroll.handleScroll(e);
    }

    handleConversationSelect(conversationId) {
        this.core.log("📞 Conversation selected:", conversationId);

        // Clear any pending scrolls
        this.core.scroll.scheduler.clear();

        // Reset states
        this.core.scroll.resetState();

        // Update state
        this.core.state.currentConversationId = conversationId;

        // Schedule modal opening detection
        this.core.scroll.scheduler.schedule(() => {
            const modal = this.core.modal.getElement();
            if (modal) {
                this.core.modal.handleOpened(modal);
            }
        }, 200);
    }

    handleModalClose() {
        this.core.log("🚪 Modal closing via button");
        this.core.modal.handleClosed();
    }

    handleLivewireUpdate() {
        requestAnimationFrame(() => {
            this.processModalUpdate();
        });
    }

    processModalUpdate() {
        if (!this.core.modal.isOpen()) return;

        try {
            // Only fix button if not actively processing
            if (!this.core.isLivewireActive()) {
                this.core.button.fix();
            }

            this.highlightUnreadMessages();
            this.core.observers.updateObservers();

            // Schedule auto-scroll
            this.core.scroll.scheduleAutoScroll(200);
        } catch (error) {
            this.core.log("❌ Error in processModalUpdate:", error);
        }
    }

    highlightUnreadMessages() {
        const unreadBadges = document.querySelectorAll(
            '[style*="background: #ef4444"], .unread-badge'
        );
        unreadBadges.forEach((badge) => {
            if (badge.textContent.trim() !== "0") {
                badge.style.animation = "pulse 2s infinite";
            }
        });
    }

    // Utility methods
    throttle(func, limit) {
        let inThrottle;
        return function (...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => (inThrottle = false), limit);
            }
        };
    }

    debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    destroy() {
        this.core.log("🧹 Event manager destroyed");
    }
}
