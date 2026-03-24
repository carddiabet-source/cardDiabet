<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConsultaResource;
use App\Models\Consulta;
use App\Models\Gestante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class ConsultaController extends Controller
{
    public function index()
    {
        return view('consultas.import');
    }

    public function store(Request $request, $id)
    {
        // 1. Validar todos os campos do formulário
        $validatedData = $request->validate([
            'data_consulta' => 'required|date',
            'idade_gestacional' => 'required|integer',
            'altura' => 'nullable|numeric',
            'peso' => 'nullable|numeric',
            'pressao_sistolica' => 'nullable|integer',
            'diabetes_gestacional' => 'required|boolean',
            'obesidade_pre_gestacional' => 'required|boolean',
            'bpm_materno' => 'nullable|integer',
            'saturacao' => 'nullable|integer',
            'temperatura_corporal' => 'nullable|numeric',
            'glicemia_jejum' => 'nullable|numeric',
            'glicemia_pos_prandial' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',
            'frequencia_cardiaca_fetal' => 'nullable|integer',
            'circunferencia_cefalica_fetal_mm' => 'nullable|numeric',
            'circunferencia_abdominal_mm' => 'nullable|numeric',
            'comprimento_femur_mm' => 'nullable|numeric',
            'translucencia_nucal_mm' => 'nullable|numeric',
            'doppler_ducto_venoso' => 'nullable|string|max:255',
            'eixo_cardiaco' => 'nullable|string|max:255',
            'quatro_camaras' => 'nullable|string|max:255',
            'chd_confirmada' => 'required|boolean',
            'tipo_chd' => 'nullable|string|max:255',
            
        ]);

        // 2. Adicionar o ID da gestante para o salvamento
        $validatedData['gestante_id'] = $id;

        // Adicionar o número da consulta
        $ultimoNumero = Consulta::where('gestante_id', $id)->max('consulta_numero');
        $validatedData['consulta_numero'] = ($ultimoNumero ?? 0) + 1;

        // 3. Criar a consulta usando atribuição em massa
        $consulta = Consulta::create($validatedData);

        // 4. Redirecionar para a página de detalhes da gestante
        return redirect()->route('gestantes.show', $id)->with('success', 'Consulta cadastrada com sucesso!');
    }
        
    public function import(Request $request)
    {
        if (!$request->hasFile('csv')) {
            return redirect()->back()->with('error', 'Nenhum arquivo CSV foi enviado.');
        }

        return $this->importCsv($request);
    }

    private function importCsv(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt'
        ]);

        set_time_limit(0);

        $file = $request->file('csv');
        $path = $file->getRealPath();

        // --- Início: Auto-detecção do delimitador ---
        $file_for_detect = fopen($path, 'r');
        if (!$file_for_detect) {
            return redirect()->back()->with('error', 'Não foi possível ler o arquivo enviado.');
        }
        $first_line = fgets($file_for_detect);
        fclose($file_for_detect);

        $delimiters = [';' => substr_count($first_line, ';'), ',' => substr_count($first_line, ',')];
        $separator = array_search(max($delimiters), $delimiters);
        // --- Fim: Auto-detecção do delimitador ---

        $handle = fopen($path, 'r');

        DB::beginTransaction();

        try {
            $header = fgetcsv($handle, 0, $separator);
            
            // Remove BOM (Byte Order Mark) do Excel caso exista no primeiro item do cabeçalho
            if ($header && isset($header[0])) {
                $header[0] = preg_replace('/[\x{FEFF}]/u', '', $header[0]);
            }
            // Limpar espaços em branco dos cabeçalhos
            if ($header) {
                $header = array_map('trim', $header);
            }

            // Validar se a coluna obrigatória existe
            if (!$header || !in_array('gestante_id', $header)) {
                throw new \Exception('A coluna "gestante_id" não foi encontrada. Verifique se o arquivo usa ponto e vírgula (;) ou vírgula (,) como separador.');
            }

            $rowNumber = 1;
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                $rowNumber++;

                if (count($header) != count($row)) {
                    continue;
                }

                $data = array_combine($header, $row);

                /*
                |-----------------------------------
                | Normalizar vazios
                |-----------------------------------
                */
                foreach ($data as $key => $value) {
                    $data[$key] = $value === '' ? null : $value;
                }

                /*
                |-----------------------------------
                | Decimais
                |-----------------------------------
                */
                $decimals = [
                    'peso',
                    'peso_fetal',
                    'imc',
                    'temperatura_corporal',
                    'glicemia_jejum',
                    'glicemia_pos_prandial',
                    'hba1c'
                ];

                foreach ($decimals as $field) {
                    if (!empty($data[$field])) {
                        $data[$field] = str_replace(',', '.', $data[$field]);
                    }
                }

                /*
                |-----------------------------------
                | Inteiros
                |-----------------------------------
                */
                $integers = [
                    'idade_gestacional',
                    'pressao_sistolica',
                    'bpm_materno',
                    'saturacao',
                    'altura',
                    'frequencia_cardiaca_fetal',
                    'circunferencia_cefalica_fetal_mm',
                    'circunferencia_abdominal_mm',
                    'comprimento_femur_mm',
                    'translucencia_nucal_mm',
                    'total_fatores_risco',
                    'num_chd_codigos'
                ];

                foreach ($integers as $field) {
                    if (!empty($data[$field])) {
                        $data[$field] = (int) $data[$field];
                    }
                }

                /*
                |-----------------------------------
                | Data
                |-----------------------------------
                */
                if (empty($data['data_consulta'])) {
                    throw new \Exception("A coluna 'data_consulta' não pode ser vazia. Verifique a linha {$rowNumber} do seu arquivo CSV.");
                }

                // Verifica se é formato brasileiro d/m/Y
                if (str_contains($data['data_consulta'], '/')) {
                    $data['data_consulta'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['data_consulta'])->format('Y-m-d');
                } else {
                    $data['data_consulta'] = date('Y-m-d', strtotime($data['data_consulta']));
                }

                /*
                |-----------------------------------
                | Boolean
                |-----------------------------------
                */
                $booleans = [
                    'diabetes_gestacional',
                    'hipertensao',
                    'hipertensao_pre_eclampsia',
                    'obesidade_pre_gestacional',
                    'alteracao_estrutural',
                    'chd_confirmada'
                ];

                foreach ($booleans as $field) {

                    if (isset($data[$field])) {

                        $data[$field] = filter_var(
                            $data[$field],
                            FILTER_VALIDATE_BOOLEAN,
                            FILTER_NULL_ON_FAILURE
                        );

                        $data[$field] = $data[$field] ?? false;
                    }
                }

                /*
                |-----------------------------------
                | Criar/Atualizar gestante
                |-----------------------------------
                */
                $gestante = Gestante::firstOrCreate(
                    ['gestante_id' => $data['gestante_id']]
                );

                if (!empty($data['data_nascimento'])) {
                    if (str_contains($data['data_nascimento'], '/')) {
                        $gestante->data_nascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $data['data_nascimento'])->format('Y-m-d');
                    } else {
                        $gestante->data_nascimento = date('Y-m-d', strtotime($data['data_nascimento']));
                    }
                    $gestante->save();
                }

                /*
                |-----------------------------------
                | Criar consulta
                |-----------------------------------
                */
                Consulta::updateOrCreate(

                    [
                        'gestante_id' => $gestante->id,
                        'consulta_numero' => $data['consulta_numero']
                    ],

                    [
                        'data_consulta' => $data['data_consulta'] ?? null,

                        'idade_gestacional' => $data['idade_gestacional'] ?? null,

                        'pressao_sistolica' => $data['pressao_sistolica'] ?? null,
                        'bpm_materno' => $data['bpm_materno'] ?? null,
                        'saturacao' => $data['saturacao'] ?? null,
                        'temperatura_corporal' => $data['temperatura_corporal'] ?? null,

                        'altura' => $data['altura'] ?? null,

                        'peso' => $data['peso'] ?? null,
                        'peso_fetal' => $data['peso_fetal'] ?? null,
                        'imc' => $data['imc'] ?? null,

                        'diabetes_gestacional' => $data['diabetes_gestacional'] ?? 0,
                        'obesidade_pre_gestacional' => $data['obesidade_pre_gestacional'] ?? 0,

                        'frequencia_cardiaca_fetal' => $data['frequencia_cardiaca_fetal'] ?? null,

                        'circunferencia_cefalica_fetal_mm' => $data['circunferencia_cefalica_fetal_mm'] ?? null,
                        'circunferencia_abdominal_mm' => $data['circunferencia_abdominal_mm'] ?? null,
                        'comprimento_femur_mm' => $data['comprimento_femur_mm'] ?? null,
                        'translucencia_nucal_mm' => $data['translucencia_nucal_mm'] ?? null,

                        'doppler_ducto_venoso' => $data['doppler_ducto_venoso'] ?? null,
                        'eixo_cardiaco' => $data['eixo_cardiaco'] ?? null,
                        'quatro_camaras' => $data['quatro_camaras'] ?? null,

                        'total_fatores_risco' => $data['total_fatores_risco'] ?? null,
                        'alteracao_estrutural' => $data['alteracao_estrutural'] ?? 0,
                        'num_chd_codigos' => $data['num_chd_codigos'] ?? null,

                        'chd_confirmada' => $data['chd_confirmada'] ?? 0,
                        'tipo_chd' => $data['tipo_chd'] ?? null,
                    ]
                );
            }

            fclose($handle);

            DB::commit();

            return redirect()->back()->with('success', 'CSV importado com sucesso!');

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()->with('error', 'Erro ao importar CSV: ' . $e->getMessage());
        }
    }
    public function create($id)
    {
        // 1. Encontrar a gestante pelo ID recebido na rota.
        $gestante = Gestante::findOrFail($id);

        // 2. Passar o objeto 'gestante' para a view.
        return view('consultas.create', compact('gestante'));
    }

    public function update(Request $request, $id)
    {
        $consulta = Consulta::findOrFail($id);

        // A validação foi expandida para incluir todos os campos do formulário,
        // espelhando o método 'storeFromForm'. Isso evita a perda de dados ao atualizar.
        $validatedData = $request->validate([
            'data_consulta' => 'required|date',
            'idade_gestacional' => 'required|integer',
            'altura' => 'nullable|numeric',
            'peso' => 'nullable|numeric',
            'pressao_sistolica' => 'nullable|integer',
            'bpm_materno' => 'nullable|integer',
            'saturacao' => 'nullable|integer',
            'temperatura_corporal' => 'nullable|numeric',
            'glicemia_jejum' => 'nullable|numeric',
            'glicemia_pos_prandial' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',
            'frequencia_cardiaca_fetal' => 'nullable|integer',
            'circunferencia_cefalica_fetal_mm' => 'nullable|numeric',
            'circunferencia_abdominal_mm' => 'nullable|numeric',
            'comprimento_femur_mm' => 'nullable|numeric',
            'translucencia_nucal_mm' => 'nullable|numeric',
            'doppler_ducto_venoso' => 'nullable|string|max:255',
            'eixo_cardiaco' => 'nullable|string|max:255',
            'quatro_camaras' => 'nullable|string|max:255',
            'diabetes_gestacional' => 'required|boolean',
            'obesidade_pre_gestacional' => 'required|boolean',
            'chd_confirmada' => 'required|boolean',
            'tipo_chd' => 'nullable|string|max:255'
        ]);

        $consulta->update($validatedData);

        return redirect()->route('gestantes.show', $consulta->gestante_id)->with('success', 'Consulta atualizada com sucesso!');
    }
}
