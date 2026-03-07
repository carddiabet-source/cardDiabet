@extends('layouts.app')

@section('title', 'Nova Consulta')

@section('content')

<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-xl p-10">

<h2 class="text-3xl font-bold mb-10 text-gray-800 border-b pb-4">
Cadastrar Nova Consulta
</h2>

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-8">
<strong class="font-bold">Encontramos alguns erros:</strong>
<ul class="mt-3 list-disc list-inside text-sm">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form action="{{ route('consultas.store', ['id' => $gestante->id]) }}" method="POST">
@csrf

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

<!-- DADOS DA CONSULTA -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Dados da Consulta
</h3>

<div>
<label class="block text-sm font-medium">Data da Consulta</label>
<input type="date" name="data_consulta"
value="{{ old('data_consulta', date('Y-m-d')) }}"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400" required>
</div>

<div>
<label class="block text-sm font-medium">Idade</label>
<input type="number" name="idade"
min="10" max="49"
value="{{ old('idade', $gestante->idade) }}"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400" required>
</div>

<div>
<label class="block text-sm font-medium">Idade Gestacional</label>
<input type="number" name="idade_gestacional"
min="4" max="42"
value="{{ old('idade_gestacional') }}"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400" required>
</div>

</div>


<!-- DADOS MATERNOS -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Dados Maternos
</h3>

<div>
<label class="block text-sm font-medium">Altura (cm)</label>
<input type="number" name="altura"
min="140" max="190"
value="{{ old('altura') }}"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
</div>

<div>
<label class="block text-sm font-medium">Peso (kg)</label>
<input type="number" step="0.1" name="peso"
min="30" max="300"
value="{{ old('peso') }}"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
</div>

<div>
<label class="block text-sm font-medium">Obesidade Pré-Gestacional</label>
<select name="obesidade_pre_gestacional"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
<option value="0">Não</option>
<option value="1">Sim</option>
</select>
</div>

<div>
<label class="block text-sm font-medium">Diabetes Gestacional</label>
<select name="diabetes_gestacional"
class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
<option value="0">Não</option>
<option value="1">Sim</option>
</select>
</div>

</div>


<!-- SINAIS VITAIS -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Sinais Vitais
</h3>

<div>
<label>Pressão Sistólica</label>
<input type="number" name="pressao_sistolica"
class="w-full border rounded-lg p-2">
</div>

<div>
<label>BPM Materno</label>
<input type="number" name="bpm_materno"
class="w-full border rounded-lg p-2">
</div>

<div>
<label>Saturação (%)</label>
<input type="number" name="saturacao"
class="w-full border rounded-lg p-2">
</div>

<div>
<label>Temperatura (°C)</label>
<input type="number" step="0.1" name="temperatura_corporal"
class="w-full border rounded-lg p-2">
</div>

</div>


<!-- FATORES DE RISCO -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Fatores de Risco
</h3>

<select name="hipertensao" class="w-full border rounded-lg p-2">
<option value="0">Hipertensão: Não</option>
<option value="1">Hipertensão: Sim</option>
</select>

<select name="hipertensao_pre_eclampsia" class="w-full border rounded-lg p-2">
<option value="0">Pré-eclâmpsia: Não</option>
<option value="1">Pré-eclâmpsia: Sim</option>
</select>

<select name="historico_familiar_chd" class="w-full border rounded-lg p-2">
<option value="0">Histórico Familiar CHD: Não</option>
<option value="1">Histórico Familiar CHD: Sim</option>
</select>

<select name="uso_medicamentos" class="w-full border rounded-lg p-2">
<option value="0">Uso de Medicamentos: Não</option>
<option value="1">Uso de Medicamentos: Sim</option>
</select>

<select name="tabagismo" class="w-full border rounded-lg p-2">
<option value="0">Tabagismo: Não</option>
<option value="1">Tabagismo: Sim</option>
</select>

<select name="alcoolismo" class="w-full border rounded-lg p-2">
<option value="0">Alcoolismo: Não</option>
<option value="1">Alcoolismo: Sim</option>
</select>

</div>


<!-- DADOS FETAIS -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Dados Fetais
</h3>

<input type="number" name="frequencia_cardiaca_fetal"
placeholder="Frequência Cardíaca Fetal"
class="w-full border rounded-lg p-2">

<input type="number" step="0.1" name="circunferencia_cefalica_fetal_mm"
placeholder="Circunferência Cefálica (mm)"
class="w-full border rounded-lg p-2">

<input type="number" step="0.1" name="circunferencia_abdominal_mm"
placeholder="Circunferência Abdominal (mm)"
class="w-full border rounded-lg p-2">

<input type="number" step="0.1" name="comprimento_femur_mm"
placeholder="Comprimento do Fêmur (mm)"
class="w-full border rounded-lg p-2">

<input type="number" step="0.1" name="translucencia_nucal_mm"
placeholder="Translucência Nucal"
class="w-full border rounded-lg p-2">

</div>


<!-- AVALIAÇÃO CARDÍACA -->
<div class="bg-gray-50 p-6 rounded-xl shadow-sm space-y-5">

<h3 class="text-lg font-semibold border-b pb-2 text-gray-700">
Avaliação Cardíaca
</h3>

<select name="doppler_ducto_venoso" class="w-full border rounded-lg p-2">
<option value="">Doppler Ducto Venoso</option>
<option value="Ausente">Ausente</option>
<option value="Fluxo normal">Fluxo normal</option>
<option value="Fluxo aumentado">Fluxo aumentado</option>
<option value="Fluxo reverso">Fluxo reverso</option>
</select>

<input type="number" name="eixo_cardiaco"
placeholder="Eixo Cardíaco"
class="w-full border rounded-lg p-2">

<select name="quatro_camaras" class="w-full border rounded-lg p-2">
<option value="">Quatro Câmaras</option>
<option value="Normal">Normal</option>
<option value="Não visível">Não visível</option>
</select>

<select name="chd_confirmada" class="w-full border rounded-lg p-2">
<option value="0">CHD Confirmada: Não</option>
<option value="1">CHD Confirmada: Sim</option>
</select>

<select name="tipo_chd" class="w-full border rounded-lg p-2">
<option value="">Tipo de CHD</option>
<option value="DSV — Defeito do Septo Ventricular">DSV</option>
<option value="DSA — Defeito do Septo Atrial">DSA</option>
<option value="Tetralogia de Fallot">Tetralogia de Fallot</option>
<option value="TGA — Transposição das Grandes Artérias">TGA</option>
<option value="Hipoplasia do Coração Esquerdo">Hipoplasia do Coração Esquerdo</option>
</select>

</div>

</div>

<div class="mt-10 text-right">

<button type="submit"
class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md">
Salvar Consulta
</button>

</div>

</form>

</div>

@endsection