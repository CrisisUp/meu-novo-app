@props(['disabled' => false, 'isError' => false])

<input @disabled($disabled) 
    aria-invalid="{{ $isError ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => 'border-slate-200 focus:border-slate-500 focus:ring-slate-500 rounded-xl shadow-sm text-base py-3 px-4 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200' . ($isError ? ' border-rose-500 ring-rose-500' : '')]) }}>
