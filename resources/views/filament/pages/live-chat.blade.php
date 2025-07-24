<x-filament-panels::page>
    <!-- Main Page Layout -->
    <div wire:poll.3s>
        <!-- Statistics Component -->
        @include('filament.pages.live-chat.statistics')
        
        <!-- Header Component -->
        @include('filament.pages.live-chat.header')
        
        <!-- Conversations List Component -->
        @include('filament.pages.live-chat.conversations-list')
    </div>
    
    <!-- Chat Modal Component -->
    @if($selectedConversationId)
        @include('filament.pages.live-chat.chat-modal')
    @endif

    <!-- Load JavaScript Modules -->
    @include('filament.pages.live-chat.scripts')
</x-filament-panels::page>