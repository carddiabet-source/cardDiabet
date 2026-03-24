<?php

namespace App\Http\Controllers;

use App\Jobs\AnalisarDadosIA;
use App\Jobs\GerarRelatorioDashboard;
use App\Models\Consulta;
use App\Models\Gestante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGestantes = Gestante::count();
        $totalConsultas = Consulta::count();
        $chdConfirmadas = Consulta::where('chd_confirmada', true)->count();

        $reportPath = storage_path('app/analytics/dashboard_data.json');

        if (!File::exists($reportPath)) {
            GerarRelatorioDashboard::dispatch();
        }

        $analyticsData = null;

        if (File::exists($reportPath)) {
            $analyticsData = json_decode(File::get($reportPath), true);
        }

        return view('dashboard', compact(
            'totalGestantes',
            'totalConsultas',
            'chdConfirmadas',
            'analyticsData'
        ));
    }
}