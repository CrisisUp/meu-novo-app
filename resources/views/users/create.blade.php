<x-app-layout>
    <x-slot name="breadcrumbs">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Início</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.index') }}" class="hover:text-slate-600">Equipe</a>
        <span class="mx-2">/</span>
        <span class="text-slate-600">Novo Membro</span>
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Cadastrar Profissional') }}
            </h2>
            <a href="{{ route('user.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-900 transition ease-in-out duration-150">
                Listar Equipe
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-8">
                    <x-alert />

                    <form action="{{ route('user.store') }}" method="POST" class="space-y-6 max-w-xl">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nome Completo')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" :isError="$errors->has('name')" required autofocus placeholder="Nome do profissional" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('E-mail Corporativo')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" :isError="$errors->has('email')" required placeholder="email@exemplo.com" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Cargo/Perfil de Acesso')" />
                            <x-select-input id="role" name="role" class="mt-1 block w-full" :isError="$errors->has('role')" required>
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Selecione o perfil</option>
                                <option value="funcionario" {{ old('role') == 'funcionario' ? 'selected' : '' }}>Funcionário (Leitura/Escrita)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador (Acesso Total)</option>
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="password" :value="__('Senha de Acesso')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :isError="$errors->has('password')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :isError="$errors->has('password_confirmation')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>

                        <div class="pt-4">
                            <x-primary-button class="bg-emerald-700 hover:bg-emerald-800">
                                {{ __('Salvar Cadastro') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
