<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forest_users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Имя (обязательно)
            $table->string('nickname')->nullable(); // Прозвище
            $table->foreignId('animal_type_id')->constrained(); // Вид животного
            $table->enum('gender', ['M', 'F']); // Пол
            $table->date('birth_date'); // Дата рождения
            $table->string('best_friend_name'); // Имя лучшего друга
            $table->string('email')->unique(); // Email для входа
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forest_users');
    }
};