<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\ActivityLogController;
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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories/create', [CategoryController::class, 'create']);
    Route::put('/categories/update', [CategoryController::class, 'update']);

    Route::get('/reimbursements', [ReimbursementController::class, 'index']);
    Route::post('/reimbursements', [ReimbursementController::class, 'store']);
    Route::post('/reimbursements/{id}/approve', [ReimbursementController::class, 'approve']);
    Route::post('/reimbursements/{id}/reject', [ReimbursementController::class, 'reject']);
    Route::delete('/reimbursements/{id}', [ReimbursementController::class, 'destroy']);

    Route::get('/logs', [ActivityLogController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);        // list all users
    Route::get('/users/{id}', [UserController::class, 'show']);    // get one user
    Route::post('/users', [UserController::class, 'store']);       // create
    Route::put('/users/{id}', [UserController::class, 'update']);  // update
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete
});
