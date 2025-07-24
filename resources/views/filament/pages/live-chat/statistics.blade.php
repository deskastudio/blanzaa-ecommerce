{{-- Statistics Component - More Compact --}}
{{-- File: resources/views/filament/pages/live-chat/statistics.blade.php --}}

@php $stats = $this->getConversationStats(); @endphp

<div style="
    background: white; 
    border-radius: 8px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    padding: 16px; 
    margin-bottom: 16px;
">
    <div style="
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 16px; 
        text-align: center;
    ">
        <div class="stat-item" style="padding: 8px;">
            <div style="
                font-size: 20px; 
                font-weight: bold; 
                color: #3b82f6; 
                margin-bottom: 2px;
                line-height: 1;
            ">
                {{ $stats['total_active'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1;">Active</div>
        </div>
        
        <div class="stat-item" style="padding: 8px;">
            <div style="
                font-size: 20px; 
                font-weight: bold; 
                color: #f59e0b; 
                margin-bottom: 2px;
                line-height: 1;
            ">
                {{ $stats['total_pending'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1;">Pending</div>
        </div>
        
        <div class="stat-item" style="padding: 8px;">
            <div style="
                font-size: 20px; 
                font-weight: bold; 
                color: #10b981; 
                margin-bottom: 2px;
                line-height: 1;
            ">
                {{ $stats['my_assigned'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1;">My Assigned</div>
        </div>
        
        <div class="stat-item" style="padding: 8px;">
            <div style="
                font-size: 20px; 
                font-weight: bold; 
                color: #ef4444; 
                margin-bottom: 2px;
                line-height: 1;
            ">
                {{ $stats['unread_total'] ?? 0 }}
            </div>
            <div style="font-size: 12px; color: #6b7280; line-height: 1;">Unread</div>
        </div>
    </div>
</div>

<style>
    .stat-item:hover {
        background: #f8fafc;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }

    @media (max-width: 768px) {
        div[style*="display: grid; grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
    }
    
    @media (max-width: 480px) {
        div[style*="display: grid; grid-template-columns: repeat(4, 1fr)"] {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        
        .stat-item div:first-child {
            font-size: 18px !important;
        }
        
        .stat-item div:last-child {
            font-size: 11px !important;
        }
    }
</style>