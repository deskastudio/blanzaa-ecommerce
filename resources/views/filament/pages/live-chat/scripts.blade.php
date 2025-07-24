{{-- Scripts Component --}}
{{-- File: resources/views/filament/pages/live-chat/scripts.blade.php --}}

<!-- Load Modular JavaScript Files in Correct Order -->
<script src="{{ asset('js/filament/pages/chat-modal.js') }}"></script>
<script src="{{ asset('js/filament/pages/chat-scroll.js') }}"></script>
<script src="{{ asset('js/filament/pages/chat-button.js') }}"></script>
<script src="{{ asset('js/filament/pages/chat-events.js') }}"></script>
<script src="{{ asset('js/filament/pages/chat-observers.js') }}"></script>
<script src="{{ asset('js/filament/pages/chat-api.js') }}"></script>

<!-- Core initialization script loads last -->
<script src="{{ asset('js/filament/pages/live-chat-core.js') }}"></script>

<!-- Fallback Script for Critical Functions -->
<script>
    // Ensure critical functions work even if modular JS fails
    document.addEventListener('DOMContentLoaded', function() {
        // Fallback auto-scroll function
        window.liveChatFallbackScroll = function() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
                console.log('💬 Fallback scroll executed');
            }
        };

        // Auto-scroll when modal opens
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === Node.ELEMENT_NODE && 
                            node.querySelector && 
                            node.querySelector('#messages-container')) {
                            setTimeout(window.liveChatFallbackScroll, 300);
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Livewire update fallback
        document.addEventListener('livewire:updated', function() {
            setTimeout(window.liveChatFallbackScroll, 100);
        });

        console.log('💬 Fallback functions initialized');
    });
</script>