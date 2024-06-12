<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/me', [AuthController::class, 'index'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/update-password', [AuthController::class, 'updatePassword']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot');
// Reset Password
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::get('/post/list',[PostController::class,'index'])->name('post.list');
Route::post('/post/create',[PostController::class,'store'])->name('post.create');
Route::put('/post/update/{id}',[PostController::class,'update'])->name('post.update');
Route::delete('/post/delete/{id}',[PostController::class,'destroy'])->name('post.destroy');
Route::get('/post/show/{id}',[PostController::class,'show'])->name('post.show');