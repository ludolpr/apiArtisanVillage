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
    Route::get('user', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('user/{id}', [CategoryController::class, 'show'])->can('view');
    Route::post('user/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('user/{id}', [CategoryController::class, 'delete'])->can('delete');

    // role
    Route::get(
        'role',
        [
            CategoryController::class,
            'index'
        ]
    )->can('viewAny');
    Route::get('role/{id}', [CategoryController::class, 'show'])->can('view');
    Route::post('role', [CategoryController::class, 'store'])->can('create');
    Route::post('role/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('role/{id}', [CategoryController::class, 'delete'])->can('delete');

    // company
    Route::get('company', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('company/{id}', [CategoryController::class, 'show'])->can('show');
    Route::post('company', [CategoryController::class, 'store'])->can('create');
    Route::post('company/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('company/{id}', [CategoryController::class, 'delete'])->can('delete');

    // product
    Route::get('product', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('product/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('product', [CategoryController::class, 'store'])->can('create');
    Route::post('product/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('product/{id}', [CategoryController::class, 'delete'])->can('delete');

    // category
    Route::get('category', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('category/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('category', [CategoryController::class, 'store'])->can('create');
    Route::post('category/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('category/{id}', [CategoryController::class, 'delete'])->can('delete');

    // tag
    Route::get('tag', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('tag/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('tag', [CategoryController::class, 'store'])->can('create');
    Route::post('tag/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('tag/{id}', [CategoryController::class, 'delete'])->can('delete');

    // chat
    Route::get(
        'chat',
        [
            CategoryController::class,
            'index'
        ]
    );
    Route::get('chat/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('chat', [CategoryController::class, 'store'])->can('create');
    Route::post('chat/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('chat/{id}', [CategoryController::class, 'delete'])->can('delete');

    // message
    Route::get('message', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('message/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('message', [CategoryController::class, 'store'])->can('create');
    Route::post('message/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('message/{id}', [CategoryController::class, 'delete'])->can('delete');

    // ticket
    Route::get('ticket', [CategoryController::class, 'index'])->can('viewAny');
    Route::get('ticket/{id}', [CategoryController::class, 'show'])->can('store');
    Route::post('ticket', [CategoryController::class, 'store'])->can('create');
    Route::post('ticket/{id}', [CategoryController::class, 'update'])->can('update');
    Route::delete('ticket/{id}', [CategoryController::class, 'delete'])->can('delete');
});