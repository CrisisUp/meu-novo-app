@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-base text-slate-700']) }}>
    {{ $value ?? $slot }}
</label>
