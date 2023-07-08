<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;


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

Route::get('/blogs', [BlogController::class, 'index']) -> name('blogs.index');
Route::get('/blogs/{blog}', [BlogController::class, 'show']) -> name('blogs.show');
Route::post('/blogs', [BlogController::class, 'store']) -> name('blogs.store');
Route::put('/blogs/{blog}', [BlogController::class, 'update']) -> name('blogs.update');
Route::delete('/blogs/{blog}', [BlogController::class, 'destroy']) -> name('blogs.destroy');


// Atau bisa disingkat dengan code di bawah
// Route::resource('blogs', BlogController::class);
