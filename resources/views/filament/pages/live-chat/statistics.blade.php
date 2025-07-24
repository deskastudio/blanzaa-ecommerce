{{-- Statistics Component --}}
{{-- File: resources/views/filament/pages/live-chat/statistics.blade.php --}}

@php $stats = $this->getConversationStats(); @endphp

<div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center;">
        <div class="stat-item">
            <div style="font-size: 24px; font-weight: bold; color: #3b82f6; margin-bottom: 4px;">
                {{ $stats['total_active'] ?? 0 }}
            </div>
            <div style="font-size: 14px; color: #6b7280;">Active</div>
        </div>
        
        <div class="stat-item">
            <div style="font-size: 24px; font-weight: bold; color: #f59e0b; margin-bottom: 4px;">
                {{ $stats['total_pending'] ?? 0 }}
            </div>
            <div style="font-size: 14px; color: #6b7280;">Pending</div>
        </div>
        
        <div class="stat-item">
            <div style="font-size: 24px; font-weight: bold; color: #10b981; margin-bottom: 4px;">
                {{ $stats['my_assigned'] ?? 0 }}
            </div>
            <div style="font-size: 14px; color: #6b7280;">My Assigned</div>
        </div>
        
        <div class="stat-item">
            <div style="font-size: 24px; font-weight: bold; color: #ef4444; margin-bottom: 4px;">
                {{ $stats['unread_total'] ?? 0 }}
            </div>
            <div style="font-size: 14px; color: #6b7280;">Unread</div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .stat-item {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 480px) {
        .stat-item {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
    }
</style>