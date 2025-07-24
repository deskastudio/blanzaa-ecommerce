{{-- Header Component - Improved Layout --}}
{{-- File: resources/views/filament/pages/live-chat/header.blade.php --}}

<div style="
    background: linear-gradient(90deg, #3b82f6, #1d4ed8); 
    color: white; 
    padding: 16px 20px; 
    border-radius: 8px 8px 0 0;
    position: relative;
    min-height: 60px;
">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
        <!-- Left side: Title and subtitle -->
        <div style="flex: 1; min-width: 0;">
            <h1 style="font-size: 18px; font-weight: 600; margin: 0; line-height: 1.2;">
                Live Chat Dashboard
            </h1>
            <p style="margin: 4px 0 0 0; opacity: 0.85; font-size: 14px; line-height: 1.2;">
                Manage customer conversations
            </p>
        </div>
        
        <!-- Right side: Status indicator -->
        <div style="flex-shrink: 0; display: flex; align-items: center; gap: 8px;">
            <div style="
                display: flex; 
                align-items: center; 
                gap: 6px; 
                background: rgba(255,255,255,0.1); 
                padding: 6px 12px; 
                border-radius: 20px;
                font-size: 12px;
            ">
                <div style="
                    width: 6px; 
                    height: 6px; 
                    background: #10b981; 
                    border-radius: 50%; 
                    animation: pulse 2s infinite;
                "></div>
                <span>Live</span>
            </div>
            
            <div style="
                background: rgba(255,255,255,0.1); 
                padding: 6px 12px; 
                border-radius: 20px;
                font-size: 12px;
            ">
                Auto-refresh: 3s
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1);
        }
        50% { 
            opacity: 0.5;
            transform: scale(1.2);
        }
    }

    /* Mobile responsive */
    @media (max-width: 640px) {
        div[style*="background: linear-gradient(90deg, #3b82f6, #1d4ed8)"] {
            padding: 12px 16px !important;
        }
        
        div[style*="display: flex; align-items: center; justify-content: space-between; gap: 16px;"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        div[style*="flex-shrink: 0; display: flex; align-items: center; gap: 8px;"] {
            align-self: flex-end !important;
        }
        
        h1 {
            font-size: 16px !important;
        }
        
        p {
            font-size: 13px !important;
        }
    }
</style>