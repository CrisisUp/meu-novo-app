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
        
        $fimMesAnterior = (clone $dataInicio)->subSecond();
        $ultimoDiaMesAnterior = $fimMesAnterior->toDateString();

        // 1. SALDO ANTERIOR (Ativos no último dia do mês anterior)
        $idososSaldoAnterior = Idoso::withTrashed()
            ->where('data_admissao', '<=', $ultimoDiaMesAnterior)
            ->where(function($q) use ($ultimoDiaMesAnterior) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $ultimoDiaMesAnterior);
            })
            ->where(function($q) use ($fimMesAnterior) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>', $fimMesAnterior);
            })
            ->get();

        // 2. ENTRADAS (Admitidos no período)
        $idososEntradas = Idoso::withTrashed()
            ->whereBetween('data_admissao', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->get();

        // 3. SAÍDAS (Quem deixou de ser ativo NESTE mês)
        $idososSaidas = Idoso::withTrashed()
            ->where(function($q) use ($dataInicio, $dataFim) {
                // Caso A: Desligamento oficial ocorreu este mês
                $q->whereBetween('data_desligamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
                // Caso B: Exclusão do sistema ocorreu este mês, mas o idoso ainda era considerado ATIVO no início do mês
                  ->orWhere(function($sq) use ($dataInicio, $dataFim) {
                      $sq->whereBetween('deleted_at', [$dataInicio, $dataFim])
                         ->where(function($ssq) use ($dataInicio) {
                             $ssq->whereNull('data_desligamento')
                                 ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
                         });
                  });
            })
            ->get();

        // 4. SALDO ATUAL (Balanço matemático: Anterior + Entradas - Saídas)
        // Isso deve ser igual aos ativos no último dia do mês.
        $idososSaldoAtual = Idoso::withTrashed()
            ->where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataFim) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $dataFim->toDateString());
            })
            ->where(function($q) use ($dataFim) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>', $dataFim);
            })
            ->get();

        // 5. TOTAL ATENDIDOS (Quem passou pelo serviço em qualquer momento do mês)
        // Regra: Estava ativo no início do mês OU entrou durante o mês.
        $usuariosAtendidos = Idoso::withTrashed()
            ->where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataInicio) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
            })
            ->where(function($q) use ($dataInicio) {
                $q->whereNull('deleted_at')
                  ->orWhere('deleted_at', '>=', $dataInicio);
            })
            ->get();

        return [
            'saldoAnterior' => $this->agruparPorFaixaESexo($idososSaldoAnterior, $fimMesAnterior),
            'entradas' => $this->agruparPorFaixaESexo($idososEntradas, $dataFim),
            'saidas' => $this->agruparPorFaixaESexo($idososSaidas, $dataFim),
            'saldoAtual' => $this->agruparPorFaixaESexo($idososSaldoAtual, $dataFim),
            'stats' => $this->calcularStatsGerais($usuariosAtendidos, $dataInicio, $dataFim),
            'totalAtendidos' => $usuariosAtendidos->count()
        ];
    }

    private function agruparPorFaixaESexo($colecao, $dataReferencia)
    {
        $res = $this->getEmptyObject();

        foreach ($colecao as $u) {
            $nascimento = Carbon::parse($u->data_nascimento);
            $idadeNaEpoca = $nascimento->diffInYears($dataReferencia);

            $isMasc = in_array($u->sexo, ['cis_m', 'trans_m']);
            $prefixo = $isMasc ? 'm_' : 'f_';

            if ($idadeNaEpoca >= 60 && $idadeNaEpoca <= 64) $chave = $prefixo . '60_64';
            elseif ($idadeNaEpoca >= 65 && $idadeNaEpoca <= 69) $chave = $prefixo . '65_69';
            elseif ($idadeNaEpoca >= 70 && $idadeNaEpoca <= 74) $chave = $prefixo . '70_74';
            elseif ($idadeNaEpoca >= 75 && $idadeNaEpoca <= 79) $chave = $prefixo . '75_79';
            elseif ($idadeNaEpoca >= 80) $chave = $prefixo . '80_mais';
            else $chave = $prefixo . '60_64'; // Fallback para não sumir com o idoso da tabela se ele tiver 59 anos mas estiver no CDI

            $res->$chave++;
        }

        return $res;
    }

    private function calcularStatsGerais($usuariosAtendidos, $dataInicio, $dataFim)
    {
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

            $isSaidaNoPeriodo = ($u->data_desligamento && 
                                $u->data_desligamento >= $dataInicio->toDateString() && 
                                $u->data_desligamento <= $dataFim->toDateString()) ||
                                ($u->deleted_at && 
                                $u->deleted_at >= $dataInicio && 
                                $u->deleted_at <= $dataFim);

            if ($isSaidaNoPeriodo) {
                $admissao = Carbon::parse($u->data_admissao);
                $dataSaida = ($u->data_desligamento && $u->data_desligamento <= $dataFim->toDateString()) 
                    ? Carbon::parse($u->data_desligamento) 
                    : $u->deleted_at;
                
                $meses = $admissao->diffInMonths($dataSaida);
                $dias = $admissao->diffInDays($dataSaida);
                
                if ($meses <= 6) {
                    $bucket = "Até 6 meses ({$dias} dias)";
                } elseif ($meses <= 12) {
                    $bucket = "Mais de 6 meses a 1 ano ({$dias} dias)";
                } elseif ($meses <= 36) {
                    $bucket = "Mais de 1 ano a 3 anos ({$dias} dias)";
                } else {
                    $bucket = "Mais de 3 anos ({$dias} dias)";
                }

                $motivo = $u->motivo_desligamento;
                if (!$u->data_desligamento && $u->deleted_at) {
                    $motivo = "Exclusão Administrativa (Sistema)";
                }

                $stats['saidas_permanencia'][] = [
                    'nome' => $u->nome,
                    'permanencia' => $bucket,
                    'meses' => $meses,
                    'motivo' => $motivo
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
