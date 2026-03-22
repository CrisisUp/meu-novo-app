<?php

namespace App\Http\Controllers;

use App\Models\Idoso;
use App\Http\Requests\IdosoRequest;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class IdosoController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Exibe uma prévia do relatório e permite escolher mês/ano.
     */
    public function relatorioPreview(Idoso $idoso, Request $request)
    {
        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $frequencias = $idoso->frequencias()
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->orderBy('data')
            ->get();

        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return view('idosos.relatorio-preview', compact('idoso', 'frequencias', 'mesNome', 'mes', 'ano'));
    }

    /**
     * Gera o relatório mensal de frequência em PDF.
     */
    public function gerarRelatorio(Idoso $idoso, Request $request)
    {
        $mes = (int) $request->input('mes', date('m'));
        $ano = (int) $request->input('ano', date('Y'));

        $pdf = $this->exportService->gerarRelatorioPdf($idoso, $mes, $ano);
        
        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return $pdf->download("frequencia-{$idoso->nome}-{$mesNome}-{$ano}.pdf");
    }

    /**
     * Exporta a lista de idosos para CSV respeitando os filtros ativos.
     */
    public function exportarCsv(Request $request)
    {
        $idosos = Idoso::filtered($request->input('search'), $request->input('filtro'))
            ->orderBy('nome')
            ->get();

        $csvContent = $this->exportService->gerarCsvIdosos($idosos);
        $fileName = 'idosos-cdi-' . date('Y-m-d') . '.csv';

        return response($csvContent, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $filtro = $request->input('filtro');

        $idosos = Idoso::filtered($search, $filtro)
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('idosos.index', compact('idosos', 'search', 'filtro'));
    }

    public function create()
    {
        return view('idosos.create');
    }

    public function store(IdosoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fotos_idosos', 'public');
        }

        Idoso::create($data);

        return redirect()->route('idoso.index')->with('success', 'Idoso cadastrado com sucesso!');
    }

    public function show(Idoso $idoso)
    {
        return view('idosos.show', compact('idoso'));
    }

    public function edit(Idoso $idoso)
    {
        return view('idosos.edit', compact('idoso'));
    }

    public function update(IdosoRequest $request, Idoso $idoso)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($idoso->foto) {
                Storage::disk('public')->delete($idoso->foto);
            }
            $data['foto'] = $request->file('foto')->store('fotos_idosos', 'public');
        }

        $idoso->update($data);

        return redirect()->route('idoso.show', $idoso)->with('success', 'Cadastro atualizado com sucesso!');
    }

    public function destroy(Idoso $idoso)
    {
        if ($idoso->foto) {
            Storage::disk('public')->delete($idoso->foto);
        }
        $idoso->delete();
        return redirect()->route('idoso.index')->with('success', 'Cadastro excluído com sucesso!');
    }
}
