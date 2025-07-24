{{-- Empty State Component --}}
{{-- File: resources/views/filament/pages/live-chat/empty-state.blade.php --}}

<div style="text-align: center; padding: 60px 20px; background: white;">
    <div style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; font-size: 32px; color: #9ca3af;">
        💬
    </div>
    <h3 style="font-size: 18px; font-weight: 500; color: #111827; margin: 0 0 8px 0;">No Active Conversations</h3>
    <p style="color: #6b7280; margin: 0 0 16px 0;">New conversations will appear here when customers start chatting.</p>
    <button onclick="location.reload()" 
            style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s ease;"
            onmouseover="this.style.background='#2563eb'"
            onmouseout="this.style.background='#3b82f6'">
        Refresh
    </button>
</div>