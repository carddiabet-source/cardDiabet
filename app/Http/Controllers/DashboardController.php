<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Gestante;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Definir caminhos
        $scriptPath = resource_path('python/dashboard_analysis.py');
        
        // Exemplo: Usando um CSV que esteja na pasta storage/app
        // Certifique-se de que o arquivo existe ou aponte para o local correto do seu CSV
        $csvPath = storage_path('app/dados.csv'); 

        // Dados padrão caso o Python falhe ou não haja CSV
        $pythonData = null;

        // 2. Executar o script Python se o arquivo existir
        if (file_exists($scriptPath) && file_exists($csvPath)) {
            // Para garantir que a versão correta do Python seja usada,
            // buscamos o caminho do executável do Python do arquivo .env.
            // Se não encontrar, usa 'python' como padrão, que pode funcionar em alguns sistemas.
            $pythonExecutable = env('PYTHON_PATH', 'python'); // Busca o caminho no .env ou usa 'python'

            // Verifica se é um caminho de arquivo (contém separadores) e se existe.
            // Se for apenas 'python', tentamos executar via PATH.
            if ((str_contains($pythonExecutable, '/') || str_contains($pythonExecutable, '\\')) && !file_exists($pythonExecutable)) {
                // Erro se o caminho especificado não existe
                $pythonData = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">';
                $pythonData .= '<strong class="font-bold">Erro de Configuração:</strong> <pre class="text-sm mt-2">O executável do Python não foi encontrado no caminho especificado.</pre>';
                $pythonData .= '<pre class="text-sm mt-1">Caminho configurado: ' . htmlspecialchars($pythonExecutable) . '</pre>';
                $pythonData .= '</div>';
            } else {
                try {
                    $process = new Process([$pythonExecutable, $scriptPath, $csvPath]);
                    $process->run();

                    // Se executou com sucesso, captura a saída do Python
                    if ($process->isSuccessful()) {
                        $pythonData = $process->getOutput();
                    } else {
                        // Se falhou, captura o erro para exibir na view (ajuda no debug)
                        $pythonData = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">';
                        $pythonData .= '<strong class="font-bold">Erro Python:</strong> <pre class="text-sm mt-2">' . htmlspecialchars($process->getErrorOutput()) . '</pre>';
                        $pythonData .= '</div>';
                    }
                } catch (\Throwable $e) {
                    $pythonData = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">';
                    $pythonData .= '<strong class="font-bold">Erro de Execução:</strong> <pre class="text-sm mt-2">' . htmlspecialchars($e->getMessage()) . '</pre>';
                    $pythonData .= '</div>';
                }
            }
        }

        return view('dashboard', [
            'totalGestantes' => Gestante::count(),
            'totalConsultas' => Consulta::count(),
            'chdConfirmadas' => Consulta::where('chd_confirmada', true)->count(),
            'pythonData' => $pythonData, // Passa os dados do Python para a View
        ]);
    }
}
