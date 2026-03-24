<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Início</a>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Relatórios</span>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Movimentação Mensal</span>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Relatório de Movimentação (Controle Social)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtro de Período -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <form action="{{ route('relatorios.movimentacao') }}" method="GET" class="flex items-end gap-4">
                        <div>
                            <x-input-label for="mes" :value="__('Mês de Referência')" />
                            <x-select-input id="mes" name="mes" :isError="$errors->has('mes')" class="mt-1 block w-full">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->locale('pt_BR')->monthName }}
                                    </option>
                                @endfor
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label for="ano" :value="__('Ano')" />
                            <x-select-input id="ano" name="ano" :isError="$errors->has('ano')" class="mt-1 block w-full">
                                @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </x-select-input>
                        </div>
                        <x-primary-button class="bg-slate-800">
                            {{ __('Filtrar Dados') }}
                        </x-primary-button>
                    </form>

                    <!-- Ação de Exportação -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('relatorios.movimentacao.pdf', ['mes' => $mes, 'ano' => $ano]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar PDF Oficial
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabela de Controle Social -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h3 class="text-lg font-bold text-slate-800 uppercase">Movimentação de Usuários</h3>
                        <p class="text-sm text-slate-500">Período: {{ Carbon\Carbon::create()->month((int)$mes)->locale('pt_BR')->monthName }} / {{ $ano }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-slate-300">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] uppercase">
                                    <th rowspan="2" class="border border-slate-300 p-2 font-black text-slate-700">Discriminação</th>
                                    <th colspan="2" class="border border-slate-300 p-2 font-black text-slate-700">60 a 64 anos</th>
                                    <th colspan="2" class="border border-slate-300 p-2 font-black text-slate-700">65 a 69 anos</th>
                                    <th colspan="2" class="border border-slate-300 p-2 font-black text-slate-700">70 a 74 anos</th>
                                    <th colspan="2" class="border border-slate-300 p-2 font-black text-slate-700">75 a 79 anos</th>
                                    <th colspan="2" class="border border-slate-300 p-2 font-black text-slate-700">80 anos ou mais</th>
                                    <th rowspan="2" class="border border-slate-300 p-2 font-black text-slate-700">Total Geral</th>
                                </tr>
                                <tr class="bg-slate-50">
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">M</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">F</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">M</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">F</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">M</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">F</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">M</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">F</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">M</th>
                                    <th class="border border-slate-300 p-1 text-[10px] font-black text-slate-600">F</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $linhas = [
                                        ['label' => 'SALDO ANTERIOR', 'data' => $saldoAnterior, 'bg' => 'bg-white'],
                                        ['label' => 'ENTRADAS (ADMISSÕES)', 'data' => $entradas, 'bg' => 'bg-emerald-50/50'],
                                        ['label' => 'SAÍDAS (DESLIGAMENTOS)', 'data' => $saidas, 'bg' => 'bg-rose-50/50'],
                                        ['label' => 'SALDO ATUAL', 'data' => $saldoAtual, 'bg' => 'bg-slate-100 font-black'],
                                    ];
                                @endphp

                                @foreach ($linhas as $linha)
                                    @php
                                        $d = $linha['data'];
                                        $totalLinha = ($d->m_60_64 ?? 0) + ($d->f_60_64 ?? 0) + 
                                                     ($d->m_65_69 ?? 0) + ($d->f_65_69 ?? 0) + 
                                                     ($d->m_70_74 ?? 0) + ($d->f_70_74 ?? 0) + 
                                                     ($d->m_75_79 ?? 0) + ($d->f_75_79 ?? 0) + 
                                                     ($d->m_80_mais ?? 0) + ($d->f_80_mais ?? 0);
                                    @endphp
                                    <tr class="{{ $linha['bg'] }}">
                                        <td class="border border-slate-300 p-3 text-xs font-bold text-slate-700">{{ $linha['label'] }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->m_60_64 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->f_60_64 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->m_65_69 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->f_65_69 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->m_70_74 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->f_70_74 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->m_75_79 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->f_75_79 ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->m_80_mais ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm">{{ $d->f_80_mais ?? 0 }}</td>
                                        <td class="border border-slate-300 p-3 text-center text-sm font-black text-slate-900">{{ $totalLinha }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 p-4 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Resumo da Lógica</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            O cálculo segue a regra oficial: <strong>Saldo Anterior</strong> (ativos no último dia do mês anterior) + 
                            <strong>Entradas</strong> (admitidos no período) - <strong>Saídas</strong> (desligados no período) = 
                            <strong>Saldo Atual</strong> (ativos no último dia do mês selecionado).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detalhamento dos Usuários Atendidos -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-8">
                    <div class="text-center mb-8 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800 uppercase">Perfil dos Usuários Atendidos</h3>
                        <p class="text-sm text-slate-500">Total de usuários que frequentaram no mês: <span class="font-bold text-slate-800">{{ $totalAtendidos }}</span></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Sexo e Identidade -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                                    <span class="bg-slate-100 p-1 rounded mr-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </span>
                                    Classificação por Sexo
                                </h4>
                                <table class="w-full border-collapse">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Masculino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['sexo']['M'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Feminino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['sexo']['F'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-600">Outros / Não declarado</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['sexo']['Outros'] }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                                    <span class="bg-slate-100 p-1 rounded mr-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </span>
                                    Identidade de Gênero
                                </h4>
                                <table class="w-full border-collapse">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Cisgênero Feminino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['cis_f'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Cisgênero Masculino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['cis_m'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Transgênero Feminino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['trans_f'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Transgênero Masculino</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['trans_m'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Agênero</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['agenero'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-600">Não declarado</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['identidade']['nao_declarado'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Raça e Dependência -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                                    <span class="bg-slate-100 p-1 rounded mr-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </span>
                                    Raça / Cor
                                </h4>
                                <table class="w-full border-collapse">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Branca</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['branca'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Preta</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['preta'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Parda</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['parda'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Amarela</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['amarela'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Indígena</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['indigena'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-600">Não informado</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['raca_cor']['nao_informado'] }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div>
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center">
                                    <span class="bg-slate-100 p-1 rounded mr-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    Grau de Dependência
                                </h4>
                                <table class="w-full border-collapse">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Grau I (Independente)</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['grau_dependencia']['I'] }}</td>
                                    </tr>
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 text-sm text-slate-600">Grau II (Dependência Leve/Mod.)</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['grau_dependencia']['II'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-600">Grau III (Dependência Grave/Total)</td>
                                        <td class="py-2 text-sm font-bold text-right text-slate-800">{{ $stats['grau_dependencia']['III'] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tempo de Permanência (Saídas) -->
                    <div class="mt-12">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <span class="bg-slate-100 p-1 rounded mr-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            Tempo de Permanência (Evasões/Saídas do Mês)
                        </h4>
                        
                        @if(count($stats['saidas_permanencia']) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50">
                                            <th class="p-3 text-[10px] font-black text-slate-500 uppercase border-b border-slate-200">Usuário</th>
                                            <th class="p-3 text-[10px] font-black text-slate-500 uppercase border-b border-slate-200">Tempo na Instituição</th>
                                            <th class="p-3 text-[10px] font-black text-slate-500 uppercase border-b border-slate-200">Motivo da Saída</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['saidas_permanencia'] as $saida)
                                            <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                                                <td class="p-3 text-sm text-slate-700 font-medium">{{ $saida['nome'] }}</td>
                                                <td class="p-3 text-sm text-slate-600">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                                        {{ $saida['permanencia'] }} ({{ $saida['meses'] }} meses)
                                                    </span>
                                                </td>
                                                <td class="p-3 text-sm text-slate-500 italic">{{ $saida['motivo'] ?? 'Não informado' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-sm text-slate-400">Nenhuma saída registrada neste período.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
