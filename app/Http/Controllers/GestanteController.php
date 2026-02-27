<?php

namespace App\Http\Controllers;

use App\Models\Gestante;
use Illuminate\Http\Request;

class GestanteController extends Controller
{
    public function index()
    {
        $gestantes = Gestante::withCount('consultas')->orderBy('nome')->get();

        return view('gestantes.index', compact('gestantes'));
    }

    public function create()
    {
        return view('gestantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            // 'gestante_id' => 'nullable|unique:gestantes,gestante_id',
            'data_nascimento' => 'required|date'
        ]);

        Gestante::create($request->only('nome', 'data_nascimento'));

        return redirect()->route('gestantes.index')->with('success', 'Gestante cadastrada com sucesso!');
    }

    public function show(Gestante $gestante)
    {
        $gestante->load(['consultas' => function ($query) {
            $query->orderBy('data_consulta');
        }]);

        return view('gestantes.show', compact('gestante'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
