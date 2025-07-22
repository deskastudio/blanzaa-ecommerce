<x-filament-panels::page>
    <!-- Main Page: Conversations List -->
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Chat Conversations</h2>
            <p class="text-sm text-gray-600 mt-1">Click on any conversation to start chatting</p>
        </div>
        
        <!-- Conversations List -->
        <div>
            @forelse($this->getActiveConversations() as $conversation)
                <div wire:click="selectConversation({{ $conversation->id }})" 
                     class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                    
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                            {{ strtoupper(substr($conversation->user->name, 0, 2)) }}
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-900">
                                    {{ $conversation->user->name }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if($conversation->unread_count > 0)
                                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $conversation->unread_count }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">
                                        {{ $conversation->last_message_at?->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-sm text-gray-600">
                                    {{ $conversation->user->email }}
                                </p>
                                <span class="text-xs px-2 py-1 rounded 
                                    {{ $conversation->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $conversation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $conversation->status === 'resolved' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($conversation->status) }}
                                </span>
                            </div>
                            
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-sm">
                                    @if($conversation->admin)
                                        <span class="text-green-600">✓ {{ $conversation->admin->name }}</span>
                                    @else
                                        <span class="text-orange-600">⚠ Unassigned</span>
                                    @endif
                                </div>
                                
                                <!-- Last Message Preview -->
                                @if($conversation->last_message)
                                    <div class="text-sm text-gray-500 truncate" style="max-width: 200px;">
                                        <span class="font-medium">{{ $conversation->last_message_sender }}:</span>
                                        {{ Str::limit($conversation->last_message, 30) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Conversations</h3>
                    <p class="text-gray-500">New conversations will appear here when customers start chatting.</p>
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
        
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" 
             wire:click.self="closeModal"
             x-data="{ sending: false }"
             @keydown.escape.window="$wire.closeModal()"
             style="z-index: 9999;">
            
            <div class="bg-white w-full flex flex-col" 
                 style="max-width: 700px; height: 600px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
                
                <!-- Modal Header (Blue) -->
                <div class="px-4 py-4 border-b border-blue-200" 
                     style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-white font-medium">
                                {{ strtoupper(substr($selectedConversation->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">{{ $selectedConversation->user->name }}</h3>
                                <p class="text-sm text-blue-100">{{ $selectedConversation->user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if(!$selectedConversation->admin_id)
                                <button wire:click="assignToMe({{ $selectedConversationId }})"
                                        class="px-3 py-1 bg-white bg-opacity-20 text-white text-sm rounded hover:bg-opacity-30">
                                    Assign
                                </button>
                            @endif
                            
                            <button wire:click="closeConversation({{ $selectedConversationId }})"
                                    class="px-3 py-1 bg-red-500 bg-opacity-80 text-white text-sm rounded hover:bg-opacity-100">
                                Close
                            </button>
                            
                            <button wire:click="closeModal" 
                                    class="p-2 text-white hover:text-blue-100 rounded hover:bg-white hover:bg-opacity-10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4" 
                     style="background-color: #f8fafc;"
                     id="messages-container"
                     x-init="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
                    
                    <div class="space-y-4">
                        @forelse($messages as $message)
                            <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                                <div style="max-width: 400px;">
                                    @if($message->sender_type === 'admin')
                                        <!-- Admin Message (Blue) -->
                                        <div style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; padding: 12px 16px; border-radius: 18px; border-bottom-right-radius: 4px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);">
                                            <p style="margin: 0; line-height: 1.4;">{{ $message->message }}</p>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 11px; color: rgba(255, 255, 255, 0.8);">
                                                <span>{{ $message->created_at->format('H:i') }}</span>
                                                <span>{{ $message->sender->name }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <!-- User Message (White) -->
                                        <div style="background: white; color: #374151; padding: 12px 16px; border-radius: 18px; border-bottom-left-radius: 4px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                                            <p style="margin: 0; line-height: 1.4;">{{ $message->message }}</p>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 11px; color: #6b7280;">
                                                <span>{{ $message->created_at->format('H:i') }}</span>
                                                <span>{{ $message->sender->name }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <p class="font-medium text-gray-700">No messages yet</p>
                                <p class="text-sm text-gray-500">Start the conversation!</p>
                            </div>
                        @endforelse
                        
                        <!-- Sending Indicator -->
                        <div x-show="sending" x-transition class="flex justify-end">
                            <div style="max-width: 400px;">
                                <div style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; padding: 12px 16px; border-radius: 18px; border-bottom-right-radius: 4px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3); opacity: 0.7;">
                                    <p style="margin: 0; line-height: 1.4;">
                                        <span>Sending...</span>
                                        <span class="animate-pulse">●●●</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200 bg-white" 
                     style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <input wire:model.live="message"
                                   @keydown.enter="
                                       if ($wire.message.trim()) {
                                           sending = true;
                                           $wire.sendMessage().then(() => {
                                               sending = false;
                                           }).catch(() => {
                                               sending = false;
                                           });
                                       }
                                   "
                                   type="text"
                                   placeholder="Type your message..."
                                   style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 25px; outline: none; transition: all 0.2s;"
                                   onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                                   onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                                   x-init="setTimeout(() => $el.focus(), 200)"
                                   :disabled="sending">
                            
                            <!-- Live character count -->
                            @if(!empty($this->message))
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ strlen((string) $this->message) }} characters
                                </div>
                            @endif
                        </div>
                        
                        <button @click="
                                    if ($wire.message.trim()) {
                                        sending = true;
                                        $wire.sendMessage().then(() => {
                                            sending = false;
                                        }).catch(() => {
                                            sending = false;
                                        });
                                    }
                                "
                                style="min-width: 80px; padding: 12px 20px; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border: none; border-radius: 25px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);"
                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.4)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)'"
                                :disabled="!$wire.message.trim() || sending"
                                x-text="sending ? 'Sending...' : 'Send'"
                                :style="(!$wire.message.trim() || sending) && 'opacity: 0.5; cursor: not-allowed;'">
                        </button>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-2 text-center">
                        Press Enter to send • Esc to close
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <script>
        console.log('Chat script loaded');
        
        // Auto scroll to bottom when messages update
        document.addEventListener('livewire:updated', function () {
            console.log('Livewire updated');
            const container = document.getElementById('messages-container');
            if (container) {
                console.log('Scrolling to bottom');
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 150);
            }
        });
    </script>
</x-filament-panels::page>