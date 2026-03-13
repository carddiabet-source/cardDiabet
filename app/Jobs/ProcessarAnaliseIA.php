namespace App\Jobs;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class ProcessarAnaliseIA implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta;
    }

    public function handle(): void
    {
        // Caminho para o executável do Python e para o script
        $pythonPath = 'python'; // ou o caminho completo 'C:/Python39/python.exe'
        $scriptPath = base_path('python_api/analise_ia.py');

        // Executa o script passando o ID da consulta como argumento
        $result = Process::run("$pythonPath $scriptPath {$this->consulta->id}");

        if ($result->successful()) {
            // Se o script salvou um HTML, você pode atualizar o caminho no banco
            $this->consulta->update([
                'status_analise' => 'concluido',
                'relatorio_path' => "output/relatorio_{$this->consulta->id}.html"
            ]);
        } else {
            $this->consulta->update(['status_analise' => 'erro']);
            \Log::error("Erro no Python: " . $result->errorOutput());
        }
    }
}