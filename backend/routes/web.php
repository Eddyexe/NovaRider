<?php

use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\MotocicletaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/cambiar-contrasena', [AuthController::class, 'cambiarContrasena']);

    Route::middleware('role:1')->group(function () {
        Route::get('/roles', [UsuarioController::class, 'roles']);
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
        Route::put('/usuarios/{id}/reactivar', [UsuarioController::class, 'reactivar']);
    });

    Route::middleware('role:1,3,4')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::post('/clientes', [ClienteController::class, 'store']);
        Route::get('/clientes/{id}', [ClienteController::class, 'show']);
        Route::put('/clientes/{id}', [ClienteController::class, 'update']);
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
        Route::put('/clientes/{id}/reactivar', [ClienteController::class, 'reactivar']);
    });

    Route::middleware('role:1,3')->group(function () {
        Route::get('/motocicletas', [MotocicletaController::class, 'index']);
        Route::post('/motocicletas', [MotocicletaController::class, 'store']);
        Route::get('/motocicletas/{id}', [MotocicletaController::class, 'show']);
        Route::put('/motocicletas/{id}', [MotocicletaController::class, 'update']);
        Route::delete('/motocicletas/{id}', [MotocicletaController::class, 'destroy']);
        Route::put('/motocicletas/{id}/reactivar', [MotocicletaController::class, 'reactivar']);
    });
});
