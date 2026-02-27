@extends('layouts.app')

@section('title', 'Gestante')

@section('content')
    <div class="space-y-6">

        <!-- Cabeçalho -->
        <div class="bg-white shadow rounded-lg p-6">

            <div class="flex flex-row  justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $gestante->nome }}
                </h2>


                <a href="{{ route('consultas.create', ['id' => $gestante->id]) }}"
                    class="inline-block mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Cadastrar nova consulta
                </a>

            </div>

            <p class="text-sm text-gray-500">
                Total de consultas: {{ $gestante->consultas->count() }}
            </p>
        </div>

        <!-- Consultas -->
        @forelse ($gestante->consultas as $consulta)
            <div class="bg-white shadow rounded-xl p-6 space-y-6">

                <!-- Cabeçalho da consulta -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-600">
                            Consulta nº {{ $consulta->consulta_numero }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $consulta->data_consulta->format('d/m/Y') }}
                        </p>
                    </div>

                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold
            {{ $consulta->chd_confirmada ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        CHD {{ $consulta->chd_confirmada ? 'Confirmada' : 'Não confirmada' }}
                    </span>
                </div>

                <!-- Grid principal -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Dados da gestante -->
                    <div class="bg-gray-50 border-l-4 border-blue-500 p-4 rounded space-y-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase">
                            Dados da Gestante
                        </h4>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <p><span class="text-gray-500">Idade</span><br><strong>{{ $consulta->idade }} anos</strong></p>
                            <p><span class="text-gray-500">Idade
                                    gestacional</span><br><strong>{{ $consulta->idade_gestacional }} sem</strong></p>
                            <p><span class="text-gray-500">Altura</span><br><strong>{{ $consulta->altura }} cm</strong></p>
                            <p><span class="text-gray-500">Peso</span><br><strong>{{ $consulta->peso }} kg</strong></p>
                        </div>
                    </div>

                    <!-- Sinais vitais -->
                    <div class="bg-gray-50 border-l-4 border-green-500 p-4 rounded space-y-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase">
                            Sinais Vitais
                        </h4>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <p><span class="text-gray-500">Pressão
                                    Sistólica</span><br><strong>{{ $consulta->pressao_sistolica }}</strong></p>
                            <p><span class="text-gray-500">BPM
                                    Materno</span><br><strong>{{ $consulta->bpm_materno }}</strong></p>
                            <p><span class="text-gray-500">Saturação</span><br><strong>{{ $consulta->saturacao }}%</strong>
                            </p>
                            <p><span class="text-gray-500">Temperatura</span><br><strong>{{ $consulta->temperatura_corporal }}
                                    °C</strong></p>
                        </div>
                    </div>

                    <!-- Condições clínicas -->
                    <div class="bg-gray-50 border-l-4 border-yellow-500 p-4 rounded space-y-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase">
                            Condições Clínicas
                        </h4>

                        <div class="flex flex-wrap gap-2 text-xs">
                            @foreach ([
            'Diabetes Gestacional' => $consulta->diabetes_gestacional,
            'Hipertensão' => $consulta->hipertensao,
            'Pré-eclâmpsia' => $consulta->hipertensao_pre_eclampsia,
            'Obesidade' => $consulta->obesidade_pre_gestacional,
            'Hist. Familiar CHD' => $consulta->historico_familiar_chd,
            'Medicamentos' => $consulta->uso_medicamentos,
            'Tabagismo' => $consulta->tabagismo,
            'Alcoolismo' => $consulta->alcoolismo,
        ] as $label => $value)
                                <span
                                    class="px-2 py-1 rounded-full font-medium
                        {{ $value ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dados fetais -->
                    <div class="bg-gray-50 border-l-4 border-purple-500 p-4 rounded space-y-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase">
                            Dados Fetais
                        </h4>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <p><span class="text-gray-500">FC
                                    Fetal</span><br><strong>{{ $consulta->frequencia_cardiaca_fetal }}</strong></p>
                            <p><span class="text-gray-500">Circ.
                                    Cefálica</span><br><strong>{{ $consulta->circunferencia_cefalica_fetal_mm }}
                                    mm</strong></p>
                            <p><span class="text-gray-500">Circ.
                                    Abdominal</span><br><strong>{{ $consulta->circunferencia_abdominal_mm }} mm</strong>
                            </p>
                            <p><span class="text-gray-500">Compr.
                                    Fêmur</span><br><strong>{{ $consulta->comprimento_femur_mm }} mm</strong></p>
                            <p><span class="text-gray-500">TN</span><br><strong>{{ $consulta->translucencia_nucal_mm }}
                                    mm</strong></p>
                        </div>
                    </div>

                </div>

                <!-- Avaliação cardíaca -->
                <div class="bg-gray-50 border-l-4 border-red-500 p-4 rounded space-y-3">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase">
                        Avaliação Cardíaca
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <p><span class="text-gray-500">Doppler Ducto
                                Venoso</span><br><strong>{{ $consulta->doppler_ducto_venoso }}</strong></p>
                        <p><span class="text-gray-500">Eixo
                                Cardíaco</span><br><strong>{{ $consulta->eixo_cardiaco }}</strong></p>
                        <p><span class="text-gray-500">Quatro
                                Câmaras</span><br><strong>{{ $consulta->quatro_camaras }}</strong></p>
                    </div>

                    @if ($consulta->chd_confirmada && $consulta->tipo_chd)
                        <p class="text-sm text-red-700 font-semibold">
                            Tipo de CHD: {{ $consulta->tipo_chd }}
                        </p>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-white shadow rounded-lg p-6 text-gray-500">
                Nenhuma consulta registrada.
            </div>
        @endforelse


    </div>
@endsection
