<?php

namespace App\Services;

use App\Models\Idoso;
use App\Models\Atividade;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get statistics for Degree of Dependency.
     */
    public function getGrauDependenciaStats()
    {
        return Idoso::whereNull('data_desligamento')
            ->select('grau_dependencia', DB::raw('count(*) as total'))
            ->groupBy('grau_dependencia')
            ->get()
            ->pluck('total', 'grau_dependencia')
            ->toArray();
    }

    /**
     * Get statistics for Age Groups.
     */
    public function getFaixaEtariaStats()
    {
        $idosos = Idoso::whereNull('data_desligamento')->get();
        
        $stats = [
            '60-64 anos' => 0,
            '65-69 anos' => 0,
            '70-74 anos' => 0,
            '75-79 anos' => 0,
            '80 anos ou mais' => 0,
            'Menor de 60 anos' => 0,
        ];

        foreach ($idosos as $idoso) {
            $faixa = $idoso->faixa_etaria;
            if (isset($stats[$faixa])) {
                $stats[$faixa]++;
            }
        }

        return array_filter($stats); // Remove faixas zeradas para o gráfico ficar limpo
    }

    /**
     * Get statistics for Activities (Enrollments).
     */
    public function getAtividadesStats()
    {
        // Apenas conta idosos ativos (sem data de desligamento) vinculados
        return Atividade::withCount(['idosos' => function ($query) {
                $query->whereNull('data_desligamento');
            }])
            ->get()
            ->mapWithKeys(function ($item) {
                // Combina nome e dia para evitar colisões no gráfico (ex: Fisioterapia - segunda)
                $label = $item->nome . ' (' . ucfirst($item->dia_semana) . ')';
                return [$label => $item->idosos_count];
            })
            ->toArray();
    }

    /**
     * Get monthly evolution (admissions vs discharges) for the last 6 months.
     */
    public function getMovimentacaoMensal()
    {
        $months = [];
        $admissions = [];
        $discharges = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M/Y');
            $months[] = $monthName;

            $admissions[] = Idoso::whereYear('data_admissao', $date->year)
                ->whereMonth('data_admissao', $date->month)
                ->count();

            $discharges[] = Idoso::whereYear('data_desligamento', $date->year)
                ->whereMonth('data_desligamento', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'admissoes' => $admissions,
            'desligamentos' => $discharges,
        ];
    }
}
