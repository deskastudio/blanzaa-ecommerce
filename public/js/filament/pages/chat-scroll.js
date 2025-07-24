// Chat Scroll Manager
// File: js/filament/pages/chat-scroll.js

class ChatScrollManager {
    constructor(core) {
        this.core = core;

        this.state = {
            autoScrollEnabled: true,
            modalScrolled: false,
            lastScrollPosition: 0,
        };

        // Scroll scheduler
        this.scheduler = {
            timeouts: new Set(),
            schedule: (callback, delay = 300) => {
                const timeoutId = setTimeout(() => {
                    this.scheduler.timeouts.delete(timeoutId);
                    callback();
                }, delay);
                this.scheduler.timeouts.add(timeoutId);
                return timeoutId;
            },
            clear: () => {
                this.scheduler.timeouts.forEach((id) => clearTimeout(id));
                this.scheduler.timeouts.clear();
            },
        };
    }

    handleModalOpened() {
        this.core.log("📜 Setting up scroll for opened modal");
        this.resetState();
        this.scheduleInitialScrolls();
    }

    handleModalClosed() {
        this.core.log("📜 Cleaning up scroll for closed modal");
        this.scheduler.clear();
        this.resetState();
    }

    handleNewMessage() {
        this.core.log("📨 New message - scheduling scroll");

        if (this.core.modal.isOpen()) {
            // Force scroll for new messages
            this.scheduler.schedule(() => {
                this.forceToBottom();
            }, 100);

            // Backup scroll
            this.scheduler.schedule(() => {
                this.forceToBottom();
            }, 500);
        }
    }

    resetState() {
        this.state.modalScrolled = false;
        this.state.autoScrollEnabled = true;
        this.state.lastScrollPosition = 0;
        this.core.log("📜 Scroll state reset");
    }

    scheduleInitialScrolls() {
        this.core.log("📜 Scheduling initial scrolls...");

        const scrollDelays = [200, 500, 800, 1200, 2000];

        scrollDelays.forEach((delay, index) => {
            this.scheduler.schedule(() => {
                const container = this.getContainer();
                if (container && this.core.modal.isOpen()) {
                    this.core.log(
                        `📜 Initial scroll attempt ${index + 1} (${delay}ms)`
                    );
                    this.forceToBottom();

                    if (index === 0) {
                        this.state.modalScrolled = true;
                    }
                }
            }, delay);
        });
    }

    scheduleAutoScroll(delay = 200) {
        this.scheduler.schedule(() => {
            if (this.core.modal.isOpen()) {
                this.autoScrollToBottom();
            }
        }, delay);
    }

    autoScrollToBottom() {
        if (!this.state.autoScrollEnabled && this.state.modalScrolled) {
            this.core.log("📜 Auto-scroll disabled by user position");
            return;
        }
        this.smartScrollToBottom();
    }

    smartScrollToBottom() {
        const container = this.getContainer();
        if (!container) {
            this.core.log("❌ Messages container not found");
            return;
        }

        const behavior = this.state.modalScrolled ? "smooth" : "auto";

        container.scrollTo({
            top: container.scrollHeight,
            behavior: behavior,
        });

        this.core.log(`📜 Smart scrolled to bottom (${behavior})`);
        this.state.lastScrollPosition = container.scrollHeight;
    }

    forceToBottom() {
        const container = this.getContainer();
        if (!container) {
            this.core.log("❌ Messages container not found for force scroll");
            return;
        }

        // Force immediate scroll
        container.scrollTop = container.scrollHeight;

        // Then smooth scroll for UX
        setTimeout(() => {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: "smooth",
            });
        }, 50);

        this.core.log("📜 Force scrolled to bottom");
    }

    handleScroll(e) {
        const container = this.getContainer();
        if (!container || e.target !== container) return;

        const { scrollTop, scrollHeight, clientHeight } = container;
        const isNearBottom = scrollTop + clientHeight >= scrollHeight - 100;

        this.state.autoScrollEnabled = isNearBottom;
        this.state.lastScrollPosition = scrollTop;

        this.core.log(
            `📜 Scroll position: ${scrollTop}, auto-scroll: ${isNearBottom}`
        );
    }

    // Public methods
    getContainer() {
        return this.core.getElement("messages");
    }

    getInfo() {
        const container = this.getContainer();
        if (!container) return null;

        return {
            scrollTop: container.scrollTop,
            scrollHeight: container.scrollHeight,
            clientHeight: container.clientHeight,
            isAtBottom:
                container.scrollTop + container.clientHeight >=
                container.scrollHeight - 50,
            distanceFromBottom:
                container.scrollHeight -
                (container.scrollTop + container.clientHeight),
            autoScrollEnabled: this.state.autoScrollEnabled,
            pendingScrolls: this.scheduler.timeouts.size,
            modalScrolled: this.state.modalScrolled,
        };
    }

    toggle() {
        this.state.autoScrollEnabled = !this.state.autoScrollEnabled;
        this.core.log(
            `📜 Auto-scroll ${
                this.state.autoScrollEnabled ? "enabled" : "disabled"
            }`
        );
        return this.state.autoScrollEnabled;
    }

    destroy() {
        this.scheduler.clear();
        this.core.log("🧹 Scroll manager destroyed");
    }
}
