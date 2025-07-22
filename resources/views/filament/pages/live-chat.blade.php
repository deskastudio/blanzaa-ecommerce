<x-filament-panels::page>
    <!-- Main Page: Conversations List -->
    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Chat Conversations</h2>
            <p class="text-sm text-gray-600 mt-1">Click on any conversation to start chatting</p>
        </div>
        
        <!-- Conversations List -->
        <div class="divide-y divide-gray-200">
            @forelse($this->getActiveConversations() as $conversation)
                <div wire:click="selectConversation({{ $conversation->id }})" 
                     class="p-4 hover:bg-gray-50 cursor-pointer transition-colors">
                    
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium flex-shrink-0">
                            {{ strtoupper(substr($conversation->user->name, 0, 2)) }}
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-medium text-gray-900 truncate">
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
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $conversation->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $conversation->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $conversation->status === 'resolved' ? 'bg-blue-100 text-blue-700' : '' }}">
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
                                @php
                                    $lastMessage = $conversation->messages()->latest()->first();
                                @endphp
                                @if($lastMessage)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">
                                        <span class="font-medium">{{ $lastMessage->sender->name }}:</span>
                                        {{ Str::limit($lastMessage->message, 30) }}
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
    
    <!-- Chat Modal (Smaller & Styled) -->
    @if($selectedConversationId)
        @php
            $selectedConversation = $this->getActiveConversations()->where('id', $selectedConversationId)->first();
            $messages = $this->getSelectedMessages();
        @endphp
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4" 
             wire:click.self="closeModal"
             x-data
             x-init="console.log('Modal opened')"
             @keydown.escape.window="$wire.closeModal()">
            
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl h-[600px] flex flex-col transform transition-all duration-200 scale-100">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-md">
                                {{ strtoupper(substr($selectedConversation->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $selectedConversation->user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $selectedConversation->user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if(!$selectedConversation->admin_id)
                                <button wire:click="assignToMe({{ $selectedConversationId }})"
                                        class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors shadow-sm">
                                    Assign
                                </button>
                            @endif
                            
                            <button wire:click="closeConversation({{ $selectedConversationId }})"
                                    class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors shadow-sm">
                                Close
                            </button>
                            
                            <button wire:click="closeModal" 
                                    class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" 
                     id="messages-container"
                     x-init="$el.scrollTop = $el.scrollHeight">
                    <div class="space-y-3">
                        @forelse($messages as $message)
                            <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs">
                                    <div class="px-4 py-2 rounded-2xl shadow-sm
                                        {{ $message->sender_type === 'admin' 
                                            ? 'bg-blue-500 text-white' 
                                            : 'bg-white text-gray-900 border border-gray-200' }}">
                                        
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <div class="flex items-center justify-between mt-1 text-xs
                                            {{ $message->sender_type === 'admin' ? 'text-blue-100' : 'text-gray-500' }}">
                                            <span>{{ $message->created_at->format('H:i') }}</span>
                                            <span>{{ $message->sender->name }}</span>
                                        </div>
                                    </div>
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
                                <p class="text-sm">Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200 bg-white rounded-b-xl">
                    <div class="flex items-end space-x-3">
                        <div class="flex-1">
                            <input wire:model.live="message"
                                   @keydown.enter="$wire.sendMessage()"
                                   type="text"
                                   placeholder="Type your message..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   x-init="$el.focus()">
                            
                            <!-- Live character count -->
                            @if(strlen($message) > 0)
                                <div class="text-xs text-gray-500 mt-1 ml-4">
                                    {{ strlen($message) }} characters
                                </div>
                            @endif
                        </div>
                        
                        <button wire:click="sendMessage"
                                class="w-10 h-10 bg-blue-500 text-white rounded-full hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center shadow-md"
                                {{ empty(trim($message)) ? 'disabled' : '' }}>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
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
                }, 100);
            }
        });
        
        // Focus input when modal opens
        document.addEventListener('livewire:updated', function () {
            if (@json($selectedConversationId)) {
                const input = document.querySelector('input[wire\\:model\\.live="message"]');
                if (input) {
                    console.log('Focusing input');
                    setTimeout(() => input.focus(), 200);
                }
            }
        });
    </script>
</x-filament-panels::page>