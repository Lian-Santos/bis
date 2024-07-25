<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;



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
Route::middleware(['AuthUser:1'])->group(function () {
    Route::get('getUserDetails', [UserController::class, 'getUserDetails']);
});
Route::post('noVerificationRegistration', [UserController::class, 'noVerificationRegistration']);
Route::post('manualLogin', [UserController::class, 'manualLogin']);
Route::post('adminLogin', [UserController::class, 'adminLogin']);


Route::middleware(['AuthUser:3'])->group(function () {
    Route::get('viewAdminableUsers', [AdminController::class, 'viewAdminableUsers']);
    Route::post('assignRole', [AdminController::class, 'assignRole']);
    Route::get('viewAssignableRoles', [AdminController::class, 'viewAssignableRoles']);
    Route::get('viewPrivilegedUsers', [AdminController::class, 'viewPrivilegedUsers']);
});
