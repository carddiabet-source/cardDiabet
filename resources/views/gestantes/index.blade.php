@extends('layouts.app')

@section('title', 'Gestantes')

@section('content')
    <div class="bg-white shadow rounded-lg p-6">

        <div class="flex flex-row  justify-between items-center mb-6">

            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Gestantes cadastradas
            </h2>

            <a href="{{ route('gestantes.create') }}"
                class="inline-block mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Cadastrar nova gestante
            </a>

        </div>

        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2">Nome</th>
                    <th class="py-2">Consultas</th>
                    <th class="py-2 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gestantes as $gestante)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2">{{ $gestante->nome }}</td>
                        <td class="py-2">{{ $gestante->consultas_count }}</td>
                        <td class="py-2 text-right">
                            <a href="{{ route('gestantes.show', $gestante) }}" class="text-blue-600 hover:underline">
                                Ver detalhes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">
                            Nenhuma gestante encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
