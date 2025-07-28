{{-- Conversations List Component - Clean Version --}}
{{-- File: resources/views/filament/pages/live-chat/conversations-list.blade.php --}}

@php
    $conversations = $this->getActiveConversations();
    $conversationCount = $conversations->count();
@endphp

<div style="
    background: white; 
    border-radius: 0 0 8px 8px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    max-height: 500px; 
    overflow-y: auto; 
    position: relative;
" 
data-conversations-count="{{ $conversationCount }}">

    @forelse($conversations as $conversation)
        @include('filament.pages.live-chat.conversation-item', ['conversation' => $conversation])
    @empty
        @include('filament.pages.live-chat.empty-state')
    @endforelse
    
    {{-- Clean footer info --}}
    @if($conversationCount > 0)
        <div style="
            padding: 8px 16px; 
            background:  linear-gradient(90deg, #3b82f6, #1d4ed8); 
            border-top: 1px solid #e5e7eb; 
            font-size: 11px; 
            color: #fff; 
            text-align: center;
            position: sticky;
            bottom: 0;
        ">
            {{ $conversationCount }} active conversation{{ $conversationCount !== 1 ? 's' : '' }}
        </div>
    @endif
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

    /* Smooth scrolling */
    div[data-conversations-count] {
        scroll-behavior: smooth;
    }
</style>