<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Gestante;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalGestantes' => Gestante::count(),
            'totalConsultas' => Consulta::count(),
            'chdConfirmadas' => Consulta::where('chd_confirmada', true)->count(),
        ]);
    }
}
