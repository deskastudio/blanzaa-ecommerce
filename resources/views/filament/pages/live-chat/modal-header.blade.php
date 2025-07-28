{{-- Modal Header Component --}}
{{-- File: resources/views/filament/pages/live-chat/modal-header.blade.php --}}

<div style="padding: 20px; background: #3b82f6; color: white; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
            {{ $selectedConversation->user->initials ?? strtoupper(substr($selectedConversation->user->name, 0, 2)) }}
        </div>
        <div>
            <h3 style="font-weight: 600; margin: 0;">{{ $selectedConversation->user->name }}</h3>
            <p style="font-size: 14px; opacity: 0.8; margin: 0;">{{ $selectedConversation->user->email }}</p>
        </div>
    </div>
    
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        @if(!$selectedConversation->admin_id)
            <button wire:click="assignToMe({{ $selectedConversationId }})" 
                    style="padding: 6px 12px; background: rgba(255,255,255,0.2); color: white; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s ease;"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                Assign to Me
            </button>
        @endif
        
        {{-- BUTTON CLOSE DIHAPUS --}}
        
        <button wire:click="closeModal" 
                style="padding: 8px; background: none; border: none; color: white; cursor: pointer; font-size: 18px; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: background-color 0.2s ease;"
                onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                onmouseout="this.style.background='none'"
                data-modal-close>×</button>
    </div>
</div>

<style>
    /* Mobile responsive header */
    @media (max-width: 480px) {
        div[style*="padding: 20px; background: #3b82f6"] {
            padding: 15px !important;
            flex-direction: column !important;
            gap: 12px !important;
            align-items: flex-start !important;
        }
        
        div[style*="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;"] {
            width: 100% !important;
            justify-content: flex-end !important;
            gap: 4px !important;
        }
    }
</style>