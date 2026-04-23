<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Guest routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ─── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Feeds
    Route::get('/', [PostController::class, 'index'])->name('feed');
    Route::get('/explore', [PostController::class, 'explore'])->name('explore');

    // Posts
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Likes (JSON endpoints — consumed by Vue components via axios)
    Route::post('/posts/{post}/like', [LikeController::class, 'togglePost'])->name('posts.like');
    Route::post('/comments/{comment}/like', [LikeController::class, 'toggleComment'])->name('comments.like');

    // Comments (POST returns JSON, DELETE returns JSON)
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Profile edit — POST used (with _method=PATCH) so file uploads work in multipart
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Follow
    Route::post('/users/{user:username}/follow', [FollowController::class, 'toggle'])->name('users.follow');
});

// ─── Public routes ─────────────────────────────────────────────────────────────
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/@{user:username}', [ProfileController::class, 'show'])->name('profile.show');
