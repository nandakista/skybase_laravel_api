<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/profile/update', [UserController::class, 'updateProfile']);
        Route::put('/change-password', [UserController::class, 'updatePassword']);
        Route::delete('/delete-account', [UserController::class, 'deleteAccount']);
    });

    Route::get('/article', [ArticleController::class, 'index']);
    Route::get('/article/{id}', [ArticleController::class, 'detail']);
    Route::put('/article/{id}', [ArticleController::class, 'update']);
    Route::post('/article', [ArticleController::class, 'add']);
    Route::delete('/article/{id}', [ArticleController::class, 'delete']);

    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/sub-category', [SubCategoryController::class, 'index']);
});
