<?php

namespace App\Http\Controllers;

use App\Models\PresencaEquipe;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresencaEquipeController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function registrarEntrada(Request $request)
    {
        $presenca = PresencaEquipe::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'data' => Carbon::today()->toDateString(),
            ],
            [
                'entrada' => Carbon::now()->toTimeString(),
            ]
        );

        if (!$presenca->wasRecentlyCreated) {
            return back()->with('error', 'Você já registrou sua entrada hoje às ' . $presenca->entrada);
        }

        return back()->with('success', 'Entrada registrada com sucesso!');
    }

    public function registrarSaida(Request $request)
    {
        $presenca = PresencaEquipe::where('user_id', Auth::id())
            ->where('data', Carbon::today()->toDateString())
            ->first();

        if (!$presenca) {
            return back()->with('error', 'Registro de entrada não encontrado para hoje.');
        }

        if ($presenca->saida) {
            return back()->with('error', 'Você já registrou sua saída hoje às ' . $presenca->saida);
        }

        $presenca->update([
            'saida' => Carbon::now()->toTimeString(),
        ]);

        return back()->with('success', 'Saída registrada com sucesso!');
    }

    /**
     * Visualização do relatório de ponto de um usuário.
     */
    public function relatorioPonto(User $user, Request $request)
    {
        // Apenas admin pode ver de todos, ou o próprio usuário pode ver o dele
        if (!Auth::user()->can('admin-access') && Auth::id() !== $user->id) {
            abort(403);
        }

        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $pontos = PresencaEquipe::where('user_id', $user->id)
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->orderBy('data')
            ->get();

        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return view('users.relatorio-ponto', compact('user', 'pontos', 'mesNome', 'mes', 'ano'));
    }

    /**
     * Exportação do ponto para PDF.
     */
    public function exportarRelatorioPonto(User $user, Request $request)
    {
        if (!Auth::user()->can('admin-access') && Auth::id() !== $user->id) {
            abort(403);
        }

        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $pdf = $this->exportService->gerarRelatorioPontoPdf($user, $mes, $ano);
        
        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return $pdf->download("ponto-{$user->name}-{$mesNome}-{$ano}.pdf");
    }
}
