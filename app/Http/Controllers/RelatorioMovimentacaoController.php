<?php

namespace App\Http\Controllers;

use App\Models\Idoso;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioMovimentacaoController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Coleta os dados de movimentação para um determinado mês/ano.
     */
    private function getMovimentacaoDados(int $mes, int $ano)
    {
        $dataInicio = Carbon::createFromDate($ano, $mes, 1)->startOfMonth();
        $dataFim = (clone $dataInicio)->endOfMonth();
        $ultimoDiaMesAnterior = (clone $dataInicio)->subDay()->toDateString();

        // IMPORTANTE: Para relatórios históricos, precisamos de withTrashed() 
        // para não "sumir" com quem foi excluído meses depois do relatório.
        
        // 1. SALDO ANTERIOR (Ativos no último dia do mês anterior)
        $idososSaldoAnterior = Idoso::withTrashed()
            ->where('data_admissao', '<=', $ultimoDiaMesAnterior)
            ->where(function($q) use ($ultimoDiaMesAnterior) {
                // Não desligado OU desligado após o mês anterior
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $ultimoDiaMesAnterior);
            })
            ->where(function($q) use ($ultimoDiaMesAnterior) {
                // Não excluído OU excluído após o mês anterior
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>', $ultimoDiaMesAnterior);
            })
            ->get();

        // 2. ENTRADAS (Admitidos no período)
        $idososEntradas = Idoso::withTrashed()
            ->whereBetween('data_admissao', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->get();

        // 3. SAÍDAS (Desligados no período - Ignora exclusões de sistema)
        $idososSaidas = Idoso::withTrashed()
            ->whereBetween('data_desligamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->get();

        // 4. SALDO ATUAL (Ativos no último dia do mês atual)
        $idososSaldoAtual = Idoso::withTrashed()
            ->where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataFim) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $dataFim->toDateString());
            })
            ->where(function($q) use ($dataFim) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>', $dataFim->toDateString());
            })
            ->get();

        return [
            'saldoAnterior' => $this->agruparPorFaixaESexo($idososSaldoAnterior, $dataFim),
            'entradas' => $this->agruparPorFaixaESexo($idososEntradas, $dataFim),
            'saidas' => $this->agruparPorFaixaESexo($idososSaidas, $dataFim),
            'saldoAtual' => $this->agruparPorFaixaESexo($idososSaldoAtual, $dataFim),
            'stats' => $this->calcularStatsGerais($dataInicio, $dataFim),
            'totalAtendidos' => Idoso::withTrashed()
                ->where('data_admissao', '<=', $dataFim->toDateString())
                ->where(function($q) use ($dataInicio) {
                    $q->whereNull('data_desligamento')
                      ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
                })
                ->where(function($q) use ($dataInicio) {
                    $q->whereNull('deleted_at')
                      ->orWhere('deleted_at', '>=', $dataInicio->toDateString());
                })->count()
        ];
    }

    private function agruparPorFaixaESexo($colecao, $dataReferencia)
    {
        $res = $this->getEmptyObject();

        foreach ($colecao as $u) {
            // CÁLCULO DE IDADE RETROATIVA: Qual idade ele tinha na data do relatório?
            $nascimento = Carbon::parse($u->data_nascimento);
            $idadeNaEpoca = $nascimento->diffInYears($dataReferencia);

            $isMasc = in_array($u->sexo, ['cis_m', 'trans_m']);
            $prefixo = $isMasc ? 'm_' : 'f_';

            if ($idadeNaEpoca >= 60 && $idadeNaEpoca <= 64) $chave = $prefixo . '60_64';
            elseif ($idadeNaEpoca >= 65 && $idadeNaEpoca <= 69) $chave = $prefixo . '65_69';
            elseif ($idadeNaEpoca >= 70 && $idadeNaEpoca <= 74) $chave = $prefixo . '70_74';
            elseif ($idadeNaEpoca >= 75 && $idadeNaEpoca <= 79) $chave = $prefixo . '75_79';
            elseif ($idadeNaEpoca >= 80) $chave = $prefixo . '80_mais';
            else continue;

            $res->$chave++;
        }

        return $res;
    }

    private function calcularStatsGerais($dataInicio, $dataFim)
    {
        $usuariosAtendidos = Idoso::withTrashed()
            ->where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataInicio) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
            })
            ->where(function($q) use ($dataInicio) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>=', $dataInicio->toDateString());
            })
            ->get();

        $stats = [
            'sexo' => ['M' => 0, 'F' => 0, 'Outros' => 0],
            'identidade' => [
                'cis_f' => 0, 'cis_m' => 0, 'trans_f' => 0, 'trans_m' => 0, 'agenero' => 0, 'nao_declarado' => 0
            ],
            'raca_cor' => [
                'branca' => 0, 'preta' => 0, 'parda' => 0, 'amarela' => 0, 'indigena' => 0, 'nao_informado' => 0
            ],
            'sexo_raca' => [
                'M' => [
                    'branca' => 0, 'preta' => 0, 'parda' => 0, 'amarela' => 0, 'indigena' => 0, 'nao_informado' => 0
                ],
                'F' => [
                    'branca' => 0, 'preta' => 0, 'parda' => 0, 'amarela' => 0, 'indigena' => 0, 'nao_informado' => 0
                ],
                'Outros' => [
                    'branca' => 0, 'preta' => 0, 'parda' => 0, 'amarela' => 0, 'indigena' => 0, 'nao_informado' => 0
                ],
            ],
            'grau_dependencia' => [
                'I' => 0, 'II' => 0, 'III' => 0
            ],
            'saidas_permanencia' => []
        ];

        foreach ($usuariosAtendidos as $u) {
            $sexoKey = 'Outros';
            if (in_array($u->sexo, ['cis_m', 'trans_m'])) $sexoKey = 'M';
            elseif (in_array($u->sexo, ['cis_f', 'trans_f'])) $sexoKey = 'F';
            
            $stats['sexo'][$sexoKey]++;

            $racaKey = $u->raca_cor ?? 'nao_informado';
            $stats['identidade'][$u->sexo ?? 'nao_declarado']++;
            $stats['raca_cor'][$racaKey]++;
            
            // Cruzamento Sexo x Raça
            $stats['sexo_raca'][$sexoKey][$racaKey]++;

            $stats['grau_dependencia'][$u->grau_dependencia ?? 'I']++;

            if ($u->data_desligamento && 
                $u->data_desligamento >= $dataInicio->toDateString() && 
                $u->data_desligamento <= $dataFim->toDateString()) {
                
                $admissao = Carbon::parse($u->data_admissao);
                $desligamento = Carbon::parse($u->data_desligamento);
                $meses = $admissao->diffInMonths($desligamento);
                $dias = $admissao->diffInDays($desligamento);
                
                if ($meses <= 6) {
                    $bucket = "Até 6 meses ({$dias} dias)";
                } elseif ($meses <= 12) {
                    $bucket = "Mais de 6 meses a 1 ano ({$dias} dias)";
                } elseif ($meses <= 36) {
                    $bucket = "Mais de 1 ano a 3 anos ({$dias} dias)";
                } else {
                    $bucket = "Mais de 3 anos ({$dias} dias)";
                }

                $stats['saidas_permanencia'][] = [
                    'nome' => $u->nome,
                    'permanencia' => $bucket,
                    'meses' => $meses,
                    'motivo' => $u->motivo_desligamento
                ];
            }
        }

        return $stats;
    }

    public function index(Request $request)
    {
        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $dados = $this->getMovimentacaoDados($mes, $ano);

        return view('relatorios.movimentacao', array_merge($dados, ['mes' => $mes, 'ano' => $ano]));
    }

    public function exportarPdf(Request $request)
    {
        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $dados = $this->getMovimentacaoDados($mes, $ano);
        $pdf = $this->exportService->gerarRelatorioMovimentacaoPdf($dados, $mes, $ano);

        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;
        return $pdf->download("movimentacao-cdi-{$mesNome}-{$ano}.pdf");
    }

    private function getEmptyObject()
    {
        return (object) [
            'm_60_64' => 0, 'f_60_64' => 0,
            'm_65_69' => 0, 'f_65_69' => 0,
            'm_70_74' => 0, 'f_70_74' => 0,
            'm_75_79' => 0, 'f_75_79' => 0,
            'm_80_mais' => 0, 'f_80_mais' => 0,
        ];
    }
}
