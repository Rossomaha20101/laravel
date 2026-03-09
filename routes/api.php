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
    
    // 🌟 Рекомендации друзей (алгоритм из 4 уровней)
    Route::get('/recommendations', [ForestUserController::class, 'recommendations'])->name('api.recommendations');
    
    // 🤝 Друзья
    Route::prefix('friends')->group(function () {
        Route::get('/', [ForestFriendController::class, 'index'])->name('api.friends.index');
        Route::get('/requests', [ForestFriendController::class, 'requests'])->name('api.friends.requests');
        Route::post('/{id}', [ForestFriendController::class, 'add'])->name('api.friends.add');
        Route::post('/{id}/accept', [ForestFriendController::class, 'accept'])->name('api.friends.accept');
        Route::delete('/{id}', [ForestFriendController::class, 'remove'])->name('api.friends.remove');
    });
    
    // 💬 Сообщения (личные)
    Route::prefix('messages')->group(function () {
        // Список диалогов
        Route::get('/conversations', [ForestMessageController::class, 'conversations'])->name('api.messages.conversations');
        
        // История переписки с пользователем: /api/messages?with={user_id}
        Route::get('/', [ForestMessageController::class, 'index'])->name('api.messages.index');
        
        // Отправить личное сообщение
        Route::post('/', [ForestMessageController::class, 'store'])->name('api.messages.store');
    });
    
    // 👥 Групповые чаты
    Route::prefix('messages/groups')->group(function () {
        // Мои группы
        Route::get('/', [ForestMessageController::class, 'myGroups'])->name('api.messages.groups.index');
        
        // Создать группу
        Route::post('/', [ForestMessageController::class, 'createGroup'])->name('api.messages.groups.create');
        
        // Сообщения конкретной группы + отправка в группу
        Route::get('/{id}', [ForestMessageController::class, 'getGroupMessages'])->name('api.messages.groups.show');
        Route::post('/{id}', [ForestMessageController::class, 'sendToGroup'])->name('api.messages.groups.send');
    });
});