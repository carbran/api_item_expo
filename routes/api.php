<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UsefulController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix'     => 'auth',

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});

Route::group([

    'middleware' => 'api',
    'prefix'     => 'user',

], function ($router) {

    Route::post('register', [UserController::class, 'register']);
    Route::post('update', [UserController::class, 'update']);
    Route::post('update-password', [UserController::class, 'updatePassword']);
    Route::post('get-access-code', [UserController::class, 'getAccessCode']);
    Route::post('update-password-ac', [UserController::class, 'updatePasswordWithAccessCode']);

});

Route::post('version', [UsefulController::class, 'version']);

Route::middleware(['api', 'jwt.auth'])->group(function () {
    Route::apiResource('collections', CollectionController::class);
});

Route::middleware(['api', 'jwt.auth'])->group(function () {
    Route::apiResource('items', ItemController::class);
});

Route::middleware(['api', 'jwt.auth'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
});

