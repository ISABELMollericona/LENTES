<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\CarritoController;
use App\Http\Controllers\Api\AsesorController;
use App\Http\Controllers\Api\FaceAnalysisController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PagoController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/catalogo', [CatalogoController::class, 'index']);
Route::get('/catalogo/{lente}', [CatalogoController::class, 'show']);
Route::get('/categorias', [CatalogoController::class, 'categorias']);
Route::get('/marcas', [CatalogoController::class, 'marcas']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);

    Route::get('/carrito', [CarritoController::class, 'index']);
    Route::post('/carrito/agregar/{lente}', [CarritoController::class, 'agregar']);
    Route::delete('/carrito/{carrito}', [CarritoController::class, 'eliminar']);
    Route::post('/carrito/confirmar', [CarritoController::class, 'confirmarCompra']);

    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']);

    Route::post('/pagos/{pedido}', [PagoController::class, 'store']);
    Route::get('/pagos/comprobante/{pago}', [PagoController::class, 'comprobante']);

    Route::post('/asesor/chat', [AsesorController::class, 'chat']);
    Route::post('/asesor/recomendar', [AsesorController::class, 'recomendar']);
    Route::get('/asesor/resultados/{recomendacion}', [AsesorController::class, 'resultados']);

    Route::post('/analisis-facial', [FaceAnalysisController::class, 'analyze']);
    Route::get('/analisis-facial/{analisis}', [FaceAnalysisController::class, 'resultado']);
});
