<?php

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource("category", CategoryController::class);
Route::apiResource("chat", ChatController::class);
Route::apiResource("company", CompanyController::class);
Route::apiResource("message", MessageController::class);
Route::apiResource("product", ProductController::class);
Route::apiResource("role", RoleController::class);
Route::apiResource("tag", TagController::class);
Route::apiResource("ticket", TicketController::class);
Route::apiResource("user", UserController::class);
