<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Accessible à tous
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Seulement accessible via le JWT
Route::middleware('auth:api')->group(function () {
    // user current and logout
    Route::get('/currentuser', [UserController::class, 'currentUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // user edit update delete
    Route::get('user', [CategoryController::class, 'index']);
    Route::get('user/{id}', [CategoryController::class, 'show']);
    Route::post('user/{id}', [CategoryController::class, 'update']);
    Route::delete('user/{id}', [CategoryController::class, 'delete']);

    // role
    Route::get(
        'role',
        [
            CategoryController::class,
            'index'
        ]
    );
    Route::get('role/{id}', [CategoryController::class, 'show']);
    Route::post('role', [CategoryController::class, 'store']);
    Route::delete('role/{id}', [CategoryController::class, 'delete']);
    Route::post('role/{id}', [CategoryController::class, 'update']);

    // company
    Route::get('company', [CategoryController::class, 'index']);
    Route::get('company/{id}', [CategoryController::class, 'show']);
    Route::post('company', [CategoryController::class, 'store']);
    Route::delete('company/{id}', [CategoryController::class, 'delete']);
    Route::post('company/{id}', [CategoryController::class, 'update']);

    // product
    Route::get('product', [CategoryController::class, 'index']);
    Route::get('product/{id}', [CategoryController::class, 'show']);
    Route::post('product', [CategoryController::class, 'store']);
    Route::delete('product/{id}', [CategoryController::class, 'delete']);
    Route::post('product/{id}', [CategoryController::class, 'update']);

    // category
    Route::get('category', [CategoryController::class, 'index']);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::delete('category/{id}', [CategoryController::class, 'delete']);
    Route::post('category/{id}', [CategoryController::class, 'update']);

    // tag
    Route::get('tag', [CategoryController::class, 'index']);
    Route::get('tag/{id}', [CategoryController::class, 'show']);
    Route::post('tag', [CategoryController::class, 'store']);
    Route::delete('tag/{id}', [CategoryController::class, 'delete']);
    Route::post('tag/{id}', [CategoryController::class, 'update']);

    // chat
    Route::get(
        'chat',
        [
            CategoryController::class,
            'index'
        ]
    );
    Route::get('chat/{id}', [CategoryController::class, 'show']);
    Route::post('chat', [CategoryController::class, 'store']);
    Route::delete('chat/{id}', [CategoryController::class, 'delete']);
    Route::post('chat/{id}', [CategoryController::class, 'update']);

    // message
    Route::get('message', [CategoryController::class, 'index']);
    Route::get('message/{id}', [CategoryController::class, 'show']);
    Route::post('message', [CategoryController::class, 'store']);
    Route::delete('message/{id}', [CategoryController::class, 'delete']);
    Route::post('message/{id}', [CategoryController::class, 'update']);

    // ticket
    Route::get('ticket', [CategoryController::class, 'index']);
    Route::get('ticket/{id}', [CategoryController::class, 'show']);
    Route::post('ticket', [CategoryController::class, 'store']);
    Route::delete('ticket/{id}', [CategoryController::class, 'delete']);
    Route::post('ticket/{id}', [CategoryController::class, 'update']);
});