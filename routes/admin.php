<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LenteController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ReporteController;

Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('lentes', LenteController::class)->names([
        'index' => 'lentes.index',
        'create' => 'lentes.create',
        'store' => 'lentes.store',
        'show' => 'lentes.show',
        'edit' => 'lentes.edit',
        'update' => 'lentes.update',
        'destroy' => 'lentes.destroy',
    ]);

    Route::patch('/lentes/{lente}/estado', [LenteController::class, 'cambiarEstado'])->name('lentes.estado');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::patch('/pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.estado');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/{usuario}/suspender', [UsuarioController::class, 'suspender'])->name('usuarios.suspender');
    Route::post('/usuarios/{usuario}/activar', [UsuarioController::class, 'activar'])->name('usuarios.activar');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas-fecha', [ReporteController::class, 'ventasPorFecha'])->name('reportes.ventas-fecha');
    Route::get('/reportes/ventas-categoria', [ReporteController::class, 'ventasPorCategoria'])->name('reportes.ventas-categoria');
    Route::get('/reportes/lentes-mas-vendidos', [ReporteController::class, 'lentesMasVendidos'])->name('reportes.lentes-top');
    Route::get('/reportes/usuarios-top', [ReporteController::class, 'usuariosTop'])->name('reportes.usuarios-top');
    Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('reportes.exportar');
});
