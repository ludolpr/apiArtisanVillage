<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContactController;
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

// Route pour afficher la page de vérification des emails
route::get('/verify/email/{id}', [AuthController::class, 'verifyEmail'])->name('verify');

route::post('/contact', [ContactController::class, 'contactEmail'])->name('contact');


// route entreprise visible by all
Route::get('company', [CompanyController::class, 'index']);
Route::get('/company/latestid', [CompanyController::class, 'getLatestIds']);
Route::get(
    'company/{company}',
    [CompanyController::class, 'show']
);

// users route
Route::get('users', [UserController::class, 'index']);

// product route 
Route::get('product', [ProductController::class, 'index']);
Route::get('productstags', [ProductController::class, 'indexWithTags']);
Route::get(
    'product/{product}',
    [ProductController::class, 'show']
);
Route::get(
    'productstags/{product}',
    [ProductController::class, 'showWithTags']
);
// tag route 
Route::get('tag', [TagController::class, 'index']);
Route::get('tag/{tag}', [TagController::class, 'show']);
// category
Route::get('category', [CategoryController::class, 'index']);
Route::get('category/{category}', [CategoryController::class, 'show']);

// Seulement accessible via le JWT
Route::middleware('auth:api')->group(function () {
    // user current and logout
    Route::get('/currentuser', [UserController::class, 'currentUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // user edit update delete
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    // role
    Route::get(
        'role',
        [RoleController::class, 'index']
    );
    Route::get('role/{role}', [RoleController::class, 'show']);
    Route::post('role', [RoleController::class, 'store']);
    Route::put('role/{role}', [RoleController::class, 'update']);
    Route::delete('role/{role}', [RoleController::class, 'destroy']);

    // company
    Route::post('company', [CompanyController::class, 'store']);
    Route::put(
        'company/{company}',
        [CompanyController::class, 'update']
    );
    Route::delete('company/{company}', [CompanyController::class, 'destroy']);

    // product
   
    Route::post('product', [ProductController::class, 'store']);
    Route::put(
        'product/{product}',
        [ProductController::class, 'update']
    );
    Route::delete('product/{product}', [ProductController::class, 'destroy']);

    // category
    Route::post('category', [CategoryController::class, 'store']);
    Route::put('category/{category}', [CategoryController::class, 'update']);
    Route::delete('category/{category}', [CategoryController::class, 'destroy']);

    // tag
    Route::post('tag', [TagController::class, 'store']);
    Route::put('tag/{tag}', [TagController::class, 'update']);
    Route::delete('tag/{tag}', [TagController::class, 'destroy']);

    // chat
    Route::get(
        'chat',
        [ChatController::class, 'index']
    );
    Route::get('chat/{chat}', [ChatController::class, 'show']);
    Route::post('chat', [ChatController::class, 'store']);
    Route::put('chat/{chat}', [ChatController::class, 'update']);
    Route::delete('chat/{chat}', [ChatController::class, 'destroy']);

    // message
    Route::get('message', [MessageController::class, 'index']);
    Route::get(
        'message/{message}',
        [MessageController::class, 'show']
    );
    Route::post('message', [MessageController::class, 'store']);
    Route::put(
        'message/{message}',
        [MessageController::class, 'update']
    );
    Route::delete('message/{message}', [MessageController::class, 'destroy']);

    // ticket
    Route::get('ticket', [TicketController::class, 'index']);
    Route::get('ticket/{ticket}', [TicketController::class, 'show']);
    Route::post('ticket', [TicketController::class, 'store']);
    Route::put('ticket/{ticket}', [TicketController::class, 'update']);
    Route::delete('ticket/{ticket}', [TicketController::class, 'destroy']);
});