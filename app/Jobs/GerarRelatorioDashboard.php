<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GerarRelatorioDashboard implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando job GerarRelatorioDashboard.');

        $pythonPath = 'python'; // Ou o caminho completo, ex: 'C:\Python39\python.exe'
        $scriptPath = base_path('python_api/app.py');

        // Define um diretório gravável para o cache do Matplotlib
        $matplotlibCacheDir = storage_path('app/matplotlib_cache');
        if (!File::isDirectory($matplotlibCacheDir)) {
            File::makeDirectory($matplotlibCacheDir, 0755, true);
        }

        $process = new Process([$pythonPath, $scriptPath]);
        $process->setEnv(['MPLCONFIGDIR' => $matplotlibCacheDir]);
        $process->setTimeout(3600); // Aumenta o timeout para 1 hora

        try {
            $process->mustRun();
            Log::info('Job GerarRelatorioDashboard concluído com sucesso.');
            // Opcional: Registra a saída do script em caso de sucesso, para depuração.
            Log::debug('Saída do script Python: ' . $process->getOutput());
        } catch (ProcessFailedException $exception) {
            // Registra a mensagem de erro completa, incluindo a saída de erro do Python.
            Log::error('Falha no job GerarRelatorioDashboard: ' . $exception->getMessage(), ['output' => $process->getOutput(), 'error_output' => $process->getErrorOutput()]);
        }
    }
}