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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text');
            $table->json('metadata')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['conversation_id', 'is_read']);
            $table->index(['sender_id']);
            $table->index(['type']);
            $table->index(['is_read']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};