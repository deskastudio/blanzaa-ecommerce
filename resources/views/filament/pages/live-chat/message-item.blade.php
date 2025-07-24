{{-- Single Message Item --}}
{{-- File: resources/views/filament/pages/live-chat/message-item.blade.php --}}

<div class="message-wrapper" 
     style="margin-bottom: 16px; display: flex; {{ $message->sender_type === 'admin' ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}" 
     data-message-id="{{ $message->id }}">
    <div style="max-width: 400px;">
        @if($message->sender_type === 'admin')
            {{-- Admin Message --}}
            <div style="background: #3b82f6; color: white; padding: 12px 16px; border-radius: 18px 18px 4px 18px; box-shadow: 0 2px 8px rgba(59,130,246,0.3);">
                <p style="margin: 0; line-height: 1.4; word-wrap: break-word;">{{ $message->message }}</p>
                <div style="font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 4px; display: flex; justify-content: space-between; gap: 8px;">
                    <span>{{ $message->created_at->format('H:i') }}</span>
                    <span>{{ $message->sender->name }}</span>
                </div>
            </div>
        @else
            {{-- User Message --}}
            <div style="background: white; color: #374151; padding: 12px 16px; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <p style="margin: 0; line-height: 1.4; word-wrap: break-word;">{{ $message->message }}</p>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px; display: flex; justify-content: space-between; gap: 8px;">
                    <span>{{ $message->created_at->format('H:i') }}</span>
                    <span>{{ $message->sender->name }}</span>
                </div>
            </div>
        @endif
    </div>
</div>