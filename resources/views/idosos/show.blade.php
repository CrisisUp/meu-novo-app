<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Início</a>
        <span class="mx-2">/</span>
        <a href="{{ route('idoso.index') }}" class="hover:text-slate-600">Idosos</a>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Prontuário</span>
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Prontuário: ') }} {{ $idoso->nome }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('encaminhamento.create', ['idoso_id' => $idoso->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Encaminhar Idoso
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-200 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-200 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
                <a href="{{ route('idoso.relatorio-preview', $idoso) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Visualizar Relatório Mensal
                </a>
                <a href="{{ route('idoso.edit', $idoso) }}" class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    Editar Cadastro
                </a>
                <a href="{{ route('idoso.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-50 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Cabeçalho Exclusivo para Impressão -->
            <div class="hidden print:block text-center border-b-2 border-slate-800 pb-6 mb-8">
                <h1 class="text-2xl font-bold text-slate-900 uppercase">Gestão CDI - Centro de Dia para Idosos</h1>
                <p class="text-sm font-bold text-slate-500">Prontuário Individual do Beneficiário</p>
                <p class="text-xs text-slate-400 mt-1">Impresso em {{ date('d/m/Y H:i') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Coluna Lateral: Dados Pessoais -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <div class="flex flex-col items-center text-center mb-6">
                            <div class="h-32 w-32 rounded-full overflow-hidden border-4 border-white shadow-md mb-4 bg-slate-100 flex items-center justify-center text-slate-400">
                                @if($idoso->foto)
                                    <img src="{{ asset('storage/' . $idoso->foto) }}" class="h-full w-full object-cover" alt="{{ $idoso->nome }}">
                                @else
                                    <span class="text-4xl font-bold">{{ strtoupper(substr($idoso->nome, 0, 1)) }}</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">{{ $idoso->nome }}</h3>
                            <p class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 mt-2">{{ $idoso->codigo_registro }}</p>
                            <p class="text-sm text-slate-400 font-medium mt-2">{{ \Carbon\Carbon::parse($idoso->data_nascimento)->age }} anos</p>
                        </div>
                        
                        <div class="space-y-4 border-t border-slate-50 pt-6">
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Data de Nasc.</span>
                                <span class="text-sm text-slate-700 font-medium">{{ \Carbon\Carbon::parse($idoso->data_nascimento)->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">CPF</span>
                                <span class="text-sm text-slate-700 font-medium">{{ $idoso->cpf ?? 'Não informado' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">NIS</span>
                                <span class="text-sm text-slate-700 font-medium">{{ $idoso->nis ?? 'Não informado' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-emerald-700 p-6 rounded-xl shadow-sm text-white">
                        <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-80">Contato de Emergência</h4>
                        <div class="space-y-1">
                            <p class="text-lg font-bold">{{ $idoso->contato_emergencia_nome }}</p>
                            <p class="text-emerald-100 font-medium">{{ $idoso->contato_emergencia_telefone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Principal: Saúde e Obs -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ficha de Saúde
                        </h3>

                        <div class="space-y-8">
                            <section>
                                <h4 class="text-xs font-bold text-slate-800 uppercase mb-2">Alergias</h4>
                                <div class="p-4 bg-red-50 rounded-lg text-red-800 text-sm border border-red-100 min-h-[60px]">
                                    {{ $idoso->alergias ?? 'Nenhuma alergia registrada.' }}
                                </div>
                            </section>

                            <section>
                                <h4 class="text-xs font-bold text-slate-800 uppercase mb-2">Medicamentos em Uso</h4>
                                <div class="p-4 bg-slate-50 rounded-lg text-slate-700 text-sm border border-slate-100 min-h-[60px]">
                                    {{ $idoso->medicamentos ?? 'Nenhum medicamento registrado.' }}
                                </div>
                            </section>

                            <section>
                                <h4 class="text-xs font-bold text-slate-800 uppercase mb-2">Observações Adicionais</h4>
                                <div class="p-4 bg-slate-50 rounded-lg text-slate-600 text-sm border border-slate-100 min-h-[100px] italic leading-relaxed">
                                    {{ $idoso->observacoes ?? 'Sem observações adicionais.' }}
                                </div>
                            </section>
                        </div>
                    </div>

                    <!-- Timeline de Intercorrências -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Histórico de Intercorrências
                        </h3>

                        <div class="space-y-6">
                            @forelse($idoso->frequencias()->whereNotNull('observacoes')->orderByDesc('data')->limit(10)->get() as $intercorrencia)
                                <div class="relative pl-6 border-l-2 border-slate-100">
                                    <div class="absolute -left-[9px] top-0 h-4 w-4 rounded-full bg-slate-200 border-2 border-white"></div>
                                    <div class="mb-1 text-xs font-bold text-slate-400 uppercase flex justify-between">
                                        <span>{{ \Carbon\Carbon::parse($intercorrencia->data)->format('d/m/Y') }}</span>
                                        @if($intercorrencia->profissional)
                                            <span class="text-[10px] text-slate-300">Anotado por: {{ $intercorrencia->profissional->name }}</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                        {{ $intercorrencia->observacoes }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-sm italic">
                                    Nenhuma intercorrência registrada recentemente.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Histórico de Encaminhamentos -->
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Histórico de Encaminhamentos (Saídas)
                        </h3>

                        <div class="space-y-4">
                            @forelse($idoso->encaminhamentos()->orderByDesc('data_encaminhamento')->get() as $enc)
                                <div class="p-4 rounded-lg border {{ $enc->prioridade == 'urgente' ? 'bg-red-50 border-red-100' : 'bg-slate-50 border-slate-100' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $enc->prioridade == 'urgente' ? 'bg-red-200 text-red-800' : 'bg-slate-200 text-slate-600' }}">
                                                {{ $enc->prioridade }}
                                            </span>
                                            <h4 class="text-sm font-bold text-slate-800 mt-1">{{ $enc->instituicao_destino }}</h4>
                                        </div>
                                        <span class="text-xs font-mono text-slate-400">{{ \Carbon\Carbon::parse($enc->data_encaminhamento)->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $enc->motivo }}</p>
                                    <div class="mt-2 text-[10px] text-slate-400 italic">
                                        Registrado por: {{ $enc->profissional->name ?? 'Sistema' }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-sm italic">
                                    Nenhum encaminhamento registrado.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <form action="{{ route('idoso.destroy', $idoso) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir permanentemente este cadastro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-bold uppercase tracking-widest transition-colors">
                                Excluir Prontuário
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
