<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments', [AppointmentController::class, 'index']);//id user
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/doctors', [AdminController::class, 'doctors']);
    Route::get('/doctors/{id}', [AdminController::class, 'getDoctor']);
    Route::post('/doctors', [AdminController::class, 'storeDoctor']);
    Route::put('/doctors/{id}', [AdminController::class, 'updateDoctor']);
    Route::delete('/doctors/{id}', [AdminController::class, 'destroyDoctor']);
    Route::get('/appointments', [AdminController::class, 'appointments']);
    Route::get('/appointments/{id}', [AdminController::class, 'getAppointment']);
    Route::put('/appointments/{id}', [AdminController::class, 'updateAppointment']);
    Route::delete('/appointments/{id}', [AdminController::class, 'destroyAppointment']);
    Route::get('/users', [AdminController::class, 'users']);
});

