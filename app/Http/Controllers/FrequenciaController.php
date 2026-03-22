<?php

namespace App\Http\Controllers;

use App\Models\Frequencia;
use App\Models\Idoso;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FrequenciaController extends Controller
{
    /**
     * Exibe a lista de frequência para um dia específico.
     */
    public function index(Request $request)
    {
        $data = $request->input('data', Carbon::today()->toDateString());
        
        // Busca todos os idosos e suas frequências para o dia selecionado
        $idosos = Idoso::orderBy('nome')->get();
        
        $frequencias = Frequencia::where('data', $data)->get()->keyBy('idoso_id');

        return view('frequencias.index', compact('idosos', 'frequencias', 'data'));
    }

    /**
     * Salva a frequência em lote de forma otimizada.
     */
    public function store(Request $request)
    {
        $data = $request->input('data');
        $presencas = $request->input('presencas', []); 
        $observacoes = $request->input('observacoes', []);
        $now = now();
        $authId = Auth::id();

        // Buscamos apenas os IDs para otimizar memória
        $idosoIds = Idoso::pluck('id');

        $upsertData = $idosoIds->map(function ($id) use ($data, $presencas, $observacoes, $now, $authId) {
            return [
                'idoso_id' => $id,
                'data' => $data,
                'status' => isset($presencas[$id]) ? 'presente' : 'ausente',
                'observacoes' => $observacoes[$id] ?? null,
                'user_id' => $authId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        // O Upsert utiliza a constraint unique(['idoso_id', 'data']) definida na migration
        // e atualiza apenas os campos especificados se o registro já existir.
        Frequencia::upsert(
            $upsertData, 
            ['idoso_id', 'data'], 
            ['status', 'observacoes', 'user_id', 'updated_at']
        );

        return redirect()->route('frequencia.index', ['data' => $data])
                         ->with('success', 'Frequência atualizada com sucesso!');
    }
}
