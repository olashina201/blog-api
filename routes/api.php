<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth;
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

Route::post('/register', [Auth::class, 'register']);
Route::post('/login', [Auth::class, 'login']);

// Bog
Route::post('/blog', [BlogController::class, 'create']);
Route::get('/blogs', [BlogController::class, 'blogs']);
Route::get('/blog/{id}', [BlogController::class, 'getSingleBlog']);
Route::delete('/blog/{id}', [BlogController::class, 'destroy']);
Route::put('/blog/{id}', [BlogController::class, 'update']);