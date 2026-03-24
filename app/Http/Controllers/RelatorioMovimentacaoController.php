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
        $hojeStr = today()->toDateString();

        // Lógica de contagem em PHP para garantir precisão máxima e consistência com o resto do sistema
        // Em vez de SQL complexo com strftime (que falha no SQLite/MySQL de formas diferentes),
        // buscamos os registros e processamos a lógica de idade no PHP.
        
        // 1. SALDO ANTERIOR (Ativos no último dia do mês anterior)
        $idososSaldoAnterior = Idoso::where('data_admissao', '<=', $ultimoDiaMesAnterior)
            ->where(function($q) use ($ultimoDiaMesAnterior) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $ultimoDiaMesAnterior);
            })
            ->get();

        // 2. ENTRADAS (Admitidos no período)
        $idososEntradas = Idoso::whereBetween('data_admissao', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->get();

        // 3. SAÍDAS (Desligados no período)
        $idososSaidas = Idoso::whereBetween('data_desligamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->get();

        // 4. SALDO ATUAL (Ativos no último dia do mês atual)
        $idososSaldoAtual = Idoso::where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataFim) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>', $dataFim->toDateString());
            })
            ->get();

        return [
            'saldoAnterior' => $this->agruparPorFaixaESexo($idososSaldoAnterior),
            'entradas' => $this->agruparPorFaixaESexo($idososEntradas),
            'saidas' => $this->agruparPorFaixaESexo($idososSaidas),
            'saldoAtual' => $this->agruparPorFaixaESexo($idososSaldoAtual),
            'stats' => $this->calcularStatsGerais($dataInicio, $dataFim),
            'totalAtendidos' => Idoso::where('data_admissao', '<=', $dataFim->toDateString())
                ->where(function($q) use ($dataInicio) {
                    $q->whereNull('data_desligamento')
                      ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
                })->count()
        ];
    }

    private function agruparPorFaixaESexo($colecao)
    {
        $res = $this->getEmptyObject();

        foreach ($colecao as $u) {
            $idade = $u->idade;
            $isMasc = in_array($u->sexo, ['cis_m', 'trans_m']);
            $prefixo = $isMasc ? 'm_' : 'f_';

            if ($idade >= 60 && $idade <= 64) $chave = $prefixo . '60_64';
            elseif ($idade >= 65 && $idade <= 69) $chave = $prefixo . '65_69';
            elseif ($idade >= 70 && $idade <= 74) $chave = $prefixo . '70_74';
            elseif ($idade >= 75 && $idade <= 79) $chave = $prefixo . '75_79';
            elseif ($idade >= 80) $chave = $prefixo . '80_mais';
            else continue; // Menores de 60 não entram na tabela de controle social

            $res->$chave++;
        }

        return $res;
    }

    private function calcularStatsGerais($dataInicio, $dataFim)
    {
        $usuariosAtendidos = Idoso::where('data_admissao', '<=', $dataFim->toDateString())
            ->where(function($q) use ($dataInicio) {
                $q->whereNull('data_desligamento')
                  ->orWhere('data_desligamento', '>=', $dataInicio->toDateString());
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
