<?php

namespace App\Http\Controllers;

use App\Http\Resources\GestanteResource;
use App\Models\Gestante;
use Illuminate\Http\Request;

class GestanteController extends Controller
{
    public function index()
    {
        $gestantes = Gestante::withCount('consultas')->orderBy('gestante_id')->paginate(15);

        return view('gestantes.index', compact('gestantes'));
    }

    public function create()
    {
        return view('gestantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestante_id' => 'required|unique:gestantes,gestante_id',
            'data_nascimento' => 'required|date',
        ]);

        $gestante = Gestante::create($request->all());

        return redirect()->route('gestantes.show', $gestante->id)->with('success', 'Gestante cadastrada com sucesso!');
    }

    public function show(Gestante $gestante)
    {
        $gestante->load(['consultas' => function ($query) {
            $query->orderBy('consulta_numero');
        }]);

        // Passa a gestante e suas consultas para a view
        return view('gestantes.show', compact('gestante'));
    }

    public function edit($id)
    {
        $gestante = Gestante::findOrFail($id);
        return view('gestantes.edit', compact('gestante'));

    }

    public function update(Request $request, $id)
    {
         $gestante = Gestante::findOrFail($id);

            $request->validate([
                'gestante_id' => 'required|unique:gestantes,gestante_id,' . $gestante->id,
                'data_nascimento' => 'required|date',
            ]);

            $gestante->update($request->all());

            return redirect()->route('gestantes.show', $gestante->id)->with('success', 'Dados da gestante atualizados com sucesso!');
    }

    public function destroy($id)
    {
         $gestante = Gestante::findOrFail($id);
         $gestante->delete();

          return redirect()->route('gestantes.index')->with('success', 'Gestante removida com sucesso!');
    }
}
