<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use App\Models\Consulta;

class AnalisarDadosIA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        Log::info('Job AnalisarDadosIA criado.');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando Job AnalisarDadosIA.');

        // Define status inicial
        Cache::put('analise_ia_status', 'processando', now()->addMinutes(30));
        Log::info('Status da análise definido como PROCESSANDO.');

        // Use um caminho configurável para o Python a partir do .env
        // Ex: PYTHON_EXECUTABLE_PATH=C:\Users\DoQR\AppData\Local\Programs\Python\Python313\python.EXE
        $pythonPath = config('app.python_executable_path', 'python');
        $scriptPath = base_path('python_api/app.py');
        $matplotlibCacheDir = storage_path('app/matplotlib_cache');

        Log::info('Caminho do Python: ' . $pythonPath);
        Log::info('Caminho do script Python: ' . $scriptPath);

        try {

            Log::info('Buscando consultas no banco.');

            $consultas = Consulta::with('gestante')->get();

            Log::info('Consultas carregadas.', [
                'quantidade' => $consultas->count()
            ]);

            // Mapeamento para um formato plano, similar ao outro job.
            // Isso simplifica o processamento no Python com json_normalize.
            $dadosParaJson = $consultas->map(function ($consulta) {
                return array_merge($consulta->getAttributes(), $consulta->gestante->getAttributes());
            });

            // Se não houver consultas, o job deve ser concluído com sucesso com um resultado vazio.
            if ($consultas->isEmpty()) {
                Log::info('Nenhuma consulta encontrada. Concluindo o job com resultado vazio.');
                Cache::put('resultado_analise_ia', ['status' => 'concluido', 'histograma_idade' => ['labels' => [], 'values' => []], 'total_diabetes' => 0, 'imagens' => []], now()->addMinutes(30));
                Cache::put('analise_ia_status', 'concluido', now()->addMinutes(30));
                Log::info('Job AnalisarDadosIA concluído com sucesso (sem dados).');
                return;
            }

            $data = [
                'historico_consultas' => $dadosParaJson->toArray()
            ];

            $jsonData = json_encode($data);

            // Abordagem robusta: Salvar o JSON em um arquivo e passar o caminho.
            $jsonDir = storage_path('app/analytics');
            File::ensureDirectoryExists($jsonDir);
            $jsonPath = $jsonDir . '/analise_ia_input.json';
            File::put($jsonPath, $jsonData);

            Log::info('Dados de entrada para análise salvos em arquivo JSON.', [
                'path' => $jsonPath,
                'size_bytes' => strlen($jsonData)
            ]);

            // Garante que o diretório de cache do Matplotlib exista
            File::ensureDirectoryExists($matplotlibCacheDir);

            // Passa o caminho do arquivo em vez da string JSON gigante.
            $process = new Process([$pythonPath, $scriptPath, $jsonPath]);
            // Define o diretório de cache para evitar problemas de permissão
            $process->setEnv(['MPLCONFIGDIR' => $matplotlibCacheDir]);
            $process->setTimeout(3600); // Aumenta o timeout para 1 hora, como no outro job.

            Log::info('Executando script Python...');

            $process->run();

            Log::info('Script Python executado.');

            if (!$process->isSuccessful()) {
                Cache::put('analise_ia_status', 'erro', now()->addMinutes(30));
                Log::error('Erro na execução do Python.', [
                    'erro' => $process->getErrorOutput(),
                    'saida' => $process->getOutput()
                ]);
                // Lança uma exceção para que a fila possa tentar novamente se configurado.
                throw new \Exception('Falha na execução do script Python: ' . $process->getErrorOutput());
                return;
            }

            $output = $process->getOutput();

            Log::info('Saída do Python recebida.', [
                'saida_bruta' => $output
            ]);

            $resultado_analise = json_decode($output, true);

            if (!$resultado_analise) {
                Log::warning('Falha ao converter JSON retornado pelo Python.', [
                    'output' => $output
                ]);
                // Considerar tratar isso como um erro também
            }

            Cache::put('resultado_analise_ia', $resultado_analise, now()->addMinutes(30));
            Cache::put('analise_ia_status', 'concluido', now()->addMinutes(30));

            Log::info('Resultado salvo no cache.');
            Log::info('Job AnalisarDadosIA concluído com sucesso.');

        } catch (\Exception $e) {

            Cache::put('analise_ia_status', 'erro', now()->addMinutes(30));

            Log::error('Erro geral no Job AnalisarDadosIA.', [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine()
            ]);
        }
    }
}
