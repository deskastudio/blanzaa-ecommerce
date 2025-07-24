// Enhanced Button Manager - Force Reset
// Update untuk chat-button.js

class ChatButtonManager {
    constructor(core) {
        this.core = core;

        this.state = {
            lastSendAttempt: null,
            fixCount: 0,
            lastResetTime: null,
        };

        // Aggressive monitoring
        this.startButtonMonitoring();
    }

    startButtonMonitoring() {
        // Monitor button every 500ms
        this.buttonMonitor = setInterval(() => {
            this.aggressiveButtonCheck();
        }, 500);

        this.core.log("🔧 Aggressive button monitoring started");
    }

    aggressiveButtonCheck() {
        const sendButton = this.getElement();
        if (!sendButton) return;

        const messageInput = this.getMessageInput();
        const hasMessage =
            messageInput &&
            messageInput.value &&
            messageInput.value.trim().length > 0;

        // Check for stuck state
        const buttonText = sendButton.textContent || sendButton.innerText || "";
        const isDisabled = sendButton.disabled;
        const hasWireLoading = sendButton.hasAttribute("wire:loading");
        const livewireActive = this.core.isLivewireActive();

        // Debug info
        sendButton.setAttribute(
            "data-debug",
            `Text: ${
                buttonText.includes("Sending") ? "SENDING" : "SEND"
            } | Disabled: ${isDisabled} | Wire: ${hasWireLoading} | LW: ${livewireActive} | Msg: ${
                hasMessage ? "Y" : "N"
            }`
        );

        // Stuck conditions
        const isStuck =
            // Shows "Sending..." but no message and not disabled
            (buttonText.includes("Sending") && !hasMessage && !isDisabled) ||
            // Shows both "Send" and "Sending..."
            (buttonText.includes("Send") && buttonText.includes("Sending")) ||
            // Disabled but no active Livewire
            (isDisabled && !livewireActive && !hasMessage) ||
            // Has wire:loading attribute but no active request
            (hasWireLoading && !livewireActive && !hasMessage);

        if (isStuck) {
            this.core.log("🚨 STUCK BUTTON DETECTED - Forcing reset", {
                buttonText,
                isDisabled,
                hasWireLoading,
                livewireActive,
                hasMessage,
            });

            this.forceReset();
        }
    }

    forceReset() {
        const sendButton = this.getElement();
        if (!sendButton) return;

        try {
            // 1. Reset disabled state
            sendButton.disabled = false;
            sendButton.removeAttribute("disabled");

            // 2. Reset wire attributes
            sendButton.removeAttribute("wire:loading");
            sendButton.removeAttribute("wire:loading.attr");

            // 3. Force innerHTML reset
            sendButton.innerHTML = `
                <span wire:loading.remove>Send</span>
                <span wire:loading style="display: flex; align-items: center; gap: 8px;">
                    <svg style="animation: spin 1s linear infinite; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            `;

            // 4. Force CSS reset
            sendButton.style.backgroundColor = "#3b82f6";
            sendButton.style.cursor = "pointer";
            sendButton.style.opacity = "1";

            // 5. Force hide loading elements
            const loadingElements =
                sendButton.querySelectorAll("[wire\\:loading]");
            loadingElements.forEach((el) => {
                el.style.display = "none";
                el.style.visibility = "hidden";
                el.style.opacity = "0";
            });

            // 6. Force show normal elements
            const normalElements = sendButton.querySelectorAll(
                "[wire\\:loading\\.remove]"
            );
            normalElements.forEach((el) => {
                el.style.display = "inline";
                el.style.visibility = "visible";
                el.style.opacity = "1";
            });

            // 7. Update state
            this.state.fixCount++;
            this.state.lastResetTime = Date.now();

            this.core.log("🔧 FORCE RESET COMPLETED", {
                fixCount: this.state.fixCount,
                buttonText: sendButton.textContent,
                disabled: sendButton.disabled,
            });
        } catch (error) {
            this.core.log("❌ Error in force reset:", error);
        }
    }

    handleSendClick() {
        this.core.log("📤 Send button clicked");
        this.state.lastSendAttempt = Date.now();

        // Schedule auto-scroll after send
        this.core.scroll.scheduleAutoScroll(800);

        // Schedule aggressive check after send
        setTimeout(() => {
            this.checkAfterSend();
        }, 2000); // Check after 2 seconds

        // Fallback check
        setTimeout(() => {
            this.forceReset();
        }, 10000); // Force reset after 10 seconds if still stuck
    }

    checkAfterSend() {
        const sendButton = this.getElement();
        const messageInput = this.getMessageInput();

        if (sendButton && sendButton.textContent.includes("Sending...")) {
            // Check if message input is empty (indicating message was sent)
            if (
                !messageInput ||
                !messageInput.value ||
                messageInput.value.trim() === ""
            ) {
                this.core.log(
                    "🔧 Message sent but button still shows 'Sending...', forcing reset..."
                );
                this.forceReset();
            }
        }
    }

    // Public methods
    getElement() {
        return this.core.getElement("sendButton");
    }

    getMessageInput() {
        return this.core.getElement("messageInput");
    }

    getState() {
        const sendButton = this.getElement();
        const messageInput = this.getMessageInput();

        if (!sendButton) return { error: "Send button not found" };

        return {
            buttonText: sendButton.textContent.trim(),
            isDisabled: sendButton.disabled,
            hasMessage: messageInput ? messageInput.value.trim() : "",
            livewireActive: this.core.isLivewireActive(),
            lastSendAttempt: this.state.lastSendAttempt,
            fixCount: this.state.fixCount,
            lastResetTime: this.state.lastResetTime,
            hasWireLoading: sendButton.hasAttribute("wire:loading"),
        };
    }

    // Manual reset method
    reset() {
        this.forceReset();
        return true;
    }

    destroy() {
        if (this.buttonMonitor) {
            clearInterval(this.buttonMonitor);
        }
        this.core.log("🧹 Button manager destroyed");
    }
}
