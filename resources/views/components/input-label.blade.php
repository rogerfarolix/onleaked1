@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-zinc-300 mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
