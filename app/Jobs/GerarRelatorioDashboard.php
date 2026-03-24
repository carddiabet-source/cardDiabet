<?php

namespace App\Jobs;

use App\Models\Consulta;
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
 
        $scriptDir = base_path('python_api');
        $scriptPath = $scriptDir . '/app.py';
        // Define um diretório gravável para o cache do Matplotlib
        $matplotlibCacheDir = storage_path('app/matplotlib_cache');
        if (!File::isDirectory($matplotlibCacheDir)) {
            File::makeDirectory($matplotlibCacheDir, 0755, true);
        }
        // Diretório para salvar o CSV
        $csvDir = storage_path('app/analytics');
        File::ensureDirectoryExists($csvDir);
        $csvPath = $csvDir . '/consultas_data.csv';

        // 1. Coletar os dados do banco de dados
        $consultas = Consulta::with('gestante')->get();

        // Mapeia os dados para um formato "plano" antes de salvar no CSV
        $dadosParaCsv = $consultas->map(function ($consulta) {
            $dadosConsulta = $consulta->getAttributes(); // Pega apenas os atributos da tabela 'consultas'
            $dadosGestante = $consulta->gestante ? $consulta->gestante->getAttributes() : [];

            // Mescla os dados, removendo IDs duplicados para evitar confusão
            unset($dadosGestante['id']);

            return array_merge($dadosConsulta, $dadosGestante);
        });

        // 2. Salvar os dados em um arquivo CSV
        $handle = fopen($csvPath, 'w');
        // Adiciona o cabeçalho (colunas)
        if ($dadosParaCsv->isNotEmpty()) {
            // Pega as chaves do primeiro registro para usar como cabeçalho
            $header = array_keys($dadosParaCsv->first());
            fputcsv($handle, $header);

            // Adiciona os dados
            foreach ($dadosParaCsv as $linha) {
                fputcsv($handle, $linha);
            }
        } else {
            // Se não houver dados, cria um CSV vazio com cabeçalho para evitar erros no script Python.
            // Defina aqui as colunas que o script Python espera.
            $header = [
                'id', 'gestante_id', 'consulta_numero', 'data_consulta', 'idade_gestacional',
                'altura', 'peso', 'imc', 'pressao_sistolica', 'bpm_materno', 'saturacao',
                'temperatura_corporal', 'glicemia_jejum', 'glicemia_pos_prandial', 'hba1c',
                'frequencia_cardiaca_fetal', 'circunferencia_cefalica_fetal_mm',
                'circunferencia_abdominal_mm', 'comprimento_femur_mm', 'translucencia_nucal_mm',
                'doppler_ducto_venoso', 'eixo_cardiaco', 'quatro_camaras', 'chd_confirmada',
                'tipo_chd', 'created_at', 'updated_at', 'nome', 'data_nascimento',
                'diabetes_gestacional', 'obesidade_pre_gestacional', 'tabagismo', 'alcoolismo'
            ];
            fputcsv($handle, $header);
        }
        fclose($handle);

        // O código abaixo que envia JSON diretamente não é mais necessário
        // $data = [
        //     'historico_consultas' => $consultas->toArray()
        // ];
        // $jsonData = json_encode($data);

        // 3. Criar o processo, passando o caminho do CSV como argumento
        // Usamos 'python3'. O Process do Symfony buscará o executável no PATH do sistema.
        $process = new Process(['python3', $scriptPath, $csvPath], $scriptDir);
        $process->setEnv(['MPLCONFIGDIR' => $matplotlibCacheDir]);
        $process->setTimeout(3600); // Aumenta o timeout para 1 hora

        try {
            // Executa o processo
            $process->run();

            // Verifica se o processo foi bem-sucedido
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Captura a saída (o JSON gerado pelo Python)
            $output = $process->getOutput();

            // Salva a saída JSON no arquivo que o DashboardController espera
            File::put(storage_path('app/analytics/dashboard_data.json'), $output);

            Log::info('Job GerarRelatorioDashboard concluído com sucesso.');
        } catch (ProcessFailedException $exception) {
            // Registra a mensagem de erro completa, incluindo a saída de erro do Python.
            Log::error('Falha na execução do script Python no job GerarRelatorioDashboard.', [
                'exception_message' => $exception->getMessage(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ]);
        }
    }
}