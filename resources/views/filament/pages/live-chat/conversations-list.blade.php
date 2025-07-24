{{-- Conversations List Component --}}
{{-- File: resources/views/filament/pages/live-chat/conversations-list.blade.php --}}

<div style="background: white; border-radius: 0 0 8px 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-height: 600px; overflow-y: auto;" data-conversations-count="{{ $this->getActiveConversations()->count() }}">
    @forelse($this->getActiveConversations() as $conversation)
        @include('filament.pages.live-chat.conversation-item', ['conversation' => $conversation])
    @empty
        @include('filament.pages.live-chat.empty-state')
    @endforelse
</div>

<style>
    /* Scrollbar styling */
    div[data-conversations-count]::-webkit-scrollbar {
        width: 6px;
    }

    div[data-conversations-count]::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    div[data-conversations-count]::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    div[data-conversations-count]::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>