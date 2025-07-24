{{-- Single Conversation Item --}}
{{-- File: resources/views/filament/pages/live-chat/conversation-item.blade.php --}}

<div wire:click="selectConversation({{ $conversation->id }})" 
     class="conversation-item {{ $conversation->is_assigned_to_me ? 'assigned-to-me' : '' }}"
     data-conversation-id="{{ $conversation->id }}"
     data-unread-count="{{ $conversation->unread_count }}"
     style="padding: 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background-color 0.2s ease; background: {{ $conversation->is_assigned_to_me ? '#eff6ff' : 'white' }}; {{ $conversation->is_assigned_to_me ? 'border-left: 4px solid #3b82f6;' : '' }}"
     onmouseover="this.style.backgroundColor='{{ $conversation->is_assigned_to_me ? '#dbeafe' : '#f9fafb' }}'"
     onmouseout="this.style.backgroundColor='{{ $conversation->is_assigned_to_me ? '#eff6ff' : 'white' }}'">
    
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <!-- Avatar -->
        @include('filament.pages.live-chat.avatar', [
            'initials' => $conversation->user->initials ?? strtoupper(substr($conversation->user->name, 0, 2)),
            'status' => $conversation->status
        ])
        
        <!-- Content -->
        <div style="flex: 1; min-width: 0;">
            <!-- Name and badges -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                <h3 style="font-weight: 600; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                    {{ $conversation->user->name }}
                    @if($conversation->is_assigned_to_me)
                        <span style="margin-left: 8px; padding: 2px 8px; background: #dbeafe; color: #1e40af; border-radius: 12px; font-size: 12px; white-space: nowrap;">
                            Assigned to you
                        </span>
                    @endif
                </h3>
                
                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                    @if($conversation->unread_count > 0)
                        <span style="background: #ef4444; color: white; padding: 2px 6px; border-radius: 50%; font-size: 12px; min-width: 20px; text-align: center; animation: pulse 2s infinite;">
                            {{ $conversation->unread_count > 99 ? '99+' : $conversation->unread_count }}
                        </span>
                    @endif
                    
                    <span style="font-size: 12px; color: #6b7280; white-space: nowrap;">
                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : $conversation->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
            
            <!-- Email and status -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                <p style="font-size: 14px; color: #6b7280; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;">
                    {{ $conversation->user->email }}
                </p>
                
                <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; white-space: nowrap; flex-shrink: 0; {{ $conversation->status === 'active' ? 'background: #d1fae5; color: #065f46;' : 'background: #fef3c7; color: #92400e;' }}">
                    {{ ucfirst($conversation->status) }}
                </span>
            </div>
            
            <!-- Assignment and last message -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="flex-shrink: 0;">
                    @if($conversation->admin)
                        <span style="font-size: 12px; color: #059669;">
                            ✓ {{ $conversation->admin->name }}
                        </span>
                    @else
                        <span style="font-size: 12px; color: #d97706;">
                            ⚠ Unassigned
                        </span>
                    @endif
                </div>
                
                @if($conversation->last_message)
                    <div style="font-size: 12px; color: #6b7280; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: right;">
                        <strong>{{ $conversation->last_message_sender }}:</strong>
                        {{ $conversation->last_message }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Arrow -->
        <div style="color: #9ca3af; font-size: 18px; flex-shrink: 0;">→</div>
    </div>
</div>

<style>
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
</style>