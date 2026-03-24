<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idoso;
use App\Models\PresencaEquipe;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Exibe a página de boas-vindas (Welcome).
     */
    public function welcome()
    {
        $totalUsers = User::count();
        $totalIdosos = Idoso::whereNull('data_desligamento')->count();
        
        return view('welcome', compact('totalUsers', 'totalIdosos'));
    }

    /**
     * Exibe o painel de controle (Dashboard).
     */
    public function index()
    {
        // Contagem apenas de idosos ativos (sem data de desligamento)
        $totalIdosos = Idoso::whereNull('data_desligamento')->count();
        
        // Equipe presente hoje (deu entrada e ainda não deu saída)
        $equipeHoje = PresencaEquipe::where('data', Carbon::today()->toDateString())
            ->whereNull('saida')
            ->count();

        // Registro de ponto do usuário logado hoje
        $meuPonto = PresencaEquipe::where('user_id', Auth::id())
            ->where('data', Carbon::today()->toDateString())
            ->first();

        // Dados para os gráficos
        $statsGrau = $this->dashboardService->getGrauDependenciaStats();
        $statsAtividades = $this->dashboardService->getAtividadesStats();
        $movimentacaoMensal = $this->dashboardService->getMovimentacaoMensal();
        $statsFaixas = $this->dashboardService->getFaixaEtariaStats();

        return view('dashboard', compact(
            'totalIdosos', 
            'equipeHoje', 
            'meuPonto',
            'statsGrau',
            'statsAtividades',
            'movimentacaoMensal',
            'statsFaixas'
        ));
    }
}
