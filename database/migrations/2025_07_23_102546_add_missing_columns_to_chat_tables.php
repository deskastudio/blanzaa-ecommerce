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
        // Tambahkan kolom yang missing di chat_conversations
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('last_message_at');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('status');
        });

        // Tambahkan index yang lebih optimal untuk admin queries
        Schema::table('chat_conversations', function (Blueprint $table) {
            // Index untuk admin dashboard - status + last_message_at untuk sorting
            $table->index(['status', 'last_message_at'], 'idx_admin_dashboard');
            
            // Index untuk assigned conversations
            $table->index(['admin_id', 'status'], 'idx_admin_assigned');
        });

        // Tambahkan index untuk unread count query di chat_messages
        Schema::table('chat_messages', function (Blueprint $table) {
            // Index untuk unread messages query yang sering dipakai
            $table->index(['conversation_id', 'sender_id', 'is_read'], 'idx_unread_messages');
            
            // Index untuk messages ordering
            $table->index(['conversation_id', 'created_at'], 'idx_conversation_timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['closed_at', 'priority']);
            $table->dropIndex('idx_admin_dashboard');
            $table->dropIndex('idx_admin_assigned');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('idx_unread_messages');
            $table->dropIndex('idx_conversation_timeline');
        });
    }
};