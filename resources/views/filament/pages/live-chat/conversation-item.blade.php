{{-- Single Conversation Item - Improved Styling --}}
{{-- File: resources/views/filament/pages/live-chat/conversation-item.blade.php --}}

<div wire:click="selectConversation({{ $conversation->id }})" 
     class="conversation-item {{ $conversation->is_assigned_to_me ? 'assigned-to-me' : '' }}"
     data-conversation-id="{{ $conversation->id }}"
     data-unread-count="{{ $conversation->unread_count }}"
     style="
         padding: 16px; 
         border-bottom: 1px solid #f3f4f6; 
         cursor: pointer; 
         transition: all 0.2s ease; 
         background: {{ $conversation->is_assigned_to_me ? '#eff6ff' : 'white' }}; 
         {{ $conversation->is_assigned_to_me ? 'border-left: 4px solid #3b82f6;' : '' }}
         position: relative;
         min-height: 80px;
         display: flex;
         align-items: center;
     "
     onmouseover="this.style.backgroundColor='{{ $conversation->is_assigned_to_me ? '#dbeafe' : '#f9fafb' }}'"
     onmouseout="this.style.backgroundColor='{{ $conversation->is_assigned_to_me ? '#eff6ff' : 'white' }}'">
    
    <div style="display: flex; align-items: flex-start; gap: 12px; width: 100%;">
        <!-- Avatar - Fixed Size -->
        <div style="flex-shrink: 0;">
            @include('filament.pages.live-chat.avatar', [
                'initials' => $conversation->user->initials ?? strtoupper(substr($conversation->user->name, 0, 2)),
                'status' => $conversation->status
            ])
        </div>
        
        <!-- Content Area - Flexible -->
        <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px;">
            
            <!-- Row 1: Name and Time -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <h3 style="
                    font-weight: 600; 
                    color: #111827; 
                    margin: 0; 
                    font-size: 14px;
                    white-space: nowrap; 
                    overflow: hidden; 
                    text-overflow: ellipsis; 
                    max-width: 200px;
                ">
                    {{ $conversation->user->name }}
                </h3>
                
                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    @if($conversation->unread_count > 0)
                        <span style="
                            background: #ef4444; 
                            color: white; 
                            padding: 2px 6px; 
                            border-radius: 50%; 
                            font-size: 11px; 
                            min-width: 18px; 
                            height: 18px;
                            text-align: center; 
                            line-height: 14px;
                            animation: pulse 2s infinite;
                            font-weight: bold;
                        ">
                            {{ $conversation->unread_count > 99 ? '99+' : $conversation->unread_count }}
                        </span>
                    @endif
                    
                    <span style="font-size: 11px; color: #6b7280; white-space: nowrap;">
                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : $conversation->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            
           
            
          
            
            <!-- Row 4: Last Message -->
            @if($conversation->last_message)
                <div style="margin-top: 4px;">
                    <p style="
                        font-size: 12px; 
                        color: #6b7280; 
                        margin: 0; 
                        line-height: 1.3;
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    ">
                        @if($conversation->last_message_sender)
                            <span style="font-weight: 500; color: #374151;">{{ $conversation->last_message_sender }}:</span>
                        @endif
                        {{ $conversation->last_message }}
                    </p>
                </div>
            @else
                <div style="margin-top: 4px;">
                    <p style="font-size: 12px; color: #9ca3af; margin: 0; font-style: italic;">
                        No messages yet
                    </p>
                </div>
            @endif
        </div>
        
        <!-- Arrow Indicator -->
        <div style="
            color: #9ca3af; 
            font-size: 16px; 
            flex-shrink: 0;
            margin-left: 8px;
            transition: transform 0.2s ease;
        " class="arrow-indicator">
            →
        </div>
    </div>
</div>

<style>
    /* Hover effects untuk arrow */
    .conversation-item:hover .arrow-indicator {
        transform: translateX(4px);
        color: #3b82f6;
    }

    /* Pulse animation untuk unread badge */
    @keyframes pulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1);
        }
        50% { 
            opacity: 0.8;
            transform: scale(1.05);
        }
    }

    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        .conversation-item {
            padding: 12px !important;
        }
        
        .conversation-item h3 {
            max-width: 150px !important;
            font-size: 13px !important;
        }
        
        .conversation-item p {
            font-size: 11px !important;
        }
        
        .conversation-item span {
            font-size: 9px !important;
            padding: 1px 6px !important;
        }
    }
</style>