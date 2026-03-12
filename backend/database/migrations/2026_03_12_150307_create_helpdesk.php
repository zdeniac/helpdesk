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
        Schema::create('helpdesk_articles', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_agent_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->string('status');

            $table->timestamps();
        });

        Schema::create('helpdesk_messages', function (Blueprint $table) {
            $table->id();

            $table->text('message');

            $table->foreignId('conversation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('sender_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_articles');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('helpdesk_messages');
    }
};
