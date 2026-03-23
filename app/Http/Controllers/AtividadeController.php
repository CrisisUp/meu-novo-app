<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtividadeRequest;
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

    public function store(AtividadeRequest $request)
    {
        Atividade::create($request->validated());

        return redirect()->route('atividade.index')->with('success', 'Atividade criada com sucesso!');
    }

    public function show(Atividade $atividade)
    {
        $atividade->load('idosos');
        $idososDisponiveis = Idoso::whereNull('data_desligamento')
            ->whereDoesntHave('atividades', function($q) use($atividade) {
                $q->where('atividade_id', $atividade->id);
            })->orderBy('nome')->get();

        return view('atividades.show', compact('atividade', 'idososDisponiveis'));
    }

    public function vincularIdoso(Request $request, Atividade $atividade)
    {
        $request->validate([
            'idoso_id' => 'required|exists:idosos,id,data_desligamento,NULL'
        ], [
            'idoso_id.exists' => 'Este idoso está desligado ou não existe e não pode ser vinculado a novas atividades.'
        ]);
        
        $atividade->idosos()->syncWithoutDetaching([$request->idoso_id]);
        
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
        return redirect()->route('atividade.index')->with('success', 'Atividade excluída com sucesso!');
    }
}
