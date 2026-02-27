@extends('layouts.app')

@section('title', 'Gestantes')

@section('content')


    <div class="bg-white shadow rounded-lg p-6">

        <form method="POST" action="{{ route('gestantes.store') }}">
            @csrf

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 font-bold mb-2">Nome:</label>
                <input type="text" name="nome" id="nome" class="w-full border border-gray-300 rounded-lg p-2" required>
            </div>
{{--
            <div class="mb-4">
                <label for="gestante_id" class="block text-gray-700 font-bold mb-2">Gestante ID:</label>
                <input type="text" name="gestante_id" id="gestante_id" class="w-full border border-gray-300 rounded-lg p-2" required>
            </div> --}}

            <div class="mb-4">
                <label for="data_nascimento" class="block text-gray-700 font-bold mb-2">Data de Nascimento:</label>
                <input type="date" name="data_nascimento" id="data_nascimento" class="w-full border border-gray-300 rounded-lg p-2" required>
            </div>


            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Cadastrar
            </button>

    </div>

@endsection
