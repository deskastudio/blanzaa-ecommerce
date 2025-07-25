{{-- File: resources/views/components/chat-widget/chat-window.blade.php --}}
{{-- Chat Window Container Component --}}

<div id="chat-window" style="display: none;" class="absolute bottom-16 right-0 w-96 h-[500px] bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden">
    {{-- Content will be filled by main widget --}}
    {{ $slot ?? '' }}
</div>

<style>
/* Chat window display utilities */
#chat-window.window-show {
    display: flex !important;
    flex-direction: column !important;
}

#chat-window.window-hidden {
    display: none !important;
}

/* Chat window responsive design */
@media (max-width: 480px) {
    #chat-window {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 0 !important;
        max-width: none !important;
        max-height: none !important;
        border: none !important;
    }
}

/* Window animations */
#chat-window.show {
    display: flex !important;
    flex-direction: column !important;
    animation: windowSlideUp 0.3s ease-out;
}

#chat-window.hide {
    display: flex !important;
    flex-direction: column !important;
    animation: windowSlideDown 0.3s ease-in;
}

@keyframes windowSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes windowSlideDown {
    from {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
}

/* Ensure proper stacking */
#chat-window {
    z-index: 9999;
}
</style>