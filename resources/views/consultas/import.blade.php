@extends('layouts.app')

@section('title', 'Importar Consultas')

@section('content')
    <div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            Importar consultas (CSV)
        </h2>

        <form id="importForm" class="space-y-4">
            @csrf

            <input type="file" name="csv" accept=".csv"
                class="block w-full text-sm text-gray-600
                   file:mr-4 file:py-2 file:px-4
                   file:rounded file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100"
                required>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Importar CSV
            </button>
        </form>

        <div id="feedback" class="mt-4 text-sm hidden"></div>
    </div>

    <script>
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const feedback = document.getElementById('feedback');

            feedback.classList.add('hidden');

            try {
                const response = await fetch("{{ route('consultas.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                feedback.classList.remove('hidden');
                feedback.className = 'mt-4 text-sm text-green-600';
                feedback.innerText = data.message;

            } catch (error) {
                feedback.classList.remove('hidden');
                feedback.className = 'mt-4 text-sm text-red-600';
                feedback.innerText = 'Erro ao importar o arquivo';
            }
        });
    </script>
@endsection
