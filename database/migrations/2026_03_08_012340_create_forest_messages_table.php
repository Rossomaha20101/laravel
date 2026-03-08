<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forest_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('forest_users')->onDelete('cascade');
            $table->string('content', 128); // Макс 128 символов
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forest_messages');
    }
};