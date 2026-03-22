<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Idoso;
use App\Models\PresencaEquipe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Exibe a página de boas-vindas (Welcome).
     */
    public function welcome()
    {
        $totalUsers = User::count();
        $totalIdosos = Idoso::count();
        
        return view('welcome', compact('totalUsers', 'totalIdosos'));
    }

    /**
     * Exibe o painel de controle (Dashboard).
     */
    public function index()
    {
        $totalIdosos = Idoso::count();
        
        // Equipe presente hoje (deu entrada e ainda não deu saída)
        $equipeHoje = PresencaEquipe::where('data', Carbon::today()->toDateString())
            ->whereNull('saida')
            ->count();

        // Registro de ponto do usuário logado hoje
        $meuPonto = PresencaEquipe::where('user_id', Auth::id())
            ->where('data', Carbon::today()->toDateString())
            ->first();

        return view('dashboard', compact('totalIdosos', 'equipeHoje', 'meuPonto'));
    }
}
