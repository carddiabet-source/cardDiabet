@extends('layouts.auth')

@section('title', 'Login')

@section('subtitle', 'Acesse sua conta')

@section('content')
    <form method="POST" id="loginForm">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                CRM
            </label>
            <input type="text" name="crm" value="{{ old('crm') }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

            @error('crm')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Senha
            </label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2">

            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-md text-base font-medium">

            Entrar
        </button>
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/login', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        throw data;
                    }

                    new Noty({
                        type: 'success',
                        layout: 'topRight',
                        text: 'Login realizado com sucesso!',
                        timeout: 2000
                    }).show();

                    // redirecionamento
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);
                })
                .catch(error => {

                    // erro de validação (422)
                    if (error.errors) {
                        Object.values(error.errors).forEach(messages => {
                            messages.forEach(msg => {
                                new Noty({
                                    type: 'error',
                                    layout: 'topRight',
                                    text: msg,
                                    timeout: 3000
                                }).show();
                            });
                        });
                        return;
                    }

                    // erro de login (401)
                    new Noty({
                        type: 'error',
                        layout: 'topRight',
                        text: error.message || 'Erro ao tentar login',
                        timeout: 3000
                    }).show();
                });
        });
    </script>

@endsection
