// Chat API Manager
// File: js/filament/pages/chat-api.js

class ChatAPIManager {
    constructor(core) {
        this.core = core;
    }

    getPublicAPI() {
        return {
            // Test functions
            test: {
                openConversation: (id = 1) => {
                    const element = document.querySelector(
                        `[wire\\:click="selectConversation(${id})"]`
                    );
                    if (element) {
                        element.click();
                        this.core.log(`🎯 Triggered conversation ${id}`);
                        return true;
                    } else {
                        this.core.log(`❌ Conversation ${id} not found`);
                        return false;
                    }
                },

                closeModal: () => {
                    return this.core.modal.close();
                },

                resetState: () => {
                    this.core.scroll.resetState();
                    this.core.button.state.lastSendAttempt = null;
                    this.core.button.state.fixCount = 0;
                    return "✅ Reset all states";
                },

                simulateMessage: () => {
                    this.core.scroll.handleNewMessage();
                    return "✅ Simulated new message";
                },
            },

            // Scroll functions
            scroll: {
                forceToBottom: () => {
                    this.core.scroll.forceToBottom();
                    return "✅ Forced scroll to bottom";
                },

                scheduleScroll: (delay = 500) => {
                    this.core.scroll.scheduleAutoScroll(delay);
                    return `✅ Scheduled scroll in ${delay}ms`;
                },

                toggle: () => {
                    const enabled = this.core.scroll.toggle();
                    return `✅ Auto-scroll ${enabled ? "enabled" : "disabled"}`;
                },

                getInfo: () => {
                    return this.core.scroll.getInfo();
                },
            },

            // Button functions
            button: {
                checkState: () => {
                    const state = this.core.button.getState();
                    this.core.log("🔍 Button State Check:", state);
                    return state;
                },

                reset: () => {
                    const success = this.core.button.reset();
                    return success
                        ? "✅ Button state reset"
                        : "❌ Failed to reset button";
                },

                fix: () => {
                    const success = this.core.button.fix();
                    return success ? "✅ Button fixed" : "ℹ️ Button not stuck";
                },
            },

            // Modal functions
            modal: {
                getInfo: () => {
                    return this.core.modal.getInfo();
                },

                close: () => {
                    return this.core.modal.close();
                },

                isOpen: () => {
                    return this.core.modal.isOpen();
                },
            },

            // Information functions
            info: {
                system: () => {
                    const systemInfo = {
                        version: this.core.version,
                        livewire: typeof window.Livewire !== "undefined",
                        alpine: typeof window.Alpine !== "undefined",
                        modalOpen: this.core.modal.isOpen(),
                        conversationsCount:
                            this.core.getAllElements("conversation").length,
                        coreState: { ...this.core.state },
                        scrollState: { ...this.core.scroll.state },
                        buttonState: { ...this.core.button.state },
                        modalState: { ...this.core.modal.state },
                        observers: Array.from(
                            this.core.observers.observers.keys()
                        ),
                        performance: this.getPerformanceInfo(),
                    };

                    this.core.log("🔧 System Check:", systemInfo);
                    return systemInfo;
                },

                modal: () => {
                    return this.core.modal.getInfo();
                },

                scroll: () => {
                    return this.core.scroll.getInfo();
                },

                button: () => {
                    return this.core.button.getState();
                },

                state: () => {
                    return {
                        core: { ...this.core.state },
                        scroll: { ...this.core.scroll.state },
                        button: { ...this.core.button.state },
                        modal: { ...this.core.modal.state },
                    };
                },
            },

            // Debug functions
            debug: {
                enable: () => {
                    this.core.debug = true;
                    this.core.state.debug = true;
                    return "✅ Debug enabled";
                },

                disable: () => {
                    this.core.debug = false;
                    this.core.state.debug = false;
                    return "✅ Debug disabled";
                },

                reset: () => {
                    // Reset all systems
                    this.core.scroll.resetState();
                    this.core.button.state = {
                        lastSendAttempt: null,
                        fixCount: 0,
                    };
                    this.core.modal.state = {
                        isOpen: false,
                        openTime: null,
                        conversationId: null,
                        lastMessageCount: 0,
                    };
                    this.core.observers.stopObservingModal();
                    this.core.observers.setupObservers();
                    return "✅ All systems reset";
                },

                clearScrolls: () => {
                    this.core.scroll.scheduler.clear();
                    return "✅ Cleared all scheduled scrolls";
                },

                triggerModalCheck: () => {
                    this.core.modal.checkForChanges();
                    return "✅ Triggered modal check";
                },

                performance: () => {
                    return this.getPerformanceInfo();
                },

                getCore: () => {
                    return this.core;
                },
            },
        };
    }

    getPerformanceInfo() {
        if (!performance || !performance.memory) {
            return { available: false };
        }

        return {
            available: true,
            memory: {
                used:
                    Math.round(
                        performance.memory.usedJSHeapSize / 1024 / 1024
                    ) + " MB",
                total:
                    Math.round(
                        performance.memory.totalJSHeapSize / 1024 / 1024
                    ) + " MB",
                limit:
                    Math.round(
                        performance.memory.jsHeapSizeLimit / 1024 / 1024
                    ) + " MB",
            },
            timing: performance.timing
                ? {
                      loadComplete:
                          performance.timing.loadEventEnd -
                          performance.timing.navigationStart,
                      domReady:
                          performance.timing.domContentLoadedEventEnd -
                          performance.timing.navigationStart,
                  }
                : null,
        };
    }
}
