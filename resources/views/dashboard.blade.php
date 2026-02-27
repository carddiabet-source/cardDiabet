@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Título -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800">
            Dashboard
        </h2>
        <p class="text-sm text-gray-500">
            Visão geral do cardioprenatal
        </p>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Gestantes -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">Gestantes</p>
            <p class="text-3xl font-bold text-blue-600">
                {{ $totalGestantes }}
            </p>
        </div>

        <!-- Consultas -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">Consultas</p>
            <p class="text-3xl font-bold text-green-600">
                {{ $totalConsultas }}
            </p>
        </div>

        <!-- CHD -->
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-sm text-gray-500">CHD confirmadas</p>
            <p class="text-3xl font-bold text-red-600">
                {{ $chdConfirmadas }}
            </p>
        </div>

    </div>

    <!-- Ações rápidas -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">
            Ações rápidas
        </h3>

        <div class="flex flex-col md:flex-row gap-4">
            <a href="{{ route('consultas.import') }}"
               class="flex-1 text-center bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">
                Importar CSV
            </a>

            <a href="{{ route('gestantes.index') }}"
               class="flex-1 text-center bg-gray-600 text-white py-3 rounded hover:bg-gray-700 transition">
                Ver Gestantes
            </a>
        </div>
    </div>

</div>
@endsection
