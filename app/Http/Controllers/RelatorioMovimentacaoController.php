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

        return [
            'saldoAnterior' => $saldoAnterior ?: $this->getEmptyObject(),
            'entradas' => $entradas ?: $this->getEmptyObject(),
            'saidas' => $saidas ?: $this->getEmptyObject(),
            'saldoAtual' => $saldoAtual ?: $this->getEmptyObject(),
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
