@props(['value'])

<label {{ $attributes->merge(['style' => 'display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);']) }}>
    {{ $value ?? $slot }}
</label>
