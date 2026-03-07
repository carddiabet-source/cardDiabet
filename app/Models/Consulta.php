<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';


    protected $fillable = [
        'gestante_id',
        'consulta_numero',
        'data_consulta',
        'idade',
        'idade_gestacional',
        'pressao_sistolica',
        'bpm_materno',
        'saturacao',
        'temperatura_corporal',
        'altura',
        'peso',
        'glicemia_jejum',
        'glicemia_pos_prandial',
        'hba1c',
        'diabetes_gestacional',
        'hipertensao',
        'hipertensao_pre_eclampsia',
        'obesidade_pre_gestacional',
        'historico_familiar_chd',
        'uso_medicamentos',
        'tabagismo',
        'alcoolismo',
        'frequencia_cardiaca_fetal',
        'circunferencia_cefalica_fetal_mm',
        'circunferencia_abdominal_mm',
        'comprimento_femur_mm',
        'translucencia_nucal_mm',
        'doppler_ducto_venoso',
        'eixo_cardiaco',
        'quatro_camaras',
        'chd_confirmada',
        'tipo_chd',
    ];

    protected $casts = [
        'data_consulta' => 'date',
        'diabetes_gestacional' => 'boolean',
        'hipertensao' => 'boolean',
        'hipertensao_pre_eclampsia' => 'boolean',
        'obesidade_pre_gestacional' => 'boolean',
        'historico_familiar_chd' => 'boolean',
        'uso_medicamentos' => 'boolean',
        'tabagismo' => 'boolean',
        'alcoolismo' => 'boolean',
        'chd_confirmada' => 'boolean',
    ];


    public function gestante()
    {
        return $this->belongsTo(Gestante::class, 'gestante_id');
    }
}
