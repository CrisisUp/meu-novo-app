<?php

namespace App\Http\Controllers;

use App\Http\Requests\FrequenciaRequest;
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
        
        // Apenas idosos que estavam ativos na data consultada (ou que são ativos hoje)
        // Usamos withTrashed() para garantir que idosos excluídos após a data continuem aparecendo no histórico
        $idosos = Idoso::withTrashed()
            ->where(function($query) use ($data) {
                $query->whereNull('data_desligamento')
                      ->orWhere('data_desligamento', '>=', $data);
            })
            ->where('data_admissao', '<=', $data)
            ->where(function($query) use ($data) {
                // Idoso não excluído OU excluído após a data consultada
                $query->whereNull('deleted_at')
                      ->orWhere('deleted_at', '>=', $data);
            })
            ->orderBy('nome')
            ->get();
        
        $frequencias = Frequencia::where('data', $data)->get()->keyBy('idoso_id');

        return view('frequencias.index', compact('idosos', 'frequencias', 'data'));
    }

    /**
     * Salva a frequência em lote de forma otimizada.
     */
    public function store(FrequenciaRequest $request)
    {
        $validated = $request->validated();
        $data = $validated['data'];
        $presencas = $validated['presencas'] ?? []; 
        $observacoes = $validated['observacoes'] ?? [];
        $now = now();
        $authId = Auth::id();

        // IMPORTANTE: Apenas idosos ativos na data devem receber registro de frequência
        $idosoIds = Idoso::withTrashed()
            ->where(function($query) use ($data) {
                $query->whereNull('data_desligamento')
                      ->orWhere('data_desligamento', '>=', $data);
            })
            ->where('data_admissao', '<=', $data)
            ->where(function($query) use ($data) {
                $query->whereNull('deleted_at')
                      ->orWhere('deleted_at', '>=', $data);
            })
            ->pluck('id');

        if ($idosoIds->isEmpty()) {
            return redirect()->back()->with('error', 'Não há idosos ativos para registrar frequência nesta data.');
        }

        $upsertData = $idosoIds->map(function ($id) use ($data, $presencas, $observacoes, $now, $authId) {
            $status = isset($presencas[$id]) ? 'presente' : 'ausente';
            return [
                'idoso_id' => $id,
                'data' => $data,
                'status' => $status,
                'observacoes' => $observacoes[$id] ?? null,
                'user_id' => $authId,
                'created_at' => $now,
                'updated_at' => $now,
                // Definimos horários padrão apenas para NOVOS registros de 'presente'
                // Como não estão no 3º parâmetro do upsert, valores existentes NÃO são sobrescritos
                'entrada' => $status == 'presente' ? '08:00:00' : null,
                'saida' => $status == 'presente' ? '17:00:00' : null,
            ];
        })->toArray();

        // No SQLite/MySQL o Upsert pode se comportar diferente com colunas faltantes.
        // Para garantir que entrada/saida não sejam zerados, usamos updateColumns seletivos.
        Frequencia::upsert(
            $upsertData, 
            ['idoso_id', 'data'], 
            ['status', 'observacoes', 'user_id', 'updated_at'] 
            // Note que NÃO incluímos 'entrada' e 'saida' no update para preservá-los
        );

        return redirect()->route('frequencia.index', ['data' => $data])
                         ->with('success', 'Frequência atualizada com sucesso!');
    }
}
