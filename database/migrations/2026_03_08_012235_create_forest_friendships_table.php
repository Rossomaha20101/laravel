<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forest_friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forest_user_id')->constrained()->onDelete('cascade');
            $table->foreignId('friend_id')->constrained('forest_users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');
            $table->timestamps();
            $table->unique(['forest_user_id', 'friend_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forest_friendships');
    }
};