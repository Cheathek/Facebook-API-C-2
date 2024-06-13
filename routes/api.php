<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
     
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'index']);
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot');
        Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
    });
});

// Post
Route::prefix('post')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [PostController::class, 'index'])->name('post.list');
    Route::post('/create', [PostController::class, 'store'])->name('post.create');
    Route::put('/update/{id}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/delete/{id}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/show/{id}', [PostController::class, 'show'])->name('post.show');
});

// Friends
Route::prefix('friend')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [FriendController::class, 'index'])->name('friend.list');
    Route::get('/list/friend-list/{id}', [UserController::class, 'show'])->name('friend.show');
    Route::get('/request/list', [FriendController::class, 'indexRequest'])->name('friend.list');
    Route::delete('/request/remove', [FriendController::class, 'removeFriendRequest'])->name('friend.remove');
    Route::post('/confirm', [FriendController::class, 'confirm'])->name('friend.comfirm');
    Route::post('/request', [FriendController::class, 'store'])->name('friend.create');
    Route::put('/update/{id}', [FriendController::class, 'update'])->name('friend.update');
    Route::delete('/cancel /{id}', [FriendController::class, 'destroy'])->name('friend.destroy');
    Route::get('/show/{id}', [FriendController::class, 'show'])->name('friend.show');
});