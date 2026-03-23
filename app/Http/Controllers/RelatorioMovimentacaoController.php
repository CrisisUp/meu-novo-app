<?php

namespace App\Http\Controllers;

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
        $ultimoDiaMesAtual = $dataFim->toDateString();

        $sqlBase = "
            SELECT *, 
            (strftime('%Y', ?) - strftime('%Y', data_nascimento)) as idade 
            FROM idosos
        ";

        $caseFaixas = "
            COALESCE(SUM(CASE WHEN idade BETWEEN 60 AND 64 AND sexo IN ('cis_m', 'trans_m') THEN 1 ELSE 0 END), 0) as m_60_64,
            COALESCE(SUM(CASE WHEN idade BETWEEN 60 AND 64 AND sexo IN ('cis_f', 'trans_f') THEN 1 ELSE 0 END), 0) as f_60_64,
            COALESCE(SUM(CASE WHEN idade BETWEEN 65 AND 69 AND sexo IN ('cis_m', 'trans_m') THEN 1 ELSE 0 END), 0) as m_65_69,
            COALESCE(SUM(CASE WHEN idade BETWEEN 65 AND 69 AND sexo IN ('cis_f', 'trans_f') THEN 1 ELSE 0 END), 0) as f_65_69,
            COALESCE(SUM(CASE WHEN idade BETWEEN 70 AND 74 AND sexo IN ('cis_m', 'trans_m') THEN 1 ELSE 0 END), 0) as m_70_74,
            COALESCE(SUM(CASE WHEN idade BETWEEN 70 AND 74 AND sexo IN ('cis_f', 'trans_f') THEN 1 ELSE 0 END), 0) as f_70_74,
            COALESCE(SUM(CASE WHEN idade >= 75 AND sexo IN ('cis_m', 'trans_m') THEN 1 ELSE 0 END), 0) as m_75_mais,
            COALESCE(SUM(CASE WHEN idade >= 75 AND sexo IN ('cis_f', 'trans_f') THEN 1 ELSE 0 END), 0) as f_75_mais
        ";

        $saldoAnterior = DB::selectOne("SELECT $caseFaixas FROM ($sqlBase) WHERE data_admissao <= ? AND (data_desligamento IS NULL OR data_desligamento > ?)", [$ultimoDiaMesAnterior, $ultimoDiaMesAnterior, $ultimoDiaMesAnterior]);
        $entradas = DB::selectOne("SELECT $caseFaixas FROM ($sqlBase) WHERE data_admissao BETWEEN ? AND ?", [$ultimoDiaMesAtual, $dataInicio->toDateString(), $dataFim->toDateString()]);
        $saidas = DB::selectOne("SELECT $caseFaixas FROM ($sqlBase) WHERE data_desligamento BETWEEN ? AND ?", [$ultimoDiaMesAtual, $dataInicio->toDateString(), $dataFim->toDateString()]);
        $saldoAtual = DB::selectOne("SELECT $caseFaixas FROM ($sqlBase) WHERE data_admissao <= ? AND (data_desligamento IS NULL OR data_desligamento > ?)", [$ultimoDiaMesAtual, $ultimoDiaMesAtual, $ultimoDiaMesAtual]);

        // Estatísticas detalhadas dos usuários atendidos no mês
        $usuariosAtendidosSql = "
            SELECT * FROM idosos 
            WHERE data_admissao <= ? 
            AND (data_desligamento IS NULL OR data_desligamento >= ?)
        ";
        $usuariosAtendidos = DB::select($usuariosAtendidosSql, [$dataFim->toDateString(), $dataInicio->toDateString()]);

        $stats = [
            'sexo' => ['M' => 0, 'F' => 0, 'Outros' => 0],
            'identidade' => [
                'cis_f' => 0, 'cis_m' => 0, 'trans_f' => 0, 'trans_m' => 0, 'agenero' => 0, 'nao_declarado' => 0
            ],
            'raca_cor' => [
                'branca' => 0, 'preta' => 0, 'parda' => 0, 'amarela' => 0, 'indigena' => 0, 'nao_informado' => 0
            ],
            'grau_dependencia' => [
                'I' => 0, 'II' => 0, 'III' => 0
            ],
            'saidas_permanencia' => []
        ];

        foreach ($usuariosAtendidos as $u) {
            // Sexo (agrupado por M/F/O)
            if (in_array($u->sexo, ['cis_m', 'trans_m'])) $stats['sexo']['M']++;
            elseif (in_array($u->sexo, ['cis_f', 'trans_f'])) $stats['sexo']['F']++;
            else $stats['sexo']['Outros']++;

            // Identidade de Gênero
            $stats['identidade'][$u->sexo ?? 'nao_declarado']++;

            // Raça/Cor
            $stats['raca_cor'][$u->raca_cor ?? 'nao_informado']++;

            // Grau de Dependência
            $stats['grau_dependencia'][$u->grau_dependencia ?? 'I']++;

            // Tempo de permanência para quem saiu NESTE mês
            if ($u->data_desligamento && 
                $u->data_desligamento >= $dataInicio->toDateString() && 
                $u->data_desligamento <= $dataFim->toDateString()) {
                
                $admissao = Carbon::parse($u->data_admissao);
                $desligamento = Carbon::parse($u->data_desligamento);
                $meses = $admissao->diffInMonths($desligamento);
                
                if ($meses < 6) $bucket = 'Menos de 6 meses';
                elseif ($meses < 12) $bucket = '6 a 12 meses';
                elseif ($meses < 24) $bucket = '1 a 2 anos';
                else $bucket = 'Mais de 2 anos';

                $stats['saidas_permanencia'][] = [
                    'nome' => $u->nome,
                    'permanencia' => $bucket,
                    'meses' => $meses,
                    'motivo' => $u->motivo_desligamento
                ];
            }
        }

        return [
            'saldoAnterior' => $saldoAnterior ?: $this->getEmptyObject(),
            'entradas' => $entradas ?: $this->getEmptyObject(),
            'saidas' => $saidas ?: $this->getEmptyObject(),
            'saldoAtual' => $saldoAtual ?: $this->getEmptyObject(),
            'stats' => $stats,
            'totalAtendidos' => count($usuariosAtendidos)
        ];
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
            'm_75_mais' => 0, 'f_75_mais' => 0,
        ];
    }
}
