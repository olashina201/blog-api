<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Bog
Route::post('/blog', [BlogController::class, 'create'])->middleware('auth:sanctum');
Route::get('/blogs', [BlogController::class, 'blogs']);
Route::get('/blog/{id}', [BlogController::class, 'getSingleBlog']);
Route::delete('/blog/{id}', [BlogController::class, 'destroy'])->middleware('auth:sanctum');
Route::put('/blog/{id}', [BlogController::class, 'update'])->middleware('auth:sanctum');

//Like and comment
Route::post('/blog/{id}/like', [BlogController::class, 'toggleLike'])->middleware('auth:sanctum');
Route::post('/blog/{id}/comment', [CommentController::class, 'createComment'])->middleware('auth:sanctum');
