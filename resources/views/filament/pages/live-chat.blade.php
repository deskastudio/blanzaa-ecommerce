<x-filament-panels::page>
    <div wire:poll.3s>  
        <!-- Statistics -->
        @php $stats = $this->getConversationStats(); @endphp
        <div class="stats-container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number stat-blue">{{ $stats['total_active'] ?? 0 }}</div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-yellow">{{ $stats['total_pending'] ?? 0 }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-green">{{ $stats['my_assigned'] ?? 0 }}</div>
                    <div class="stat-label">My Assigned</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number stat-red">{{ $stats['unread_total'] ?? 0 }}</div>
                    <div class="stat-label">Unread</div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="chat-header">
            <h1 class="header-title">Live Chat Dashboard</h1>
            <p class="header-subtitle">Manage customer conversations</p>
        </div>

        <!-- Conversations List -->
        <div class="conversations-container" data-conversations-count="{{ $this->getActiveConversations()->count() }}">
            @forelse($this->getActiveConversations() as $conversation)
                <div wire:click="selectConversation({{ $conversation->id }})" 
                     class="conversation-item {{ $conversation->is_assigned_to_me ? 'assigned-to-me' : '' }}"
                     data-conversation-id="{{ $conversation->id }}"
                     data-unread-count="{{ $conversation->unread_count }}">
                    
                    <div class="conversation-content">
                        <!-- Avatar -->
                        <div class="avatar avatar-{{ $conversation->status }}">
                            {{ $conversation->user->initials ?? strtoupper(substr($conversation->user->name, 0, 2)) }}
                        </div>
                        
                        <!-- Content -->
                        <div class="conversation-details">
                            <!-- Name and badges -->
                            <div class="conversation-header">
                                <h3 class="user-name">
                                    {{ $conversation->user->name }}
                                    @if($conversation->is_assigned_to_me)
                                        <span class="assigned-badge">Assigned to you</span>
                                    @endif
                                </h3>
                                
                                <div class="conversation-meta">
                                    @if($conversation->unread_count > 0)
                                        <span class="unread-badge" data-count="{{ $conversation->unread_count }}">
                                            {{ $conversation->unread_count > 99 ? '99+' : $conversation->unread_count }}
                                        </span>
                                    @endif
                                    
                                    <span class="timestamp">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : $conversation->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Email and status -->
                            <div class="conversation-info">
                                <p class="user-email">{{ $conversation->user->email }}</p>
                                <span class="status-badge status-{{ $conversation->status }}">
                                    {{ ucfirst($conversation->status) }}
                                </span>
                            </div>
                            
                            <!-- Assignment and last message -->
                            <div class="conversation-footer">
                                <div class="assignment-info">
                                    @if($conversation->admin)
                                        <span class="assigned">✓ {{ $conversation->admin->name }}</span>
                                    @else
                                        <span class="unassigned">⚠ Unassigned</span>
                                    @endif
                                </div>
                                
                                @if($conversation->last_message)
                                    <div class="last-message">
                                        <strong>{{ $conversation->last_message_sender }}:</strong>
                                        {{ $conversation->last_message }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="conversation-arrow">→</div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">💬</div>
                    <h3 class="empty-title">No Active Conversations</h3>
                    <p class="empty-description">New conversations will appear here when customers start chatting.</p>
                    <button onclick="location.reload()" class="refresh-button">Refresh</button>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Chat Modal -->
    @if($selectedConversationId)
        @php
            $selectedConversation = $this->getActiveConversations()->where('id', $selectedConversationId)->first();
            $messages = $this->getSelectedMessages();
        @endphp
        
        <div wire:poll.2s="refreshMessages" 
             class="chat-modal-overlay" 
             wire:click.self="closeModal"
             data-conversation-id="{{ $selectedConversationId }}"
             data-message-count="{{ $messages->count() }}">
            
            <div class="chat-modal">
                
                <!-- Debug Box - Will be removed in production -->
                <div class="debug-box">
                    🔄 Conv ID: {{ $selectedConversationId }} | {{ $messages->count() }} msgs | {{ now()->format('H:i:s') }}
                </div>
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="header-user-info">
                        <div class="modal-avatar">
                            {{ $selectedConversation->user->initials ?? strtoupper(substr($selectedConversation->user->name, 0, 2)) }}
                        </div>
                        <div class="user-details">
                            <h3 class="modal-user-name">{{ $selectedConversation->user->name }}</h3>
                            <p class="modal-user-email">{{ $selectedConversation->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="header-actions">
                        @if(!$selectedConversation->admin_id)
                            <button wire:click="assignToMe({{ $selectedConversationId }})" class="assign-button">
                                Assign to Me
                            </button>
                        @endif
                        
                        <button wire:click="closeConversation({{ $selectedConversationId }})"
                                onclick="return confirm('Close this conversation?')"
                                class="close-conversation-button">
                                Close
                        </button>
                        
                        <button wire:click="closeModal" class="close-modal-button" data-modal-close>×</button>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="messages-container" id="messages-container">
                    @forelse($messages as $message)
                        <div class="message-wrapper message-{{ $message->sender_type }}" data-message-id="{{ $message->id }}">
                            <div class="message-bubble">
                                @if($message->sender_type === 'admin')
                                    <!-- Admin Message -->
                                    <div class="admin-message">
                                        <p class="message-text">{{ $message->message }}</p>
                                        <div class="message-meta">
                                            <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                            <span class="message-sender">{{ $message->sender->name }}</span>
                                        </div>
                                    </div>
                                @else
                                    <!-- User Message -->
                                    <div class="user-message">
                                        <p class="message-text">{{ $message->message }}</p>
                                        <div class="message-meta">
                                            <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                            <span class="message-sender">{{ $message->sender->name }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-messages">
                            <div class="empty-messages-icon">💬</div>
                            <h4 class="empty-messages-title">No messages yet</h4>
                            <p class="empty-messages-text">Start the conversation by sending a message below.</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Message Input -->
                <div class="message-input-container">
                    <div class="input-wrapper">
                        <input wire:model.live="message"
                               type="text"
                               placeholder="Type your message..."
                               class="message-input"
                               onkeydown="if(event.key==='Enter' && !event.shiftKey && this.value.trim()) { event.preventDefault(); @this.call('sendMessage'); }"
                               data-chat-input>
                        
                        <button wire:click="sendMessage"
                                class="send-button"
                                wire:loading.attr="disabled"
                                data-send-button>
                                <span wire:loading.remove>Send</span>
                                <span wire:loading>Sending...</span>
                        </button>
                    </div>
                    
                    <div class="input-help">
                        Press Enter to send • Click outside to close
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Styles -->
    <style>
        /* Statistics */
        .stats-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-blue { color: #3b82f6; }
        .stat-yellow { color: #f59e0b; }
        .stat-green { color: #10b981; }
        .stat-red { color: #ef4444; }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
        }

        /* Header */
        .chat-header {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .header-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .header-subtitle {
            margin: 5px 0 0 0;
            opacity: 0.8;
        }

        /* Conversations */
        .conversations-container {
            background: white;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-height: 600px;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .conversation-item:hover {
            background-color: #f9fafb;
        }

        .conversation-item.assigned-to-me {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
        }

        .conversation-item.assigned-to-me:hover {
            background-color: #eff6ff;
        }

        .conversation-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .avatar-active { background: #10b981; }
        .avatar-pending { background: #f59e0b; }

        .conversation-details {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .user-name {
            font-weight: 600;
            color: #111827;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .assigned-badge {
            margin-left: 8px;
            padding: 2px 8px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 12px;
            font-size: 12px;
        }

        .conversation-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unread-badge {
            background: #ef4444;
            color: white;
            padding: 2px 6px;
            border-radius: 50%;
            font-size: 12px;
            min-width: 20px;
            text-align: center;
            animation: pulse 2s infinite;
        }

        .timestamp {
            font-size: 12px;
            color: #6b7280;
        }

        .conversation-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .user-email {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .conversation-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .assigned {
            font-size: 12px;
            color: #059669;
        }

        .unassigned {
            font-size: 12px;
            color: #d97706;
        }

        .last-message {
            font-size: 12px;
            color: #6b7280;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-arrow {
            color: #9ca3af;
            font-size: 18px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            font-size: 32px;
            color: #9ca3af;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 500;
            color: #111827;
            margin: 0 0 8px 0;
        }

        .empty-description {
            color: #6b7280;
            margin: 0 0 16px 0;
        }

        .refresh-button {
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .refresh-button:hover {
            background: #2563eb;
        }

        /* Chat Modal */
        .chat-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
            backdrop-filter: blur(4px);
        }

        .chat-modal {
            background: white;
            width: 100%;
            max-width: 800px;
            height: 600px;
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            position: relative;
        }

        .debug-box {
            position: absolute;
            top: 10px;
            right: 10px;
            background: lime;
            color: black;
            padding: 5px;
            font-size: 10px;
            z-index: 10000;
            border-radius: 4px;
            font-family: monospace;
        }

        .modal-header {
            padding: 20px;
            background: #3b82f6;
            color: white;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .modal-user-name {
            font-weight: 600;
            margin: 0;
        }

        .modal-user-email {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .assign-button {
            padding: 6px 12px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .assign-button:hover {
            background: rgba(255,255,255,0.3);
        }

        .close-conversation-button {
            padding: 6px 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .close-conversation-button:hover {
            background: #dc2626;
        }

        .close-modal-button {
            padding: 8px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .close-modal-button:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Messages */
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8fafc;
            scroll-behavior: smooth;
        }

        .message-wrapper {
            margin-bottom: 16px;
            display: flex;
        }

        .message-admin {
            justify-content: flex-end;
        }

        .message-user {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 400px;
        }

        .admin-message {
            background: #3b82f6;
            color: white;
            padding: 12px 16px;
            border-radius: 18px 18px 4px 18px;
            box-shadow: 0 2px 8px rgba(59,130,246,0.3);
        }

        .user-message {
            background: white;
            color: #374151;
            padding: 12px 16px;
            border-radius: 18px 18px 18px 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
        }

        .message-text {
            margin: 0;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message-meta {
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
        }

        .admin-message .message-meta {
            color: rgba(255,255,255,0.8);
        }

        .user-message .message-meta {
            color: #6b7280;
        }

        /* Empty Messages */
        .empty-messages {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-messages-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-messages-title {
            font-weight: 500;
            color: #374151;
            margin: 0 0 8px 0;
        }

        .empty-messages-text {
            margin: 0;
        }

        /* Message Input */
        .message-input-container {
            border-top: 1px solid #e5e7eb;
            padding: 20px;
            background: white;
            border-radius: 0 0 12px 12px;
        }

        .input-wrapper {
            display: flex;
            gap: 12px;
        }

        .message-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 25px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .message-input:focus {
            border-color: #3b82f6;
        }

        .send-button {
            padding: 12px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            min-width: 80px;
            transition: background-color 0.2s ease;
        }

        .send-button:hover:not(:disabled) {
            background: #2563eb;
        }

        .send-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .input-help {
            text-align: center;
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
        }

        /* Animations */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .chat-modal {
                height: 100vh;
                max-height: none;
                border-radius: 0;
            }
            
            .conversation-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            
            .conversation-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }

        /* Smooth scrolling enhancement */
        .messages-container {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .messages-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <!-- Load the optimized JavaScript -->
    <script src="{{ asset('js/filament/pages/live-chat.js') }}"></script>
</x-filament-panels::page>