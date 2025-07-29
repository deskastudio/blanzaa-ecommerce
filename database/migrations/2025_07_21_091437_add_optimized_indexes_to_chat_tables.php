<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to chat_conversations table
        Schema::table('chat_conversations', function (Blueprint $table) {
            if (!$this->hasIndex('chat_conversations', 'idx_user_status')) {
                $table->index(['user_id', 'status'], 'idx_user_status');
            }
            
            if (!$this->hasIndex('chat_conversations', 'idx_admin_status')) {
                $table->index(['admin_id', 'status'], 'idx_admin_status');
            }
            
            if (!$this->hasIndex('chat_conversations', 'idx_product_status')) {
                $table->index(['product_id', 'status'], 'idx_product_status');
            }
            
            if (!$this->hasIndex('chat_conversations', 'idx_last_message_status')) {
                $table->index(['last_message_at', 'status'], 'idx_last_message_status');
            }
        });

        // Add indexes to chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!$this->hasIndex('chat_messages', 'idx_conv_created')) {
                $table->index(['conversation_id', 'created_at'], 'idx_conv_created');
            }
            
            if (!$this->hasIndex('chat_messages', 'idx_sender_type')) {
                $table->index(['sender_id', 'type'], 'idx_sender_type');
            }
            
            if (!$this->hasIndex('chat_messages', 'idx_type')) {
                $table->index('type', 'idx_type');
            }
            
            if (!$this->hasIndex('chat_messages', 'idx_is_read_conv')) {
                $table->index(['is_read', 'conversation_id'], 'idx_is_read_conv');
            }
            
            if (!$this->hasIndex('chat_messages', 'idx_conv_id_created')) {
                $table->index(['conversation_id', 'id', 'created_at'], 'idx_conv_id_created');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            if ($this->hasIndex('chat_conversations', 'idx_user_status')) {
                $table->dropIndex('idx_user_status');
            }
            if ($this->hasIndex('chat_conversations', 'idx_admin_status')) {
                $table->dropIndex('idx_admin_status');
            }
            if ($this->hasIndex('chat_conversations', 'idx_product_status')) {
                $table->dropIndex('idx_product_status');
            }
            if ($this->hasIndex('chat_conversations', 'idx_last_message_status')) {
                $table->dropIndex('idx_last_message_status');
            }
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            if ($this->hasIndex('chat_messages', 'idx_conv_created')) {
                $table->dropIndex('idx_conv_created');
            }
            if ($this->hasIndex('chat_messages', 'idx_sender_type')) {
                $table->dropIndex('idx_sender_type');
            }
            if ($this->hasIndex('chat_messages', 'idx_type')) {
                $table->dropIndex('idx_type');
            }
            if ($this->hasIndex('chat_messages', 'idx_is_read_conv')) {
                $table->dropIndex('idx_is_read_conv');
            }
            if ($this->hasIndex('chat_messages', 'idx_conv_id_created')) {
                $table->dropIndex('idx_conv_id_created');
            }
        });
    }

    /**
     * Check if index exists on table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }

    /**
     * Check if column exists on table
     */
    private function hasColumn(string $table, string $columnName): bool
    {
        return Schema::hasColumn($table, $columnName);
    }
};