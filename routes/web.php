<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Cliente\CatalogoController;
use App\Http\Controllers\Cliente\CarritoController;
use App\Http\Controllers\Cliente\PedidoController;
use App\Http\Controllers\Cliente\PagoController;
use App\Http\Controllers\AsesorVirtual\ChatController;
use App\Http\Controllers\AsesorVirtual\RecomendacionController;
use App\Http\Controllers\Facial\FaceAnalysisController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/google', [SocialiteController::class, 'redirectToGoogle'])->name('google');
    Route::get('/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/lentes', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/lentes/{lente}', [CatalogoController::class, 'show'])->name('catalogo.show');

Route::get('/asesor-virtual', [ChatController::class, 'index'])->name('asesor.index');
Route::get('/analisis-facial', [FaceAnalysisController::class, 'index'])->name('facial.index');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/asesor-virtual/resultados/{recomendacion}', [RecomendacionController::class, 'resultados'])->name('asesor.resultados');

    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{lente}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::delete('/carrito/{carrito}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/confirmar', [CarritoController::class, 'confirmarCompra'])->name('carrito.confirmar');
    Route::get('/carrito/checkout', [CarritoController::class, 'checkout'])->name('carrito.checkout');
    Route::post('/carrito/checkout', [CarritoController::class, 'procesarCheckout'])->name('carrito.procesarCheckout');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');

    Route::get('/pagos/{pedido}', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos/{pedido}', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/comprobante/{pago}', [PagoController::class, 'comprobante'])->name('pagos.comprobante');
});
