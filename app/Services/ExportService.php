<?php

namespace App\Services;

use App\Models\Idoso;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExportService
{
    /**
     * Gera o relatório mensal de frequência em PDF.
     */
    public function gerarRelatorioPdf(Idoso $idoso, int $mes, int $ano)
    {
        $frequencias = $idoso->frequencias()
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->orderBy('data')
            ->get();

        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return Pdf::loadView('idosos.relatorio-pdf', [
            'idoso' => $idoso,
            'frequencias' => $frequencias,
            'mesNome' => ucfirst($mesNome),
            'mes' => $mes,
            'ano' => $ano
        ]);
    }

    /**
     * Gera o relatório de ponto mensal de um funcionário em PDF.
     */
    public function gerarRelatorioPontoPdf(\App\Models\User $user, int $mes, int $ano)
    {
        $pontos = \App\Models\PresencaEquipe::where('user_id', $user->id)
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->orderBy('data')
            ->get();

        $mesNome = Carbon::createFromDate($ano, $mes, 1)->locale('pt_BR')->monthName;

        return Pdf::loadView('users.relatorio-ponto-pdf', [
            'user' => $user,
            'pontos' => $pontos,
            'mesNome' => ucfirst($mesNome),
            'mes' => $mes,
            'ano' => $ano
        ]);
    }

    /**
     * Gera o conteúdo de um arquivo CSV baseado em uma coleção de idosos.
     */
    public function gerarCsvIdosos(Collection $idosos): string
    {
        $handle = fopen('php://temp', 'r+');
        
        // Adiciona o BOM para o Excel abrir corretamente em UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); 
        
        fputcsv($handle, ['ID', 'Nome', 'CPF', 'Data Nascimento', 'Idade', 'Responsável', 'Telefone', 'Medicamentos', 'Alergias']);

        foreach ($idosos as $idoso) {
            fputcsv($handle, [
                $idoso->id,
                $idoso->nome,
                $idoso->cpf,
                $idoso->data_nascimento,
                Carbon::parse($idoso->data_nascimento)->age,
                $idoso->contato_emergencia_nome,
                $idoso->contato_emergencia_telefone,
                $idoso->medicamentos,
                $idoso->alergias,
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return $contents;
    }
}
