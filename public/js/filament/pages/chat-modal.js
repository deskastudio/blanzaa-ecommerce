// Chat Modal Manager
// File: js/filament/pages/chat-modal.js

class ChatModalManager {
    constructor(core) {
        this.core = core;
        this.modalWatcher = null;

        this.state = {
            isOpen: false,
            openTime: null,
            conversationId: null,
            lastMessageCount: 0,
        };
    }

    startModalWatcher() {
        this.modalWatcher = setInterval(() => {
            this.checkForChanges();
        }, 500);
        this.core.log("👁️ Modal watcher started");
    }

    checkForChanges() {
        const modal = this.getElement();
        const wasOpen = this.state.isOpen;
        const isOpen = !!modal;

        if (!wasOpen && isOpen) {
            this.handleOpened(modal);
        } else if (wasOpen && !isOpen) {
            this.handleClosed();
        } else if (isOpen && modal) {
            this.handleUpdate(modal);
        }
    }

    handleOpened(modal) {
        this.core.log("🎭 Modal opened detected!");

        this.state.isOpen = true;
        this.state.openTime = Date.now();
        this.state.conversationId = this.extractConversationId(modal);

        this.core.log("📞 Modal conversation ID:", this.state.conversationId);

        // Notify other components
        this.core.scroll.handleModalOpened();
        this.core.observers.startObservingModal(modal);

        // Update core state
        this.core.state.currentConversationId = this.state.conversationId;
        this.core.state.modalOpenTime = this.state.openTime;
    }

    handleClosed() {
        this.core.log("🚪 Modal closed detected!");

        this.state.isOpen = false;
        this.state.openTime = null;
        this.state.conversationId = null;
        this.state.lastMessageCount = 0;

        // Notify other components
        this.core.scroll.handleModalClosed();
        this.core.observers.stopObservingModal();

        // Update core state
        this.core.state.currentConversationId = null;
        this.core.state.modalOpenTime = null;
    }

    handleUpdate(modal) {
        const currentMessageCount = this.getMessageCount(modal);

        if (
            currentMessageCount > this.state.lastMessageCount &&
            this.state.lastMessageCount > 0
        ) {
            this.core.log("📨 New message detected in modal update!");
            this.core.scroll.handleNewMessage();
        }

        this.state.lastMessageCount = currentMessageCount;
    }

    extractConversationId(modal) {
        // Try multiple methods to get conversation ID
        const dataAttr = modal.getAttribute("data-conversation-id");
        if (dataAttr) return dataAttr;

        // Try to extract from debug box
        const debugBox = modal.querySelector(this.core.selectors.debugBox);
        if (debugBox) {
            const match = debugBox.textContent.match(/Conv ID:\s*(\d+)/);
            if (match) return match[1];
        }

        // Try to extract from URL
        const url = window.location.href;
        const urlMatch = url.match(/conversation[=\/](\d+)/);
        if (urlMatch) return urlMatch[1];

        return null;
    }

    getMessageCount(modal) {
        const debugBox = modal.querySelector(this.core.selectors.debugBox);
        if (debugBox) {
            const match = debugBox.textContent.match(/(\d+)\s*msgs/);
            if (match) return parseInt(match[1]);
        }

        // Fallback: count message elements
        const messageElements = modal.querySelectorAll(
            "[data-message-id], .message-wrapper"
        );
        return messageElements.length;
    }

    // Public methods
    isOpen() {
        return !!this.getElement();
    }

    getElement() {
        return this.core.getElement("modal");
    }

    close() {
        const closeBtn = document.querySelector(
            '[wire\\:click*="closeModal"], [data-modal-close]'
        );
        if (closeBtn) {
            closeBtn.click();
            this.core.log("🚪 Modal closed via API");
            return true;
        }
        return false;
    }

    getInfo() {
        const modal = this.getElement();
        if (!modal) return null;

        return {
            isOpen: true,
            conversationId: this.state.conversationId,
            openTime: this.state.openTime,
            timeSinceOpen: this.state.openTime
                ? Date.now() - this.state.openTime
                : null,
            messageCount: this.getMessageCount(modal),
            hasDebugBox: !!modal.querySelector(this.core.selectors.debugBox),
            hasEmptyState: !!modal.querySelector(
                '[style*="background: #ffcccc"], .empty-messages'
            ),
        };
    }

    destroy() {
        if (this.modalWatcher) {
            clearInterval(this.modalWatcher);
            this.modalWatcher = null;
        }
        this.core.log("🧹 Modal manager destroyed");
    }
}
