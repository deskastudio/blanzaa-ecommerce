{{-- Avatar Component --}}
{{-- File: resources/views/filament/pages/live-chat/avatar.blade.php --}}

<div style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; flex-shrink: 0; background: {{ $status === 'active' ? '#10b981' : '#f59e0b' }};">
    {{ $initials }}
</div>