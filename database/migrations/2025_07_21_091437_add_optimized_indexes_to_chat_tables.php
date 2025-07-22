<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah index untuk chat_messages
        Schema::table('chat_messages', function (Blueprint $table) {
            // Index untuk incremental loading (getMessages dengan after_id)
            $table->index(['conversation_id', 'id'], 'idx_conv_msg_id');
            
            // Index untuk count unread messages
            $table->index(['conversation_id', 'is_read'], 'idx_conv_unread');
            
            // Index untuk ordering messages
            $table->index('created_at', 'idx_msg_created');
        });

        // Tambah index untuk chat_conversations
        Schema::table('chat_conversations', function (Blueprint $table) {
            // Index untuk find active conversation per user
            $table->index(['user_id', 'status', 'updated_at'], 'idx_user_active_conv');
            
            // Index untuk admin dashboard ordering
            $table->index('updated_at', 'idx_conv_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('idx_conv_msg_id');
            $table->dropIndex('idx_conv_unread');
            $table->dropIndex('idx_msg_created');
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex('idx_user_active_conv');
            $table->dropIndex('idx_conv_updated');
        });
    }
};