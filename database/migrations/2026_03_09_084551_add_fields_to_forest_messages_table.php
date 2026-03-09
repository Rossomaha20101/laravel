<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forest_messages', function (Blueprint $table) {
            // Добавляем получателя (для личных сообщений)
            $table->unsignedBigInteger('recipient_id')->nullable()->after('sender_id');
            $table->foreign('recipient_id')->references('id')->on('forest_users')->onDelete('cascade');
            
            // Добавляем групповой чат
            $table->unsignedBigInteger('group_id')->nullable()->after('recipient_id');
            
            // Тип сообщения
            $table->enum('type', ['personal', 'group'])->default('personal')->after('group_id');
            
            // Индексы
            $table->index(['sender_id', 'recipient_id']);
            $table->index(['group_id']);
        });
    }

    public function down(): void
    {
        Schema::table('forest_messages', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropIndex(['sender_id', 'recipient_id']);
            $table->dropIndex(['group_id']);
            $table->dropColumn(['recipient_id', 'group_id', 'type']);
        });
    }
};