<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use \App\Http\Controllers\UserController;
use \App\Http\Controllers\UserAuthController;

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

Route::middleware('apiauth')->get('/user', function (Request $request) {
    return $request->user();
});

//Login class does not require API key authorisation
Route::post('user/login', [UserAuthController::class, 'login'])->name('user.login');

//These routes require user authentication via API key as bearer token
Route::middleware('apiauth')->group(function () {
    Route::post('user/register', [UserAuthController::class, 'register'])->name('user.register');

    Route::get('user/{user}', [UserController::class, 'get'])->name('user.get');
    Route::get('users', [UserController::class, 'index'])->name('user.index');

    Route::post('user/{user}/update', [UserController::class, 'update'])->name('user.update');
});