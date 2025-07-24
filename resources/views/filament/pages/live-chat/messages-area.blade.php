{{-- Messages Area Component --}}
{{-- File: resources/views/filament/pages/live-chat/messages-area.blade.php --}}

<div style="flex: 1; overflow-y: auto; padding: 20px; background: #f8fafc; scroll-behavior: smooth;" id="messages-container">
    @forelse($messages as $message)
        @include('filament.pages.live-chat.message-item', ['message' => $message])
    @empty
        @include('filament.pages.live-chat.empty-messages')
    @endforelse
</div>

<style>
    /* Messages container scrollbar */
    #messages-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    #messages-container::-webkit-scrollbar {
        width: 6px;
    }

    #messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    #messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    #messages-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Message animations */
    .message-wrapper {
        animation: fadeInMessage 0.3s ease-out;
    }

    @keyframes fadeInMessage {
        from { 
            opacity: 0; 
            transform: translateY(10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
</style>