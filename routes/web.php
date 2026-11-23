<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TituloController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::resource('clientes', ClienteController::class)->only(['index', 'create', 'store']);

Route::resource('titulos', TituloController::class)->only(['index', 'create', 'store', 'show']);
Route::post('titulos/{titulo}/pagar', [TituloController::class, 'pagar'])->name('titulos.pagar');
Route::post('titulos/{titulo}/cancelar', [TituloController::class, 'cancelar'])->name('titulos.cancelar');
