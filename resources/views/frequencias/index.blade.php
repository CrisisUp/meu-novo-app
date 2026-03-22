<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Início</a>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Frequência Diária</span>
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Frequência Diária') }}
            </h2>
            <div class="flex items-center space-x-4">
                <form action="{{ route('frequencia.index') }}" method="GET" class="flex items-center space-x-2">
                    <x-text-input type="date" name="data" value="{{ $data }}" class="!py-1.5" onchange="this.form.submit()" />
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('frequencia.store') }}" method="POST">
                @csrf
                <input type="hidden" name="data" value="{{ $data }}">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                    <div class="p-6 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">
                            Lista de Presença: {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                        </span>
                        <div class="text-xs text-slate-400 font-medium italic">
                            Marque os idosos presentes e clique em salvar.
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="bg-white border-b border-slate-100 text-xs text-slate-400 uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-4 w-20 text-center">Presente</th>
                                    <th class="px-6 py-4">Idoso</th>
                                    <th class="px-6 py-4">Observações / Intercorrências</th>
                                    <th class="px-6 py-4 w-40">Status Atual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($idosos as $idoso)
                                    @php
                                        $freq = $frequencias->get($idoso->id);
                                        $isPresente = $freq && $freq->status == 'presente';
                                        $obs = $freq ? $freq->observacoes : '';
                                    @endphp
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" name="presencas[{{ $idoso->id }}]" value="1" 
                                                {{ $isPresente ? 'checked' : '' }}
                                                class="w-6 h-6 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                        </td>
                                        <td class="px-6 py-4 text-lg font-black text-slate-900">
                                            {{ $idoso->nome }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" name="observacoes[{{ $idoso->id }}]" value="{{ $obs }}" 
                                                placeholder="Anote aqui intercorrências..."
                                                class="w-full border-slate-200 focus:border-slate-400 focus:ring-0 rounded-xl text-base py-3 px-4 text-slate-700 bg-transparent focus:bg-white transition-all shadow-sm">
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($freq)
                                                @if($freq->status == 'presente')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                        Presente
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-400">
                                                        Ausente
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-slate-300 italic">Aguardando registro</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                                            Nenhum idoso cadastrado no sistema.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($idosos->isNotEmpty())
                        <div class="p-6 bg-slate-50 border-t border-slate-200">
                            <x-primary-button class="bg-emerald-700 hover:bg-emerald-800 px-8 py-3">
                                Salvar Lista de Presença
                            </x-primary-button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
