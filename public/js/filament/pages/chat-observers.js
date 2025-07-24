// Chat Observer Manager
// File: js/filament/pages/chat-observers.js

class ChatObserverManager {
    constructor(core) {
        this.core = core;
        this.observers = new Map();
    }

    setupObservers() {
        // Intersection Observer for scroll detection
        if ("IntersectionObserver" in window) {
            this.setupIntersectionObserver();
        }

        // Mutation Observer for DOM changes
        if ("MutationObserver" in window) {
            this.setupMutationObserver();
        }

        this.core.log("👁️ Observers setup complete");
    }

    setupIntersectionObserver() {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.core.scroll.state.autoScrollEnabled = true;
                        this.core.log(
                            "👁️ User is at bottom - auto-scroll enabled"
                        );
                    } else {
                        this.core.scroll.state.autoScrollEnabled = false;
                        this.core.log(
                            "👁️ User scrolled up - auto-scroll disabled"
                        );
                    }
                });
            },
            { threshold: 0.8 }
        );

        this.observers.set("intersection", observer);
    }

    setupMutationObserver() {
        const observer = new MutationObserver(
            this.throttle((mutations) => {
                let shouldUpdate = false;

                mutations.forEach((mutation) => {
                    if (
                        mutation.type === "childList" &&
                        mutation.addedNodes.length > 0
                    ) {
                        // Check if new messages were added
                        const hasNewMessage = Array.from(
                            mutation.addedNodes
                        ).some(
                            (node) =>
                                node.nodeType === Node.ELEMENT_NODE &&
                                (node.classList?.contains("message-wrapper") ||
                                    node.getAttribute?.("data-message-id") ||
                                    node.style?.marginBottom === "16px")
                        );

                        if (hasNewMessage) {
                            shouldUpdate = true;
                            this.core.log(
                                "🔍 MutationObserver detected new message"
                            );
                        }
                    }
                });

                if (shouldUpdate) {
                    this.core.scroll.handleNewMessage();
                }
            }, 100)
        );

        this.observers.set("mutation", observer);
    }

    startObservingModal(modal) {
        if (!modal) return;

        const mutationObserver = this.observers.get("mutation");
        if (mutationObserver) {
            mutationObserver.observe(modal, {
                childList: true,
                subtree: true,
            });
            this.core.log("👁️ Started observing modal for changes");
        }

        // Also observe the messages container specifically
        const messagesContainer = this.core.scroll.getContainer();
        if (messagesContainer) {
            this.createScrollSentinel(messagesContainer);
        }
    }

    createScrollSentinel(container) {
        // Remove existing sentinel
        const existingSentinel = container.querySelector(".scroll-sentinel");
        if (existingSentinel) {
            existingSentinel.remove();
        }

        // Create new sentinel at the bottom
        const sentinel = document.createElement("div");
        sentinel.className = "scroll-sentinel";
        sentinel.style.cssText =
            "height: 1px; width: 100%; position: absolute; bottom: 0; pointer-events: none;";

        container.style.position = "relative";
        container.appendChild(sentinel);

        const intersectionObserver = this.observers.get("intersection");
        if (intersectionObserver) {
            intersectionObserver.observe(sentinel);
        }

        this.core.log("🎯 Scroll sentinel created");
    }

    stopObservingModal() {
        this.observers.forEach((observer) => {
            if (observer.disconnect) {
                observer.disconnect();
            }
        });
        // Recreate observers for next use
        this.setupObservers();
        this.core.log("👁️ Stopped observing modal");
    }

    updateObservers() {
        // Update intersection observer targets
        const messagesContainer = this.core.scroll.getContainer();
        if (messagesContainer) {
            this.createScrollSentinel(messagesContainer);
        }
    }

    // Utility method
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

    destroy() {
        this.stopObservingModal();
        this.observers.clear();
        this.core.log("🧹 Observer manager destroyed");
    }
}
