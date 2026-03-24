<?php

namespace App\Http\Controllers;

use App\Http\Requests\EncaminhamentoRequest;
use App\Models\Encaminhamento;
use App\Models\Idoso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncaminhamentoController extends Controller
{
    /**
     * Lista todos os encaminhamentos.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $encaminhamentos = Encaminhamento::with([
            'idoso' => function ($query) {
                $query->withTrashed();
            }, 
            'profissional'
        ])
            ->when($search, function ($query, $search) {
                return $query->whereHas('idoso', function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                })->orWhere('instituicao_destino', 'like', "%{$search}%");
            })
            ->orderByDesc('data_encaminhamento')
            ->paginate(10);

        return view('encaminhamentos.index', compact('encaminhamentos', 'search'));
    }

    /**
     * Exibe o formulário de criação para um idoso específico.
     */
    public function create(Request $request)
    {
        $idosos = Idoso::whereNull('data_desligamento')->orderBy('nome')->get();
        $idosoSelecionado = $request->input('idoso_id');

        return view('encaminhamentos.create', compact('idosos', 'idosoSelecionado'));
    }

    /**
     * Salva o encaminhamento.
     */
    public function store(EncaminhamentoRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        Encaminhamento::create($data);

        return redirect()->route('encaminhamento.index')->with('success', 'Encaminhamento registrado com sucesso!');
    }

    public function destroy(Encaminhamento $encaminhamento)
    {
        $encaminhamento->delete();
        return redirect()->route('encaminhamento.index')->with('success', 'Registro removido.');
    }
}
