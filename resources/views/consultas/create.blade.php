@extends('layouts.app')

@section('title', 'Nova Consulta')

@section('content')


    <div class="bg-white shadow rounded-lg p-6">

        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Cadastrar nova consulta
        </h2>

        <form action="{{ route('consultas.store', ['id' => $id]) }}" method="POST" class="space-y-6">
            @csrf

            <input type="hidden" name="id" value="{{ request()->route('id') }}">

            <div>
                <label for="data_consulta" class="block text-sm font-medium text-gray-700">Data da Consulta</label>
                <input type="date" name="data_consulta" id="data_consulta"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>


        @endsection
