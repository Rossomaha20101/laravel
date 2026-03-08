<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BasicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Web\ForestAuthController; // ← Ваш контроллер для регистрации ForestUser
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| СОЦСЕТЬ «ВОЛШЕБНЫЙ ЛЕС» — Пользователи (ForestUser)
|--------------------------------------------------------------------------
*/

// Регистрация (именно эта будет вызываться route('register'))
Route::get('/register', [ForestAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [ForestAuthController::class, 'register']);

// Вход для пользователей соцсети
Route::get('/login', [ForestAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [ForestAuthController::class, 'login']);

// Выход из соцсети
Route::post('/logout', function () {
    Auth::guard('forest')->logout();
    return redirect('/');
})->name('forest.logout')->middleware('auth.forest');

// Страница социальной сети (публичная)
Route::view('/social', 'social.index')->name('social.index');

// Защищённые маршруты соцсети (только для авторизованных ForestUser)
Route::middleware(['auth.forest'])->group(function () {
    Route::get('/forest/dashboard', function () {
        $user = Auth::guard('forest')->user();
        return view('forest.dashboard', compact('user'));
    })->name('forest.dashboard');
    
    // Сюда можно добавить маршруты друзей, сообщений и т.д. для веб-версии
});

/*
|--------------------------------------------------------------------------
| ОБЫЧНЫЕ СТРАНИЦЫ САЙТА (публичные)
|--------------------------------------------------------------------------
*/
Route::get('/', [BasicController::class, 'index'])->name('home');
Route::get('/about-us', [BasicController::class, 'about'])->name('about');
Route::get('/contact', [BasicController::class, 'contact'])->name('contact');
Route::post('/contact', [BasicController::class, 'submit'])->name('contact.post');

Route::get('/posts', [PostController::class, 'index'])->name('posts');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.one');
Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.one.edit');
Route::post('/posts/{id}/edit', [PostController::class, 'update'])->name('posts.edit');
Route::get('/posts/{id}/delete', [PostController::class, 'delete'])->name('posts.one.delete');
