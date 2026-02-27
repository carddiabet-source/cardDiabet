@extends('layouts.auth')

@section('title', 'Recuperar senha')

@section('subtitle', 'Informe seu email')

@section('content')
    <form method="POST" action="#">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Email
            </label>
            <input type="email"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Enviar link de recuperação
        </button>

        <div class="mt-4 text-center">
            <a href="#"
               class="text-sm text-gray-500 hover:underline">
                Voltar para o login
            </a>
        </div>
    </form>
@endsection
