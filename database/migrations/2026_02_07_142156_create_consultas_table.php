<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestante_id')
                ->constrained('gestantes')
                ->cascadeOnDelete();
            $table->string('nome');

            $table->integer('consulta_numero');
            $table->date('data_consulta');

            $table->integer('idade');
            $table->integer('idade_gestacional');

            $table->integer('pressao_sistolica');
            $table->integer('bpm_materno');
            $table->integer('saturacao');
            $table->decimal('temperatura_corporal', 4, 1);

            $table->integer('altura');
            $table->decimal('peso', 5, 2);

            $table->boolean('diabetes_gestacional');
            $table->boolean('hipertensao');
            $table->boolean('hipertensao_pre_eclampsia');
            $table->boolean('obesidade_pre_gestacional');

            $table->boolean('historico_familiar_chd');
            $table->boolean('uso_medicamentos');
            $table->boolean('tabagismo');
            $table->boolean('alcoolismo');

            $table->integer('frequencia_cardiaca_fetal');
            $table->integer('circunferencia_cefalica_fetal_mm');
            $table->integer('circunferencia_abdominal_mm');
            $table->integer('comprimento_femur_mm');
            $table->integer('translucencia_nucal_mm');

            $table->string('doppler_ducto_venoso');
            $table->string('eixo_cardiaco');
            $table->string('quatro_camaras');

            $table->boolean('chd_confirmada');
            $table->string('tipo_chd')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
