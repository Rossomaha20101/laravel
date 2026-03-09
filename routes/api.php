<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ForestAuthController;
use App\Http\Controllers\Api\ForestUserController;
use App\Http\Controllers\Api\ForestFriendController;
use App\Http\Controllers\Api\ForestMessageController;

/*
|--------------------------------------------------------------------------
| API Routes для социальной сети «Волшебный лес»
|--------------------------------------------------------------------------
|
| Все маршруты по умолчанию используют префикс /api
| Middleware 'auth:sanctum' защищает маршруты, требующие аутентификации
|
*/

// 🌲 Публичные маршруты (регистрация/вход)
Route::post('/register', [ForestAuthController::class, 'register'])->name('api.register');
Route::post('/login', [ForestAuthController::class, 'login'])->name('api.login');

// 🔐 Защищённые маршруты (требуют токен Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Выход из системы
    Route::post('/logout', [ForestAuthController::class, 'logout'])->name('api.logout');
    
    // Профиль текущего пользователя
    Route::get('/user', [ForestUserController::class, 'profile'])->name('api.user');
    
    // Рекомендации друзей (по виду животного + полу)
    Route::get('/recommendations', [ForestUserController::class, 'recommendations'])->name('api.recommendations');
    
    // 🤝 Друзья
    Route::get('/friends', [ForestFriendController::class, 'index'])->name('api.friends.index');
    Route::get('/friends/requests', [ForestFriendController::class, 'requests'])->name('api.friends.requests');
    Route::post('/friends/{id}', [ForestFriendController::class, 'add'])->name('api.friends.add');
    Route::post('/friends/{id}/accept', [ForestFriendController::class, 'accept'])->name('api.friends.accept');
    Route::delete('/friends/{id}', [ForestFriendController::class, 'remove'])->name('api.friends.remove');
    
    // 💬 Сообщения
    Route::post('/messages', [ForestMessageController::class, 'store'])->name('api.messages.store');
    Route::get('/messages', [ForestMessageController::class, 'index'])->name('api.messages.index');
});