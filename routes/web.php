<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestanteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\AnaliseController;

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
})->name('login');

=======
use App\Http\Controllers\LoginController;


Route::get('/', [LoginController::class, 'create'])->name('login')->middleware('guest'); // exibe o form
Route::post('/login', [LoginController::class, 'store'])->name('login.store'); // envia form
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout'); // logout



>>>>>>> salvando-commit
// ROTAS PROTEGIDAS POR LOGIN
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/dashboard/analisar', [DashboardController::class, 'analisar'])
        ->name('dashboard.analisar');

    Route::get('/dashboard/verificar-analise', [DashboardController::class, 'verificarAnalise'])
        ->name('dashboard.verificarAnalise');

    Route::resource('gestantes', GestanteController::class);

    // Rotas de Consulta
    Route::get('/consultas/import', [ConsultaController::class, 'index'])->name('consultas.import');
    Route::post('/consultas/import', [ConsultaController::class, 'import'])->name('consultas.import.store');
    Route::get('/consultas/create/{id}', [ConsultaController::class, 'create'])->name('consultas.create');
    Route::post('/consultas/{id}', [ConsultaController::class, 'store'])->name('consultas.store');

});

    Route::post('/analise', [AnaliseController::class, 'iniciarAnalise']);
    Route::get('/analise/status', [AnaliseController::class, 'verificarStatus']);
    Route::get('/analise/resultado', [AnaliseController::class, 'obterResultado']);