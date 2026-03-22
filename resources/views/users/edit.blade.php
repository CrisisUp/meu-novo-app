<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Usuário') }}
            </h2>
            <a href="{{ route('user.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                Listar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-alert />

                    <form action="{{ route('user.update', ['user' => $user->id]) }}" method="POST" class="space-y-6 max-w-xl">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nome')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus placeholder="Nome completo do usuário" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required placeholder="Melhor e-mail do usuário" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <h3 class="text-sm font-medium text-gray-900 mb-4 uppercase tracking-wider">Alterar Senha (Opcional)</h3>
                            
                            <div class="mb-4">
                                <x-input-label for="password" :value="__('Nova Senha')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Deixe em branco para manter a atual" />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Nova Senha')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" placeholder="Repita a nova senha" />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button class="bg-green-600 hover:bg-green-700 active:bg-green-900 focus:ring-green-300">
                                {{ __('Salvar Alterações') }}
                            </x-primary-button>
                            <a href="{{ route('user.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
