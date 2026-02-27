<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestanteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');


Route::post('login', [AuthController::class, 'login'])->name('auth');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ROTAS PROTEGIDAS POR LOGIN
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('gestantes', GestanteController::class);
    Route::get('/consultas/import', [ConsultaController::class, 'index'])->name('consultas.import');
    Route::resource('consultas/{id}', ConsultaController::class)->names('consultas');
});
