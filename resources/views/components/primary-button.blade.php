<button {{ $attributes->merge(['type' => 'submit', 'style' => 'background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:8px 16px;font-size:13px;font-weight:500;font-family:Inter,sans-serif;cursor:pointer;']) }}>
    {{ $slot }}
</button>
