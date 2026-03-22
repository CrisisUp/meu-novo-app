<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Idoso;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    public function index()
    {
        $atividades = Atividade::withCount('idosos')->orderBy('horario')->get();
        return view('atividades.index', compact('atividades'));
    }

    public function create()
    {
        return view('atividades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'dia_semana' => 'required',
            'horario' => 'required',
        ]);

        Atividade::create($request->all());

        return redirect()->route('atividade.index')->with('success', 'Atividade criada!');
    }

    public function show(Atividade $atividade)
    {
        $atividade->load('idosos');
        $idososDisponiveis = Idoso::whereDoesntHave('atividades', function($q) use($atividade) {
            $q->where('atividade_id', $atividade->id);
        })->orderBy('nome')->get();

        return view('atividades.show', compact('atividade', 'idososDisponiveis'));
    }

    public function vincularIdoso(Request $request, Atividade $atividade)
    {
        $request->validate(['idoso_id' => 'required|exists:idosos,id']);
        $atividade->idosos()->attach($request->idoso_id);
        return back()->with('success', 'Idoso vinculado à atividade!');
    }

    public function desvincularIdoso(Atividade $atividade, Idoso $idoso)
    {
        $atividade->idosos()->detach($idoso->id);
        return back()->with('success', 'Idoso removido da atividade.');
    }

    public function destroy(Atividade $atividade)
    {
        $atividade->delete();
        return redirect()->route('atividade.index')->with('success', 'Atividade excluída.');
    }
}
