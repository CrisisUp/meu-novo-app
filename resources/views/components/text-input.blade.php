@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-slate-500 focus:ring-slate-500 rounded-xl shadow-sm text-base py-3 px-4']) }}>
