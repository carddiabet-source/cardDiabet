@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="max-w-7xl mx-auto py-8 space-y-8">

    <!-- Título -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
        <p class="text-sm text-gray-500">Visão geral do cardioprenatal</p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-lg rounded-2xl p-6">
            <p class="text-sm text-gray-500">Total de Gestantes</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalGestantes }}</p>
        </div>

        <div class="bg-white shadow-lg rounded-2xl p-6">
            <p class="text-sm text-gray-500">Total de Consultas</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalConsultas }}</p>
        </div>

        <div class="bg-white shadow-lg rounded-2xl p-6">
            <p class="text-sm text-gray-500">Casos de CHD Confirmados</p>
            <p class="text-3xl font-bold text-red-600">{{ $chdConfirmadas }}</p>
        </div>
    </div>

    <!-- Análise Preditiva -->
    <div class="bg-white shadow-lg rounded-2xl p-8">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4 border-b pb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Análise Preditiva com IA</h3>
                <p class="text-gray-500 mt-1 max-w-2xl">
                    A análise do histórico de dados gera insights automaticamente.
                </p>
            </div>
            <button id="btn-iniciar-analise"
                class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700">
                Iniciar Nova Análise
            </button>
        </div>

        <div id="area-resultados-ia" class="mt-8 border-t pt-8 hidden">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Resultados da Análise</h3>

            <div id="loading-indicator" class="text-center py-4">
                <p class="text-gray-600 animate-pulse">Análise em andamento...</p>
            </div>

            <div id="graficos-container" class="grid grid-cols-1 md:grid-cols-2 gap-8 hidden"></div>

            <div id="error-message" class="text-center py-4 text-red-600 hidden">
                Ocorreu um erro ao processar a análise.
            </div>
        </div>
    </div>

    <!-- Análise Estatística -->
    <div class="bg-white shadow-lg rounded-2xl p-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">
            Análise Estatística (Histórico Completo)
        </h3>

        @if($analyticsData)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h4 class="font-semibold text-gray-600">Distribuição de Idade</h4>
                <canvas id="distIdadeChart"></canvas>
            </div>

            <div>
                <h4 class="font-semibold text-gray-600">IMC por Confirmação de CHD</h4>
                <canvas id="imcChart"></canvas>
            </div>
        </div>
        @else
        <p class="text-gray-600 text-center">O relatório ainda está sendo gerado.</p>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/chart.js-chart-box-and-violin-plot/build/Chart.BoxPlot.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-iniciar-analise');
    const areaResultados = document.getElementById('area-resultados-ia');
    const loading = document.getElementById('loading-indicator');
    const graficos = document.getElementById('graficos-container');
    const erro = document.getElementById('error-message');
    let polling;

    btn?.addEventListener('click', () => {
        areaResultados.classList.remove('hidden');
        loading.classList.remove('hidden');
        graficos.classList.add('hidden');
        erro.classList.add('hidden');

        btn.disabled = true;
        btn.innerText = 'Analisando...';

        fetch('/analise', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => verificarStatus());
    });

    function verificarStatus() {
        polling = setInterval(() => {
            fetch('/analise/status')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'concluido') {
                    clearInterval(polling);
                    obterResultados();
                } else if (data.status === 'erro') {
                    clearInterval(polling);
                    loading.classList.add('hidden');
                    erro.classList.remove('hidden');
                }
            });
        }, 5000);
    }

    function obterResultados() {
        fetch('/analise/resultado')
        .then(r => r.json())
        .then(resultado => {
            loading.classList.add('hidden');
            graficos.innerHTML = '';
            graficos.classList.remove('hidden');

            if (!resultado.imagens || Object.keys(resultado.imagens).length === 0) {
                erro.classList.remove('hidden');
                return;
            }

            for (const [titulo, img] of Object.entries(resultado.imagens)) {
                const div = document.createElement('div');
                div.className = 'bg-gray-50 p-4 rounded-xl border';
                div.innerHTML = `
                    <h4 class="font-semibold text-center mb-2">${titulo.replace(/_/g,' ')}</h4>
                    <img src="${img}" class="w-full rounded-md">
                `;
                graficos.appendChild(div);
            }

            btn.disabled = false;
            btn.innerText = 'Iniciar Nova Análise';
        });
    }

    @if($analyticsData)
    const analyticsData = @json($analyticsData);

    if (analyticsData.histograma_idade?.labels.length) {
        new Chart(
            document.getElementById('distIdadeChart'),
            {
                type:'bar',
                data:{
                    labels: analyticsData.histograma_idade.labels,
                    datasets:[{
                        label:'Gestantes',
                        data: analyticsData.histograma_idade.values,
                        backgroundColor: 'skyblue'
                    }]
                },
                options:{
                    responsive:true,
                    plugins:{ legend:{ display:false }, title:{ display:true, text:'Distribuição de Idade' } }
                }
            }
        );
    }

    if (analyticsData.boxplot_imc_chd) {
        new Chart(
            document.getElementById('imcChart'),
            {
                type:'boxplot',
                data:{
                    labels:['Sem CHD','Com CHD'],
                    datasets:[{
                        label:'Distribuição IMC',
                        data:[
                            analyticsData.boxplot_imc_chd.sem_chd ?? [],
                            analyticsData.boxplot_imc_chd.com_chd ?? []
                        ]
                    }]
                },
                options:{ responsive:true }
            }
        );
    }
    @endif
});
</script>
@endpush