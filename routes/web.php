<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BasicController;
use App\Http\Controllers\PostController;


Route::get('/', [BasicController::class, 'index'])->name('home');

Route::get('/about-us', [BasicController::class, 'about'])->name('about');

Route::get('/contact', [BasicController::class, 'contact'])->name('contact');

Route::post('/contact', [BasicController::class, 'submit'])->name('contact.post');

Route::get('/posts', [PostController::class, 'index'])->name('posts');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.one');
Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.one.edit');
Route::post('/posts/{id}/edit', [PostController::class, 'update'])->name('posts.edit');
Route::get('/posts/{id}/delete', [PostController::class, 'delete'])->name('posts.one.delete');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Страница социальной сети для жителей леса
Route::view('/social', 'social.index')->name('social.index');

require __DIR__.'/auth.php';
