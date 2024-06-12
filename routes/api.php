<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\AuthController;
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
Route::get('/me', [AuthController::class, 'index'])->middleware('auth:sanctum');

Route::get('/comment/list', [CommentController::class, 'index']);
Route::post('/comment/create', [CommentController::class, 'store']);
Route::get('/comment/show/{id}', [CommentController::class, 'show']);
Route::put('/comment/update/{id}', [CommentController::class, 'update']); 
Route::delete('/comment/delete/{id}', [CommentController::class, 'destroy']);
