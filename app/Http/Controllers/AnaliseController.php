// Em app/Http/Controllers/AnaliseController.php

use App\Jobs\ProcessarAnaliseIA;
use App\Models\Consulta; // Ou o modelo que você usa

public function iniciarAnalise(Request $request)
{
    // Valide o request, encontre a consulta, etc.
    $consulta = Consulta::findOrFail($request->input('consulta_id'));

    // Atualiza o status para indicar que a análise começou
    $consulta->update(['status_analise' => 'processando']);

    // Dispara o job para ser executado em segundo plano
    ProcessarAnaliseIA::dispatch($consulta);

    // Retorna uma resposta imediata para o usuário
    return response()->json([
        'message' => 'A análise foi iniciada com sucesso. O resultado estará disponível em breve.'
    ]);
}
