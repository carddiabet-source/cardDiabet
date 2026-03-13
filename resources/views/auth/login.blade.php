@extends('layouts.auth')

@section('title', 'Login')
@section('subtitle', 'Acesse sua conta')

@section('content')
<form method="POST" id="loginForm" action="{{ route('login.store') }}">
    @csrf

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">CRM</label>
        <input type="text" name="crm" value="{{ old('crm') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('crm')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2">
        @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-md text-base font-medium">
        Entrar
    </button>
</form>
@endsection