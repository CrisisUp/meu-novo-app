<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 selection:bg-emerald-100 dark:bg-slate-900 dark:text-slate-100" x-data="{ highContrast: localStorage.getItem('high-contrast') === 'true' }" :class="{ 'high-contrast': highContrast }">
        <!-- Skip Link -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-emerald-600 focus:text-white focus:font-bold focus:rounded-b-lg focus:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
            Pular para o conteúdo principal
        </a>

        <div class="min-h-screen bg-gray-100 dark:bg-slate-950 transition-colors duration-200">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm border-b border-slate-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @isset($breadcrumbs)
                            <nav class="flex mb-4 text-slate-400 text-xs font-bold uppercase tracking-widest" aria-label="Breadcrumb">
                                {{ $breadcrumbs }}
                            </nav>
                        @endisset
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="main-content" tabindex="-1" class="focus:outline-none">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
