<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Início</a>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Idosos</span>
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Idosos Atendidos') }}
            </h2>
            <a href="{{ route('idoso.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-800 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                + Novo Cadastro
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Barra de Busca e Filtros -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                <form action="{{ route('idoso.index') }}" method="GET">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ $search }}" 
                                placeholder="Buscar por nome ou CPF..." 
                                class="w-full border-slate-200 focus:border-slate-500 focus:ring-slate-500 rounded-lg shadow-sm pl-10 text-slate-600">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-slate-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-900 focus:outline-none transition ease-in-out duration-150">
                            Pesquisar
                        </button>
                        
                        <!-- Botão Exportar -->
                        <a href="{{ route('idoso.exportar-csv', ['search' => $search, 'filtro' => $filtro]) }}" 
                            class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-lg font-bold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition ease-in-out duration-150 shadow-sm"
                            title="Baixar em Excel/CSV">
                            <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            CSV
                        </a>
                    </div>

                    <!-- Filtros Rápidos -->
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Status:</span>
                            
                            <a href="{{ route('idoso.index', ['search' => $search]) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ !$filtro ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }} transition-all">
                                Todos
                            </a>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'sem_cpf']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'sem_cpf' ? 'bg-amber-100 text-amber-800 border-amber-300 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-amber-300' }} transition-all">
                                Ficha Incompleta
                            </a>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'com_medicamento']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'com_medicamento' ? 'bg-blue-100 text-blue-800 border-blue-300 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300' }} transition-all">
                                Medicamentos
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Faixa Etária:</span>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'faixa_60_64']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'faixa_60_64' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-600' }} transition-all">
                                60-64 anos
                            </a>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'faixa_65_69']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'faixa_65_69' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-600' }} transition-all">
                                65-69 anos
                            </a>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'faixa_70_74']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'faixa_70_74' ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-600' }} transition-all">
                                70-74 anos
                            </a>

                            <a href="{{ route('idoso.index', ['search' => $search, 'filtro' => 'faixa_75_mais']) }}" 
                                class="px-3 py-1 text-xs font-bold rounded-full border {{ $filtro == 'faixa_75_mais' ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:border-purple-600' }} transition-all">
                                75+ anos
                            </a>

                            @if($search || $filtro)
                                <a href="{{ route('idoso.index') }}" class="text-slate-400 hover:text-slate-600 text-xs font-bold uppercase underline ml-auto">
                                    Limpar Tudo
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-header">
                            <tr>
                                <th class="table-header-cell">Registro</th>
                                <th class="table-header-cell">Nome</th>
                                <th class="table-header-cell">Idade / Faixa</th>
                                <th class="table-header-cell">CPF / NIS</th>
                                <th class="table-header-cell text-center w-36">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($idosos as $idoso)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="text-xs font-black text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                            {{ $idoso->codigo_registro }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 mr-4 border border-slate-200 shadow-sm">
                                                @if($idoso->foto)
                                                    <img src="{{ asset('storage/' . $idoso->foto) }}" class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-xl font-black text-slate-600">{{ strtoupper(substr($idoso->nome, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="text-xl font-black text-slate-900 leading-tight">{{ $idoso->nome }}</div>
                                                <div class="flex mt-2 space-x-2">
                                                @if(!$idoso->cpf)
                                                    <span class="badge badge-warning">CPF PENDENTE</span>
                                                @endif
                                                @if($idoso->medicamentos)
                                                    <span class="badge badge-info">Uso de Medicação</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="text-lg text-slate-800 font-bold">{{ $idoso->idade }} anos</div>
                                        <div class="mt-1">
                                            @php
                                                $corFaixa = match($idoso->faixa_etaria) {
                                                    '60-64 anos' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    '65-69 anos' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    '70-74 anos' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    '75 anos ou mais' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $corFaixa }}">
                                                {{ $idoso->faixa_etaria }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="text-sm text-slate-600 font-bold font-mono">CPF: {{ $idoso->cpf_masked }}</div>
                                        <div class="text-xs text-slate-800 font-black font-mono mt-1">NIS: {{ $idoso->nis_masked }}</div>
                                    </td>
                                    <td class="table-cell text-center">
                                        <div class="flex items-center justify-center space-x-5">
                                            <a href="{{ route('idoso.show', $idoso) }}" class="text-slate-400 hover:text-slate-700 transition-colors" title="Ver Prontuário">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('idoso.edit', $idoso) }}" class="text-slate-400 hover:text-slate-700 transition-colors" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="table-row">
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                        Nenhum idoso encontrado para "{{ $search }}".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 sm:flex sm:items-center sm:justify-between">
                <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">
                    Total de <strong>{{ $idosos->total() }}</strong> idosos cadastrados
                </div>
                <div class="mt-4 sm:mt-0">
                    {{ $idosos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
