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
Route::post('/register', [AuthController::class, 'register']); //done
Route::post('/login', [AuthController::class, 'login']); //done

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index']);//done
    Route::post('/appointments', [AppointmentController::class, 'store']);//done
    Route::put('/appointments/{id}', [AppointmentController::class, 'update']);//done
    Route::get('/appointments', [AppointmentController::class, 'index']);//id user -> done
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);//done
    Route::put('/profile', [AuthController::class, 'update']);//done
    Route::get('/me', [AuthController::class, 'me']);//done
    Route::post('/logout', [AuthController::class, 'logout']);//done
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/doctors', [AdminController::class, 'doctors']);//done
    Route::get('/doctors/{id}', [AdminController::class, 'getDoctor']);//done
    Route::post('/doctors', [AdminController::class, 'storeDoctor']);//done
    Route::put('/doctors/{id}', [AdminController::class, 'updateDoctor']);//done
    Route::delete('/doctors/{id}', [AdminController::class, 'destroyDoctor']);//done
    Route::get('/appointments', [AdminController::class, 'appointments']);//done
    Route::get('/appointments/{id}', [AdminController::class, 'getAppointment']);//done
    Route::put('/appointments/{id}', [AdminController::class, 'updateAppointment']);//done
    Route::delete('/appointments/{id}', [AdminController::class, 'destroyAppointment']);//done
    Route::get('/users', [AdminController::class, 'users']);//done
    Route::get('/users/{id}', [AdminController::class, 'getUser']);//done
});

