<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\ProgramacionController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\EquipamientoController;
use App\Models\Producto;
use Illuminate\Support\Facades\Route;

// 1. Rutas Públicas e Invitados
Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// 2. Rutas Protegidas de tus compañeros
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

        Route::get('/proveedores', [ProveedorController::class, 'index']);
        Route::post('/proveedores', [ProveedorController::class, 'store']);
        Route::get('/proveedores/{id}', [ProveedorController::class, 'show']);
        Route::put('/proveedores/{id}', [ProveedorController::class, 'update']);
        Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy']);

        Route::get('/compras', [CompraController::class, 'index']);
        Route::post('/compras', [CompraController::class, 'store']);
        Route::get('/compras/{id}', [CompraController::class, 'show']);

        Route::get('/turnos', [TurnoController::class, 'index']);
        Route::post('/turnos', [TurnoController::class, 'store']);
        Route::post('/turnos/registrar', [TurnoController::class, 'registrar']);
        Route::put('/turnos/{id}', [TurnoController::class, 'update']);

        Route::get('/planillas', [PlanillaController::class, 'index']);
        Route::post('/planillas', [PlanillaController::class, 'store']);
        Route::delete('/planillas/{id}', [PlanillaController::class, 'destroy']);
        Route::get('/planillas/resumen', [PlanillaController::class, 'resumen']);

        Route::get('/programaciones', [ProgramacionController::class, 'index']);
        Route::post('/programaciones', [ProgramacionController::class, 'store']);
        Route::get('/programaciones/global', [ProgramacionController::class, 'global']);

        Route::get('/productos', function () {
            $productos = Producto::where('estadoA', true)->orderBy('nombre')->get(['id_producto', 'nombre', 'stock_disponible']);
            return response()->json(['productos' => $productos]);
        });
    }); 
});

// 3. 🛡️ TUS MÓDULOS LIBRES (Blindados contra bloqueos de sesión y CORS en local)
Route::group(['middleware' => [\Illuminate\Routing\Middleware\SubstituteBindings::class]], function () {
    
    Route::post('taller/caja/apertura', [CajaController::class, 'abrirCaja']);
    Route::post('taller/caja/cierre', [CajaController::class, 'cerrarCaja']);
    
    // Forzamos que la ruta de recibos ignore cualquier cookie corrupta de la sesión
    Route::post('taller/caja/recibos', [CajaController::class, 'crearRecibo']);
    
    Route::get('taller/caja/ventas', [CajaController::class, 'obtenerVentas']); 
});

Route::prefix('taller/equipamiento')->group(function () {
    Route::get('/', [EquipamientoController::class, 'index']);
    Route::post('/fallas', [EquipamientoController::class, 'reportarFalla']);
    Route::post('/mantenimiento', [EquipamientoController::class, 'programarMantenimiento']);
});

Route::get('taller/dashboard', function () {
    return view('dashboard_taller');
});