<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\AnalisarDadosIA;
use Illuminate\Support\Facades\Cache;

class AnaliseController extends Controller
{
    // POST /analise
    public function iniciarAnalise()
    {
            Log::info("ANALISE DISPARADA");

        // Dispara o job
        AnalisarDadosIA::dispatch();

        return response()->json(['status' => 'ok']);
    }

    // GET /analise/status
    public function verificarStatus()
    {
        $status = Cache::get('analise_ia_status', 'nao_iniciado');
        return response()->json(['status' => $status]);
    }

    // GET /analise/resultado
    public function obterResultado()
    {
        $resultado = Cache::get('resultado_analise_ia', [
            'status' => 'nao_disponivel',
            'imagens' => []
        ]);

        return response()->json($resultado);
    }
}