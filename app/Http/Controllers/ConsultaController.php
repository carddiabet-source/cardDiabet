<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Gestante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    public function index()
    {

        return view('consultas.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt'
        ]);

        set_time_limit(0);

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return response()->json(['message' => 'Erro ao ler o arquivo'], 400);
        }

        DB::beginTransaction();

        try {
            $header = fgetcsv($handle, 0, ';');

            $gestantesCache = [];
            $batch = [];

            while (($row = fgetcsv($handle, 0, ';')) !== false) {

                $data = array_combine($header, $row);

                // Normalizações
                $data['data_consulta'] = date('Y-m-d', strtotime($data['data_consulta']));

                $booleans = [
                    'diabetes_gestacional',
                    'hipertensao',
                    'hipertensao_pre_eclampsia',
                    'obesidade_pre_gestacional',
                    'historico_familiar_chd',
                    'uso_medicamentos',
                    'tabagismo',
                    'alcoolismo',
                    'chd_confirmada',
                ];

                foreach ($booleans as $field) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }

                /*
     |--------------------------------------------------------------------------
     | 1. Criar ou localizar a gestante
     |--------------------------------------------------------------------------
     */
                $gestante = Gestante::firstOrCreate(
                    [
                        'nome' => $data['nome'],
                    ]
                );

                /*
     |--------------------------------------------------------------------------
     | 2. Criar ou atualizar a consulta
     |--------------------------------------------------------------------------
     */
                Consulta::updateOrCreate(
                    [
                        'gestante_id'     => $gestante->id,
                        'consulta_numero' => $data['consulta_numero'],
                    ],
                    [
                        'gestante_id' => $gestante->id,

                        'data_consulta' => $data['data_consulta'],
                        'idade' => $data['idade'],
                        'idade_gestacional' => $data['idade_gestacional'],
                        'pressao_sistolica' => $data['pressao_sistolica'],
                        'bpm_materno' => $data['bpm_materno'],
                        'saturacao' => $data['saturacao'],
                        'temperatura_corporal' => $data['temperatura_corporal'],
                        'altura' => $data['altura'],
                        'peso' => $data['peso'],

                        'diabetes_gestacional' => $data['diabetes_gestacional'],
                        'hipertensao' => $data['hipertensao'],
                        'hipertensao_pre_eclampsia' => $data['hipertensao_pre_eclampsia'],
                        'obesidade_pre_gestacional' => $data['obesidade_pre_gestacional'],
                        'historico_familiar_chd' => $data['historico_familiar_chd'],
                        'uso_medicamentos' => $data['uso_medicamentos'],
                        'tabagismo' => $data['tabagismo'],
                        'alcoolismo' => $data['alcoolismo'],

                        'frequencia_cardiaca_fetal' => $data['frequencia_cardiaca_fetal'],
                        'circunferencia_cefalica_fetal_mm' => $data['circunferencia_cefalica_fetal_mm'],
                        'circunferencia_abdominal_mm' => $data['circunferencia_abdominal_mm'],
                        'comprimento_femur_mm' => $data['comprimento_femur_mm'],
                        'translucencia_nucal_mm' => $data['translucencia_nucal_mm'],

                        'doppler_ducto_venoso' => $data['doppler_ducto_venoso'],
                        'eixo_cardiaco' => $data['eixo_cardiaco'],
                        'quatro_camaras' => $data['quatro_camaras'],

                        'chd_confirmada' => $data['chd_confirmada'],
                        'tipo_chd' => $data['tipo_chd'] ?? null,
                    ]
                );
            }


            if ($batch) {
                Consulta::insert($batch);
            }

            DB::commit();

            return response()->json([
                'message' => 'CSV importado com sucesso'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao importar CSV',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function create($id)
    {



        return view('consultas.create', compact([
            'id'
            ]));
    }

    public function show($id)
    {
        // Lógica para mostrar uma consulta específica
    }

    public function edit($id)
    {
        // Lógica para mostrar o formulário de edição de consulta
    }

    public function update(Request $request, $id)
    {
        // Lógica para atualizar uma consulta existente
    }

    public function destroy($id)
    {
        // Lógica para excluir uma consulta
    }
}
