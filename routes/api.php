<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\LikeCommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LikePostController;
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

Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');



Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot');
    Route::put('/password/update', [AuthController::class, 'resetPassword'])->name('password.changed');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::post('/reset', [AuthController::class, 'updatePassword']);
        Route::put('/update', [AuthController::class, 'updateInformation']);
        Route::post('/update/profile', [AuthController::class, 'updateProfile']);
    });
});

// Post
Route::prefix('post')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [PostController::class, 'index'])->name('post.list');
    Route::post('/create', [PostController::class, 'store'])->name('post.create');
    Route::post('/update/image/{id}', [PostController::class, 'updateImage'])->name('post.updateImage');
    Route::put('/update/{id}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/delete/{id}', [PostController::class, 'deletePost'])->name('post.destroy');
    Route::get('/show/{id}', [PostController::class, 'showPost'])->name('post.show');
});

// Friends
Route::prefix('friend')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [FriendController::class, 'friendList'])->name('friend.list');
    Route::get('/list/friend-list/{id}', [UserController::class, 'showFriend'])->name('friend.show');
    Route::get('/request/list', [FriendController::class, 'indexRequest'])->name('friend.list');
    Route::delete('/request/remove', [FriendController::class, 'removeFriendRequest'])->name('friend.remove');
    Route::post('/confirm', [FriendController::class, 'confirm'])->name('friend.comfirm');
    Route::post('/request', [FriendController::class, 'storeFriend'])->name('friend.create');
    Route::delete('/cancel /{id}', [FriendController::class, 'destroy'])->name('friend.destroy');
});

// Like post
Route::prefix('like/post')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [LikePostController::class, 'listLikePost'])->name('like.list');
    Route::post('/create', [LikePostController::class, 'likePost'])->name('like.create');
    Route::delete('/delete/{id}', [LikePostController::class, 'unlikePost'])->name('like.delete');
    Route::put('/update/reach/{id}', [LikePostController::class, 'updateReach'])->name('like.update');

});

// like comment
Route::prefix('like/comment')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [LikeCommentController::class, 'listLikeComment'])->name('like.list');
    Route::post('/create', [LikeCommentController::class, 'likeComment'])->name('like.create');
    Route::delete('/delete/{id}', [LikeCommentController::class, 'UnlikeComment'])->name('like.delete');
    Route::put('/update/reach/{id}', [LikeCommentController::class, 'updateReachComment'])->name('like.update');

});

// comment
Route::prefix('comment')->middleware('auth:sanctum')->group(function () {
    Route::get('/list', [CommentController::class, 'listComment']);
    Route::post('/create', [CommentController::class, 'storeComment']);
    Route::put('/update/{id}', [CommentController::class, 'updateComment']);
    Route::delete('/delete/{id}', [CommentController::class, 'destroyComment']);

});
