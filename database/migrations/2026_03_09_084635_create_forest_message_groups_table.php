<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица групп
        Schema::create('forest_message_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('forest_users')->onDelete('cascade');
            $table->timestamps();
        });

        // Таблица участников групп
        Schema::create('forest_message_group_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('forest_message_groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('forest_users')->onDelete('cascade');
            
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forest_message_group_users');
        Schema::dropIfExists('forest_message_groups');
    }
};