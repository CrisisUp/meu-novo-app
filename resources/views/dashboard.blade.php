<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Painel de Controle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="alert-success mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Boas-vindas e Registro de Ponto -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200 mb-8">
                <div class="p-8 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold mb-2 text-slate-800">Olá, {{ Auth::user()->name }}!</h3>
                        <p class="text-slate-500 font-medium">Bem-vindo ao Sistema de Gestão CDI. Veja o resumo de hoje.</p>
                    </div>
                    
                    <div class="mt-6 md:mt-0 flex space-x-3">
                        @if(!$meuPonto)
                            <form action="{{ route('ponto.entrada') }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:scale-105 active:scale-95">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Registrar Entrada
                                </button>
                            </form>
                        @elseif(!$meuPonto->saida)
                            <form action="{{ route('ponto.saida') }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-sm transition-all shadow-lg hover:scale-105 active:scale-95">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Registrar Saída
                                </button>
                            </form>
                            <div class="text-right ml-4 hidden md:block">
                                <span class="block text-xs font-bold text-slate-400 uppercase">Entrada às:</span>
                                <span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($meuPonto->entrada)->format('H:i') }}</span>
                            </div>
                        @else
                            <div class="flex flex-col items-end">
                                <div class="bg-slate-50 px-6 py-3 rounded-lg border border-slate-200 text-slate-500 font-bold text-sm flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Jornada Finalizada
                                </div>
                                <a href="{{ route('ponto.historico', Auth::user()) }}" class="text-[10px] text-slate-400 font-bold uppercase mt-2 hover:text-emerald-600 transition-colors">
                                    Ver meu histórico mensal
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estatísticas Reais -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card Idosos -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Atendidos</span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $totalIdosos }}</div>
                    <p class="text-sm text-slate-400 mt-1">Idosos cadastrados</p>
                </div>

                <!-- Card Equipe Presente -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">No Posto</span>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $equipeHoje }}</div>
                    <p class="text-sm text-slate-400 mt-1">Colaboradores presentes</p>
                </div>
            </div>

            <!-- Atalhos Rápidos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-slate-800 p-8 rounded-xl shadow-lg text-white">
                    <h4 class="text-lg font-bold mb-4">Ações Rápidas de Cadastro</h4>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('idoso.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-bold text-sm transition-colors">
                            + Novo Idoso
                        </a>
                        @can('admin-access')
                            <a href="{{ route('user.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg font-bold text-sm transition-colors border border-slate-600">
                                + Nova Equipe
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-center">
                    <h4 class="text-slate-800 font-bold mb-2">Prontuários Atualizados</h4>
                    <p class="text-slate-500 text-sm mb-4">Acesse rapidamente a lista completa para consultar prontuários e informações de saúde.</p>
                    <a href="{{ route('idoso.index') }}" class="text-emerald-700 font-bold text-sm hover:underline flex items-center">
                        Ver Lista de Idosos
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
