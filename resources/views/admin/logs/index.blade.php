<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Logs de Auditoria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Data/Hora</th>
                                <th class="px-6 py-3">Usuário</th>
                                <th class="px-6 py-3">Ação</th>
                                <th class="px-6 py-3">Modelo</th>
                                <th class="px-6 py-3">Alterações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="px-6 py-4">{{ $log->user->name ?? 'Sistema' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded text-xs font-bold 
                                            {{ $log->action == 'created' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $log->action == 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->action == 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ str_replace('App\Models\\', '', $log->model_type) }} (#{{ $log->model_id }})
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->action == 'updated')
                                            <details class="cursor-pointer">
                                                <summary class="text-blue-600 hover:underline">Ver Detalhes</summary>
                                                <div class="mt-2 p-2 bg-gray-50 rounded text-[10px]">
                                                    <strong>DE:</strong> {{ json_encode($log->old_values) }}<br>
                                                    <strong>PARA:</strong> {{ json_encode($log->new_values) }}
                                                </div>
                                            </details>
                                        @elseif($log->action == 'created')
                                            <span class="text-gray-400 italic text-xs">Novo registro criado</span>
                                        @else
                                            <span class="text-red-400 italic text-xs">Registro excluído</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
