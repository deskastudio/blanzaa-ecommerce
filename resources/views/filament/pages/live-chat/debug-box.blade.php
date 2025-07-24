{{-- Debug Box Component --}}
{{-- File: resources/views/filament/pages/live-chat/debug-box.blade.php --}}

<div style="position: absolute; top: 10px; right: 10px; background: lime; color: black; padding: 5px; font-size: 10px; z-index: 10000; border-radius: 4px; font-family: monospace;">
    🔄 Conv ID: {{ $selectedConversationId }} | {{ $messages->count() ?? 0 }} msgs | {{ now()->format('H:i:s') }}
</div>