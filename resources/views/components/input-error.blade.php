@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['style' => 'font-size:12px;color:var(--cms-error);list-style:none;padding:0;margin:6px 0 0;display:flex;flex-direction:column;gap:2px;']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
