<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ExperienciaController;
use App\Http\Controllers\Api\V1\ReservaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Autenticación
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Experiencias (CRUD)
    Route::apiResource('experiencias', ExperienciaController::class);
    // Ruta para aprobar una experiencia (CU-15)
    Route::put('experiencias/{id}/approve', [ExperienciaController::class, 'approve'])->name('experiencias.approve');

    // Reservas (CRUD)
    Route::apiResource('reservas', ReservaController::class);
});
