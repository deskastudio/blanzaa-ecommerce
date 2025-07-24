// Live Chat Page Handler - Fixed Auto-scroll Version v3.1
(() => {
    "use strict";

    class LiveChatHandler {
        constructor() {
            this.debug = true;
            this.modalSelector = '[style*="z-index: 9999"]';
            this.messagesSelector = "#messages-container";
            this.debugBoxSelector = '[style*="background: lime"]';

            // Enhanced state management
            this.state = {
                modalScrolled: false,
                lastMessageCount: 0,
                currentConversationId: null,
                isInitialized: false,
                lastScrollPosition: 0,
                autoScrollEnabled: true,
                modalOpenTime: null,
                lastLivewireUpdate: null,
            };

            // Performance optimization - throttled functions
            this.throttledHandleLivewireUpdate = this.throttle(
                this.handleLivewireUpdate.bind(this),
                150
            );
            this.debouncedScrollHandler = this.debounce(
                this.handleScroll.bind(this),
                100
            );

            // Observer for better DOM monitoring
            this.observers = new Map();

            // Auto-scroll scheduler
            this.scrollScheduler = {
                timeouts: new Set(),
                schedule: (callback, delay = 300) => {
                    const timeoutId = setTimeout(() => {
                        this.scrollScheduler.timeouts.delete(timeoutId);
                        callback();
                    }, delay);
                    this.scrollScheduler.timeouts.add(timeoutId);
                    return timeoutId;
                },
                clear: () => {
                    this.scrollScheduler.timeouts.forEach((id) =>
                        clearTimeout(id)
                    );
                    this.scrollScheduler.timeouts.clear();
                },
            };

            this.init();
        }

        init() {
            if (this.state.isInitialized) {
                this.log("⚠️ Already initialized, skipping...");
                return;
            }

            this.log(
                "🚀 LiveChat handler initialized (Fixed Auto-scroll v3.1)"
            );
            this.setupEventListeners();
            this.setupObservers();
            this.checkInitialState();
            this.startModalWatcher();

            this.state.isInitialized = true;
        }

        // Utility functions for performance
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

        log(message, data = null) {
            if (this.debug) {
                const timestamp = new Date().toLocaleTimeString();
                console.log(
                    `[${timestamp}] 💬 LiveChat: ${message}`,
                    data || ""
                );
            }
        }

        // Enhanced modal watching system
        startModalWatcher() {
            this.modalWatcher = setInterval(() => {
                this.checkForModalChanges();
            }, 500); // Check every 500ms
        }

        checkForModalChanges() {
            const modal = this.getModal();
            const wasOpen = this.state.currentConversationId !== null;
            const isOpen = !!modal;

            if (!wasOpen && isOpen) {
                // Modal just opened
                this.handleModalOpened(modal);
            } else if (wasOpen && !isOpen) {
                // Modal just closed
                this.handleModalClosed();
            } else if (isOpen && modal) {
                // Modal is open, check for updates
                this.handleModalUpdate(modal);
            }
        }

        handleModalOpened(modal) {
            this.log("🎭 Modal opened detected!");
            this.state.modalOpenTime = Date.now();

            // Extract conversation ID from modal
            const conversationId = this.extractConversationId(modal);
            if (conversationId) {
                this.state.currentConversationId = conversationId;
                this.log("📞 Modal conversation ID:", conversationId);
            }

            // Reset state for new modal
            this.resetModalState();

            // Start observing immediately
            this.startObservingModal(modal);

            // Schedule multiple scroll attempts with increasing delays
            this.scheduleInitialScrolls();
        }

        scheduleInitialScrolls() {
            this.log("📜 Scheduling initial scrolls...");

            // Multiple scroll attempts with different timings
            const scrollDelays = [200, 500, 800, 1200, 2000];

            scrollDelays.forEach((delay, index) => {
                this.scrollScheduler.schedule(() => {
                    const container = this.getMessagesContainer();
                    if (container && this.isModalOpen()) {
                        this.log(
                            `📜 Initial scroll attempt ${
                                index + 1
                            } (${delay}ms)`
                        );
                        this.forceScrollToBottom();

                        // Mark as scrolled after first successful scroll
                        if (index === 0) {
                            this.state.modalScrolled = true;
                        }
                    }
                }, delay);
            });
        }

        handleModalClosed() {
            this.log("🚪 Modal closed detected!");
            this.stopObservingModal();
            this.resetModalState();
            this.scrollScheduler.clear();
            this.state.currentConversationId = null;
            this.state.modalOpenTime = null;
        }

        handleModalUpdate(modal) {
            // Check for new messages
            const currentMessageCount = this.getMessageCount(modal);

            if (
                currentMessageCount > this.state.lastMessageCount &&
                this.state.lastMessageCount > 0
            ) {
                this.log("📨 New message detected in modal update!");
                this.handleNewMessage();
            }

            this.state.lastMessageCount = currentMessageCount;
        }

        extractConversationId(modal) {
            // Try multiple methods to get conversation ID
            const dataAttr = modal.getAttribute("data-conversation-id");
            if (dataAttr) return dataAttr;

            // Try to extract from debug box
            const debugBox = modal.querySelector(this.debugBoxSelector);
            if (debugBox) {
                const match = debugBox.textContent.match(/Conv ID:\s*(\d+)/);
                if (match) return match[1];
            }

            // Try to extract from URL or other sources
            const url = window.location.href;
            const urlMatch = url.match(/conversation[=\/](\d+)/);
            if (urlMatch) return urlMatch[1];

            return null;
        }

        getMessageCount(modal) {
            const debugBox = modal.querySelector(this.debugBoxSelector);
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

        setupEventListeners() {
            // Use passive listeners for better performance
            const eventOptions = { passive: true };

            // Livewire events with error handling
            document.addEventListener(
                "livewire:load",
                () => {
                    this.log("Livewire loaded");
                    this.setupLivewireHooks();
                },
                eventOptions
            );

            document.addEventListener(
                "livewire:updated",
                () => {
                    this.state.lastLivewireUpdate = Date.now();
                    this.throttledHandleLivewireUpdate();
                },
                eventOptions
            );

            // Optimized click handler with event delegation
            document.addEventListener("click", this.handleClicks.bind(this));

            // Enhanced keyboard handling
            document.addEventListener(
                "keydown",
                this.handleKeyboard.bind(this)
            );

            // Scroll tracking for auto-scroll behavior
            document.addEventListener(
                "scroll",
                this.debouncedScrollHandler,
                eventOptions
            );
        }

        setupObservers() {
            // Intersection Observer for better scroll detection
            if ("IntersectionObserver" in window) {
                this.setupIntersectionObserver();
            }

            // Mutation Observer for DOM changes
            if ("MutationObserver" in window) {
                this.setupMutationObserver();
            }
        }

        setupIntersectionObserver() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            this.state.autoScrollEnabled = true;
                            this.log(
                                "👁️ User is at bottom - auto-scroll enabled"
                            );
                        } else {
                            this.state.autoScrollEnabled = false;
                            this.log(
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
                                    (node.classList?.contains(
                                        "message-wrapper"
                                    ) ||
                                        node.getAttribute?.(
                                            "data-message-id"
                                        ) ||
                                        node.style?.marginBottom === "16px")
                            );

                            if (hasNewMessage) {
                                shouldUpdate = true;
                                this.log(
                                    "🔍 MutationObserver detected new message"
                                );
                            }
                        }
                    });

                    if (shouldUpdate) {
                        this.handleNewMessage();
                    }
                }, 100)
            );

            this.observers.set("mutation", observer);
        }

        setupLivewireHooks() {
            // Enhanced Livewire integration
            if (window.Livewire) {
                window.Livewire.hook("message.processed", () => {
                    this.throttledHandleLivewireUpdate();
                });

                window.Livewire.hook("component.initialized", () => {
                    this.log("🔌 Livewire component initialized");
                });
            }
        }

        handleClicks(e) {
            // Conversation selection
            const conversationElement = e.target.closest(
                '[wire\\:click*="selectConversation"]'
            );
            if (conversationElement) {
                const wireClick =
                    conversationElement.getAttribute("wire:click");
                const conversationId = wireClick.match(/\d+/)?.[0];
                this.handleConversationSelect(conversationId);
                return;
            }

            // Send button
            if (e.target.closest('[wire\\:click="sendMessage"]')) {
                this.handleSendMessage();
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
            if (e.key === "Escape" && this.isModalOpen()) {
                this.handleModalClose();
            }

            // Enhanced keyboard shortcuts
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case "Enter":
                        if (this.isModalOpen()) {
                            const sendBtn = document.querySelector(
                                '[wire\\:click="sendMessage"]'
                            );
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
            const container = this.getMessagesContainer();
            if (!container || e.target !== container) return;

            const { scrollTop, scrollHeight, clientHeight } = container;
            const isNearBottom = scrollTop + clientHeight >= scrollHeight - 100;

            this.state.autoScrollEnabled = isNearBottom;
            this.state.lastScrollPosition = scrollTop;

            this.log(
                `📜 Scroll position: ${scrollTop}, auto-scroll: ${isNearBottom}`
            );
        }

        handleConversationSelect(conversationId) {
            this.log("📞 Conversation selected:", conversationId);
            this.scrollScheduler.clear(); // Clear any pending scrolls
            this.resetModalState();
            this.state.currentConversationId = conversationId;

            // Schedule modal opening detection
            this.scrollScheduler.schedule(() => {
                const modal = this.getModal();
                if (modal) {
                    this.handleModalOpened(modal);
                }
            }, 200);
        }

        handleSendMessage() {
            this.log("📤 Send button clicked");
            // Pre-scroll for immediate feedback
            this.scheduleAutoScroll(800);
        }

        handleModalClose() {
            this.log("🚪 Modal closing via button");
            this.handleModalClosed();
        }

        resetModalState() {
            this.state.modalScrolled = false;
            this.state.lastMessageCount = 0;
            this.state.autoScrollEnabled = true;
            this.state.lastScrollPosition = 0;
            this.log("🔄 Modal state reset");
        }

        startObservingModal(modal) {
            if (!modal) return;

            const mutationObserver = this.observers.get("mutation");
            if (mutationObserver) {
                mutationObserver.observe(modal, {
                    childList: true,
                    subtree: true,
                });
                this.log("👁️ Started observing modal for changes");
            }

            // Also observe the messages container specifically
            const messagesContainer = this.getMessagesContainer();
            if (messagesContainer) {
                const intersectionObserver = this.observers.get("intersection");
                if (intersectionObserver) {
                    // Create a sentinel element at the bottom
                    this.createScrollSentinel(messagesContainer);
                }
            }
        }

        createScrollSentinel(container) {
            // Remove existing sentinel
            const existingSentinel =
                container.querySelector(".scroll-sentinel");
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

            this.log("🎯 Scroll sentinel created");
        }

        stopObservingModal() {
            this.observers.forEach((observer) => {
                if (observer.disconnect) {
                    observer.disconnect();
                }
            });
            // Recreate observers for next use
            this.setupObservers();
            this.log("👁️ Stopped observing modal");
        }

        handleLivewireUpdate() {
            // Use requestAnimationFrame for better performance
            requestAnimationFrame(() => {
                this.processModalUpdate();
            });
        }

        handleNewMessage() {
            this.log("📨 New message detected - processing...");

            // Always scroll for new messages if modal is open
            if (this.isModalOpen()) {
                // Force scroll regardless of auto-scroll setting for new messages
                this.scrollScheduler.schedule(() => {
                    this.forceScrollToBottom();
                }, 100);

                // Also schedule a backup scroll
                this.scrollScheduler.schedule(() => {
                    this.forceScrollToBottom();
                }, 500);
            }
        }

        processModalUpdate() {
            if (!this.isModalOpen()) return;

            try {
                this.debugModalState();
                this.fixStuckButton();
                this.highlightUnreadMessages();
                this.updateObservers();

                // Always try to scroll after updates
                this.scheduleAutoScroll(200);
            } catch (error) {
                this.log("❌ Error in processModalUpdate:", error);
            }
        }

        updateObservers() {
            // Update intersection observer targets
            const messagesContainer = this.getMessagesContainer();
            if (messagesContainer) {
                this.createScrollSentinel(messagesContainer);
            }
        }

        checkInitialState() {
            this.log("🔍 Checking initial page state");

            const conversations = document.querySelectorAll(
                '[wire\\:click*="selectConversation"]'
            );
            this.log("📋 Conversations found:", conversations.length);

            if (this.isModalOpen()) {
                this.log("🎭 Modal already open on page load");
                const modal = this.getModal();
                this.handleModalOpened(modal);
            }
        }

        debugModalState() {
            const modal = this.getModal();
            if (!modal) return;

            this.log("🎭 Modal is open - debugging...");

            const debugBox = modal.querySelector(this.debugBoxSelector);
            if (debugBox) {
                this.parseDebugInfo(debugBox);
            }

            this.analyzeMessages(modal);
            this.checkEmptyStates(modal);
        }

        parseDebugInfo(debugBox) {
            const text = debugBox.textContent;
            const messageCountMatch = text.match(/(\d+)\s*msgs/);
            const currentMessageCount = messageCountMatch
                ? parseInt(messageCountMatch[1])
                : 0;

            const debugInfo = {
                conversationId: this.extractValue(
                    text,
                    "Conv ID:",
                    /Conv ID:\s*(\w+)/
                ),
                messageCount: currentMessageCount,
                timestamp: this.extractValue(
                    text,
                    "Time:",
                    /(\d{2}:\d{2}:\d{2})/
                ),
            };

            this.log("📊 Debug Info Parsed:", debugInfo);

            // Smart new message detection
            if (
                currentMessageCount > this.state.lastMessageCount &&
                this.state.lastMessageCount > 0
            ) {
                this.log(
                    "📨 New message detected via debug info! Auto-scrolling..."
                );
                this.handleNewMessage();
            }

            this.state.lastMessageCount = currentMessageCount;
        }

        extractValue(text, label, regex) {
            const match = text.match(regex);
            return match ? match[1].trim() : "Not found";
        }

        analyzeMessages(modal) {
            const messageDivs = modal.querySelectorAll(
                '[style*="margin-bottom: 16px"], .message-wrapper'
            );
            const debugBoxes = modal.querySelectorAll(this.debugBoxSelector);
            const emptyStates = modal.querySelectorAll(
                '[style*="background: #ffcccc"], .empty-messages'
            );

            const analysis = {
                messageDivs: messageDivs.length,
                debugBoxes: debugBoxes.length,
                emptyStates: emptyStates.length,
                actualMessages:
                    messageDivs.length - debugBoxes.length - emptyStates.length,
            };

            this.log("📈 Message Analysis:", analysis);
        }

        checkEmptyStates(modal) {
            const emptyState = modal.querySelector(
                '[style*="background: #ffcccc"], .empty-messages'
            );
            if (emptyState) {
                this.log(
                    "🕳️ Empty state detected:",
                    emptyState.textContent.trim()
                );
                // Don't auto-scroll if empty
                return;
            }
        }

        // Enhanced scrolling methods
        autoScrollToBottom() {
            if (!this.state.autoScrollEnabled && this.state.modalScrolled) {
                this.log("📜 Auto-scroll disabled by user position");
                return;
            }

            this.smartScrollToBottom();
        }

        smartScrollToBottom() {
            const container = this.getMessagesContainer();
            if (!container) {
                this.log("❌ Messages container not found");
                return;
            }

            // Use smooth scrolling for better UX, but instant for initial load
            const behavior = this.state.modalScrolled ? "smooth" : "auto";

            container.scrollTo({
                top: container.scrollHeight,
                behavior: behavior,
            });

            this.log(`📜 Smart scrolled to bottom (${behavior})`);

            // Update state
            this.state.lastScrollPosition = container.scrollHeight;
        }

        forceScrollToBottom() {
            const container = this.getMessagesContainer();
            if (!container) {
                this.log("❌ Messages container not found for force scroll");
                return;
            }

            // Force immediate scroll
            container.scrollTop = container.scrollHeight;

            // Then do a smooth one for better UX
            setTimeout(() => {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: "smooth",
                });
            }, 50);

            this.log("📜 Force scrolled to bottom");
        }

        scheduleAutoScroll(delay = 200) {
            this.scrollScheduler.schedule(() => {
                if (this.isModalOpen()) {
                    this.autoScrollToBottom();
                }
            }, delay);
        }

        // Enhanced button fix with better detection
        fixStuckButton() {
            const sendButton = document.querySelector(
                '[wire\\:click="sendMessage"]'
            );
            if (!sendButton) return;

            // Multiple detection methods
            const fixes = [
                this.fixByLoadingAttribute,
                this.fixByTextContent,
                this.fixByDisabledState,
            ];

            fixes.forEach((fix) => fix.call(this, sendButton));
        }

        fixByLoadingAttribute(sendButton) {
            const sendingSpan = sendButton.querySelector("[wire\\:loading]");
            const normalSpan = sendButton.querySelector(
                "[wire\\:loading\\.remove]"
            );

            if (sendingSpan && normalSpan) {
                const isStuck =
                    sendingSpan.style.display !== "none" &&
                    !sendButton.hasAttribute("wire:loading.attr");

                if (isStuck) {
                    sendingSpan.style.display = "none";
                    normalSpan.style.display = "inline";
                    sendButton.disabled = false;
                    this.log("🔧 Fixed stuck send button (loading attribute)");
                }
            }
        }

        fixByTextContent(sendButton) {
            if (
                sendButton.textContent.includes("Sending...") &&
                !sendButton.disabled
            ) {
                setTimeout(() => {
                    if (sendButton.textContent.includes("Sending...")) {
                        sendButton.innerHTML = "<span>Send</span>";
                        sendButton.disabled = false;
                        this.log("🔧 Fixed stuck send button (text content)");
                    }
                }, 3000);
            }
        }

        fixByDisabledState(sendButton) {
            // Check for buttons stuck in disabled state
            if (
                sendButton.disabled &&
                !sendButton.querySelector("[wire\\:loading]")
            ) {
                setTimeout(() => {
                    sendButton.disabled = false;
                    this.log("🔧 Re-enabled stuck button");
                }, 2000);
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
        isModalOpen() {
            return !!this.getModal();
        }

        getModal() {
            return (
                document.querySelector(this.modalSelector) ||
                document.querySelector(".chat-modal-overlay")
            );
        }

        getMessagesContainer() {
            return document.querySelector(this.messagesSelector);
        }

        // Enhanced public API
        getPublicAPI() {
            return {
                test: {
                    openConversation: (id = 1) => {
                        const element = document.querySelector(
                            `[wire\\:click="selectConversation(${id})"]`
                        );
                        if (element) {
                            element.click();
                            this.log(`🎯 Triggered conversation ${id}`);
                            return true;
                        } else {
                            this.log(`❌ Conversation ${id} not found`);
                            return false;
                        }
                    },

                    closeModal: () => {
                        const closeBtn = document.querySelector(
                            '[wire\\:click*="closeModal"]'
                        );
                        if (closeBtn) {
                            closeBtn.click();
                            this.log("🚪 Modal closed");
                            return true;
                        }
                        return false;
                    },

                    forceScroll: () => {
                        this.forceScrollToBottom();
                        return "✅ Forced scroll to bottom";
                    },

                    fixButton: () => {
                        this.fixStuckButton();
                        return "✅ Attempted to fix stuck button";
                    },

                    toggleAutoScroll: () => {
                        this.state.autoScrollEnabled =
                            !this.state.autoScrollEnabled;
                        return `✅ Auto-scroll ${
                            this.state.autoScrollEnabled
                                ? "enabled"
                                : "disabled"
                        }`;
                    },

                    simulateMessage: () => {
                        this.handleNewMessage();
                        return "✅ Simulated new message";
                    },

                    resetState: () => {
                        this.resetModalState();
                        return "✅ Reset modal state";
                    },

                    scheduleScroll: (delay = 500) => {
                        this.scheduleAutoScroll(delay);
                        return `✅ Scheduled scroll in ${delay}ms`;
                    },
                },

                info: {
                    checkSystem: () => {
                        const systemInfo = {
                            livewire: typeof window.Livewire !== "undefined",
                            alpine: typeof window.Alpine !== "undefined",
                            modalOpen: this.isModalOpen(),
                            conversationsCount: document.querySelectorAll(
                                '[wire\\:click*="selectConversation"]'
                            ).length,
                            state: { ...this.state },
                            observers: Array.from(this.observers.keys()),
                            performance: this.getPerformanceInfo(),
                            scrollInfo: this.isModalOpen()
                                ? this.getScrollInfo()
                                : null,
                        };

                        this.log("🔧 System Check:", systemInfo);
                        return systemInfo;
                    },

                    getModalInfo: () => {
                        const modal = this.getModal();
                        if (!modal) return null;

                        const container = this.getMessagesContainer();
                        return {
                            isOpen: true,
                            hasDebugBox: !!modal.querySelector(
                                this.debugBoxSelector
                            ),
                            messagesCount: this.getMessageCount(modal),
                            hasEmptyState: !!modal.querySelector(
                                '[style*="background: #ffcccc"], .empty-messages'
                            ),
                            scrollInfo: container ? this.getScrollInfo() : null,
                            autoScrollEnabled: this.state.autoScrollEnabled,
                            modalOpenTime: this.state.modalOpenTime,
                            timeSinceOpen: this.state.modalOpenTime
                                ? Date.now() - this.state.modalOpenTime
                                : null,
                        };
                    },

                    getScrollInfo: () => {
                        const container = this.getMessagesContainer();
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
                            pendingScrolls: this.scrollScheduler.timeouts.size,
                        };
                    },

                    getState: () => ({ ...this.state }),
                },

                debug: {
                    enableDebug: () => {
                        this.debug = true;
                        return "✅ Debug enabled";
                    },

                    disableDebug: () => {
                        this.debug = false;
                        return "✅ Debug disabled";
                    },

                    resetObservers: () => {
                        this.stopObservingModal();
                        this.setupObservers();
                        return "✅ Observers reset";
                    },

                    clearScheduledScrolls: () => {
                        this.scrollScheduler.clear();
                        return "✅ Cleared all scheduled scrolls";
                    },

                    triggerModalCheck: () => {
                        this.checkForModalChanges();
                        return "✅ Triggered modal check";
                    },

                    getPerformanceMetrics: () => this.getPerformanceInfo(),
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

        // Cleanup method
        destroy() {
            if (this.modalWatcher) {
                clearInterval(this.modalWatcher);
            }
            this.scrollScheduler.clear();
            this.stopObservingModal();
            this.observers.clear();
            this.state.isInitialized = false;
            this.log("🧹 LiveChat handler destroyed");
        }
    }

    // Enhanced initialization with better error handling
    function initializeLiveChat() {
        try {
            // Prevent duplicate initialization
            if (window.LiveChatAPI) {
                console.log("💬 LiveChat already initialized, skipping...");
                return window.LiveChatAPI;
            }

            const handler = new LiveChatHandler();
            const api = handler.getPublicAPI();

            // Expose to global scope
            window.LiveChatAPI = api;
            window.LCA = api; // Short alias
            window.liveChatHandler = handler; // Direct access for advanced debugging

            // Enhanced console help
            console.log(`
🎮 Enhanced LiveChat API Commands (v3.1 - Fixed Auto-scroll):

📞 Conversation Management:
- LCA.test.openConversation(1)     // Open conversation by ID
- LCA.test.closeModal()            // Close modal
- LCA.test.resetState()            // Reset modal state

🔧 Troubleshooting:
- LCA.test.forceScroll()           // Force scroll to bottom
- LCA.test.fixButton()             // Fix stuck send button
- LCA.test.toggleAutoScroll()      // Toggle auto-scroll
- LCA.test.simulateMessage()       // Test auto-scroll
- LCA.test.scheduleScroll(500)     // Schedule scroll with delay

ℹ️ Information:
- LCA.info.checkSystem()           // Complete system status
- LCA.info.getModalInfo()          // Modal information
- LCA.info.getScrollInfo()         // Scroll position info
- LCA.info.getState()              // Current state

🐛 Debug & Performance:
- LCA.debug.enableDebug()          // Enable debug logging
- LCA.debug.disableDebug()         // Disable debug logging
- LCA.debug.resetObservers()       // Reset DOM observers
- LCA.debug.clearScheduledScrolls() // Clear pending scrolls
- LCA.debug.triggerModalCheck()    // Manual modal check
- LCA.debug.getPerformanceMetrics() // Performance info

🚨 Auto-scroll Fixed! Modal should now auto-scroll when opened!
Type LCA.info.checkSystem() to see current status!
            `);

            return api;
        } catch (error) {
            console.error("❌ Failed to initialize LiveChat:", error);
            return null;
        }
    }

    // Smart initialization
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initializeLiveChat);
    } else {
        // Use requestIdleCallback for better performance if available
        if (window.requestIdleCallback) {
            requestIdleCallback(initializeLiveChat);
        } else {
            setTimeout(initializeLiveChat, 100);
        }
    }

    // Expose initialization function for manual use
    window.initializeLiveChat = initializeLiveChat;
})();
