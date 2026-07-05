@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['style' => 'width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);']) }}>
