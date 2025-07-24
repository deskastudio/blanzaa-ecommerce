{{-- Message Input Component - Enhanced Button Fix --}}
{{-- File: resources/views/filament/pages/live-chat/message-input.blade.php --}}

<div style="border-top: 1px solid #e5e7eb; padding: 20px; background: white; border-radius: 0 0 12px 12px;">
    <div style="display: flex; gap: 12px; align-items: end;">
        <input wire:model.live="message"
               type="text"
               placeholder="Type your message..."
               style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 25px; outline: none; transition: border-color 0.2s ease; background: white; color: #374151;"
               onkeydown="if(event.key==='Enter' && !event.shiftKey && this.value.trim()) { event.preventDefault(); @this.call('sendMessage'); }"
               onfocus="this.style.borderColor='#3b82f6'"
               onblur="this.style.borderColor='#e5e7eb'"
               data-chat-input>
        
        <button wire:click="sendMessage"
                style="padding: 12px 20px; background: #3b82f6; color: white; border: none; border-radius: 25px; cursor: pointer; min-width: 80px; transition: all 0.2s ease; position: relative; display: flex; align-items: center; justify-content: center; font-weight: 500;"
                wire:loading.attr="disabled"
                data-send-button
                onmouseover="if (!this.disabled) { this.style.background='#2563eb'; this.style.transform='translateY(-1px)'; }"
                onmouseout="if (!this.disabled) { this.style.background='#3b82f6'; this.style.transform='translateY(0)'; }"
                onfocus="this.style.outline='2px solid #1d4ed8'; this.style.outlineOffset='2px';"
                onblur="this.style.outline='none';">
                <span wire:loading.remove>Send</span>
                <span wire:loading style="display: flex; align-items: center; gap: 8px;">
                    <svg style="animation: spin 1s linear infinite; width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
        </button>
    </div>
    
    <div style="text-align: center; margin-top: 8px; font-size: 12px; color: #6b7280;">
        Press Enter to send • Click outside to close
    </div>
</div>

<style>
    /* CRITICAL BUTTON FIX - Priority CSS */
    
    /* Force hide loading state when not actually loading */
    [data-send-button]:not([disabled]) [wire\:loading],
    [data-send-button]:not([wire\:loading]) [wire\:loading] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

    /* Force show normal state when not loading */
    [data-send-button]:not([disabled]) [wire\:loading\.remove],
    [data-send-button]:not([wire\:loading]) [wire\:loading\.remove] {
        display: inline !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Only show loading when actually loading */
    [data-send-button][wire\:loading] [wire\:loading] {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Hide normal state when loading */
    [data-send-button][wire\:loading] [wire\:loading\.remove] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

    /* Button state colors - Force override */
    [data-send-button]:not([disabled]) {
        background-color: #3b82f6 !important;
        cursor: pointer !important;
        opacity: 1 !important;
    }

    [data-send-button][disabled] {
        background-color: #6b7280 !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }

    /* Reset conflicting styles */
    [data-send-button] * {
        pointer-events: inherit;
    }

    /* Specific Livewire conflict fixes */
    [data-send-button]:not([wire\:loading\:attr="disabled"]) [wire\:loading] {
        display: none !important;
    }

    /* Force button content fixes */
    [data-send-button]:not([disabled]):not([wire\:loading]) {
        color: white !important;
    }

    /* Spinning animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    [data-send-button] [wire\:loading] svg {
        animation: spin 1s linear infinite;
    }

    /* Input focus effect */
    [data-chat-input]:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        div[style*="display: flex; gap: 12px; align-items: end;"] {
            gap: 8px !important;
        }
        
        [data-send-button] {
            min-width: 60px !important;
            padding: 10px 16px !important;
        }
        
        [data-chat-input] {
            padding: 10px 14px !important;
        }
    }

    /* DEBUG STYLES - Remove in production */
    [data-send-button] {
        position: relative;
    }

    /* Uncomment to see debug info */
    /*
    [data-send-button]::after {
        content: attr(data-debug);
        position: absolute;
        top: -30px;
        right: 0;
        font-size: 9px;
        background: red;
        color: white;
        padding: 2px 4px;
        border-radius: 2px;
        white-space: nowrap;
        z-index: 1000;
    }
    */
</style>

<!-- Additional JavaScript for aggressive button monitoring -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Emergency button reset function
        window.emergencyButtonReset = function() {
            const button = document.querySelector('[data-send-button]');
            if (!button) return;

            console.log('🚨 EMERGENCY BUTTON RESET');
            
            // Force reset everything
            button.disabled = false;
            button.removeAttribute('disabled');
            button.removeAttribute('wire:loading');
            button.style.backgroundColor = '#3b82f6';
            button.style.cursor = 'pointer';
            button.style.opacity = '1';
            
            // Reset innerHTML
            button.innerHTML = `
                <span wire:loading.remove>Send</span>
                <span wire:loading style="display: none;">Sending...</span>
            `;
            
            console.log('✅ Emergency reset completed');
        };

        // Auto-monitor button every 2 seconds
        setInterval(function() {
            const button = document.querySelector('[data-send-button]');
            const input = document.querySelector('[data-chat-input]');
            
            if (button && input) {
                const buttonText = button.textContent || '';
                const hasMessage = input.value.trim().length > 0;
                const isDisabled = button.disabled;
                
                // Check for stuck state
                if ((buttonText.includes('Sending') && !hasMessage && !isDisabled) ||
                    (buttonText.includes('Send') && buttonText.includes('Sending'))) {
                    console.log('🚨 Stuck button detected, auto-fixing...');
                    window.emergencyButtonReset();
                }
            }
        }, 2000);

        console.log('💬 Emergency button monitoring active');
    });
</script>