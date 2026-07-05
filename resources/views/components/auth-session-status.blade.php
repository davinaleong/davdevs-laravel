@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['style' => 'font-weight:500;font-size:12px;color:var(--cms-success);']) }}>
        {{ $status }}
    </div>
@endif
