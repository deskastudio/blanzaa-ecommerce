{{-- File: resources/views/livewire/navigation-chat-notification.blade.php --}}

<div wire:poll.5s="refreshNotifications" class="relative">
    @if($unreadCount > 0 || $activeConversations > 0)
        <!-- Badge Container -->
        <div class="absolute -top-2 -right-2 z-10">
            @if($unreadCount > 0)
                <!-- Unread Messages Badge -->
                <div class="
                    inline-flex items-center justify-center 
                    min-w-[18px] h-[18px] 
                    text-xs font-bold text-white 
                    rounded-full
                    {{ $hasUrgent ? 'bg-red-600 animate-pulse' : ($unreadCount >= 5 ? 'bg-orange-500' : 'bg-blue-600') }}
                    border-2 border-white
                    shadow-sm
                ">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </div>
            @elseif($activeConversations > 0)
                <!-- Active Conversations Indicator -->
                <div class="
                    w-3 h-3 
                    bg-green-500 
                    rounded-full 
                    border-2 border-white
                    animate-pulse
                "></div>
            @endif
        </div>

        <!-- Tooltip Info on Hover -->
        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-20">
            <div class="bg-gray-900 text-white text-xs rounded py-2 px-3 whitespace-nowrap">
                @if($unreadCount > 0)
                    {{ $unreadCount }} unread message{{ $unreadCount !== 1 ? 's' : '' }}
                    @if($activeConversations > 0)
                        <br>{{ $activeConversations }} active conversation{{ $activeConversations !== 1 ? 's' : '' }}
                    @endif
                @else
                    {{ $activeConversations }} active conversation{{ $activeConversations !== 1 ? 's' : '' }}
                @endif
                @if($hasUrgent)
                    <br><span class="text-red-300">⚠ Urgent messages!</span>
                @endif
                
                <!-- Tooltip Arrow -->
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Ensure proper positioning within navigation */
    .relative {
        position: relative !important;
    }
    
    /* Hover group for tooltip */
    .navigation-item:hover .group-hover\:opacity-100 {
        opacity: 1 !important;
    }
    
    /* Animation untuk urgent notifications */
    @keyframes urgentPulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1);
        }
        50% { 
            opacity: 0.7;
            transform: scale(1.1);
        }
    }
    
    .animate-urgent {
        animation: urgentPulse 1s ease-in-out infinite;
    }
</style>