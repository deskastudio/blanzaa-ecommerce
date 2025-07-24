{{-- Chat Modal Component --}}
{{-- File: resources/views/filament/pages/live-chat/chat-modal.blade.php --}}

@php
    $selectedConversation = $this->getActiveConversations()->where('id', $selectedConversationId)->first();
    $messages = $this->getSelectedMessages();
@endphp

<div wire:poll.2s="refreshMessages" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; backdrop-filter: blur(4px);" 
     wire:click.self="closeModal"
     data-conversation-id="{{ $selectedConversationId }}"
     data-message-count="{{ $messages->count() }}">
    
    <div style="background: white; width: 100%; max-width: 800px; height: 600px; display: flex; flex-direction: column; border-radius: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); position: relative;">
        
        <!-- Debug Box -->
        @include('filament.pages.live-chat.debug-box')
        
        <!-- Modal Header -->
        @include('filament.pages.live-chat.modal-header', ['selectedConversation' => $selectedConversation])
        
        <!-- Messages Area -->
        @include('filament.pages.live-chat.messages-area', ['messages' => $messages])
        
        <!-- Message Input -->
        @include('filament.pages.live-chat.message-input')
    </div>
</div>

<style>
    /* Modal responsive */
    @media (max-width: 768px) {
        div[data-conversation-id] > div {
            height: 100vh !important;
            max-height: none !important;
            border-radius: 0 !important;
        }
        
        div[data-conversation-id] {
            padding: 0 !important;
        }
    }
</style>