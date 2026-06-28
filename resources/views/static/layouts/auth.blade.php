<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Dav/Devs'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-display: 'Syne', sans-serif;
            --font-mono:    'JetBrains Mono', monospace;
            --font-sans:    'Inter', sans-serif;

            --radius-tag: 2px;
            --radius-btn: 5px;
            --radius-card: 8px;
            --radius-input: 5px;

            --duration-fast: 120ms;
            --duration-base: 200ms;
            --easing-default: cubic-bezier(0.16, 1, 0.3, 1);

            --tracking-display: -0.02em;
            --tracking-mono-sm:  0.06em;
            --tracking-mono-xs:  0.10em;

            --leading-display: 1.05;
            --leading-body:    1.65;
            --leading-mono:    1.50;
        }

        [data-theme="dark"] {
            --bg-page:         #0E0E10;
            --bg-surface-1:    #1A1A1E;
            --bg-surface-2:    #242428;
            --border-default:  #2A2A30;
            --border-strong:   #3A3A42;
            --text-primary:    #F5F0E8;
            --text-secondary:  #A8A39C;
            --text-muted:      #6A6A72;
            --text-faint:      #3A3A42;
            --accent:          #D4A757;
            --accent-hover:    #E3B040;
            --accent-tint:     rgba(212,167,87,0.10);
            --accent-border:   rgba(212,167,87,0.20);
            --tertiary:        #C4553A;
            --tertiary-tint:   rgba(196,85,58,0.10);
            --tertiary-border: rgba(196,85,58,0.20);
            --secondary:       #4A9B7F;
            --secondary-tint:  rgba(74,155,127,0.10);
            --secondary-border:rgba(74,155,127,0.20);
            --input-bg:        #0E0E10;
            --input-border:    #2A2A30;
            --input-border-focus: #D4A757;
            --input-placeholder: #3A3A42;
        }

        [data-theme="light"] {
            --bg-page:         #FAF7F2;
            --bg-surface-1:    #F5F1EB;
            --bg-surface-2:    #EDE9E2;
            --border-default:  #D8D4CC;
            --border-strong:   #C8C3BB;
            --text-primary:    #1A1916;
            --text-secondary:  #5A5850;
            --text-muted:      #88837C;
            --text-faint:      #C8C3BB;
            --accent:          #8B6F47;
            --accent-hover:    #7A5E38;
            --accent-tint:     rgba(139,111,71,0.10);
            --accent-border:   rgba(139,111,71,0.20);
            --tertiary:        #C4553A;
            --tertiary-tint:   rgba(196,85,58,0.10);
            --tertiary-border: rgba(196,85,58,0.20);
            --secondary:       #2B7A5E;
            --secondary-tint:  rgba(43,122,94,0.10);
            --secondary-border:rgba(43,122,94,0.20);
            --input-bg:        #FFFFFF;
            --input-border:    #D8D4CC;
            --input-border-focus: #8B6F47;
            --input-placeholder: #C8C3BB;
        }

        html { scroll-behavior: smooth; }

        body {
            background-color: var(--bg-page);
            color: var(--text-primary);
            font-family: var(--font-sans);
            font-size: 13px;
            line-height: var(--leading-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        a { color: inherit; text-decoration: none; }

        /* ── SHARED AUTH COMPONENTS ───────────────────────────────── */
        .auth-wordmark {
            font-family: var(--font-mono); font-size: 13px;
            color: var(--text-muted); text-align: center;
            margin-bottom: 28px; display: block;
        }
        .auth-wordmark span { color: var(--accent); }

        .auth-card {
            width: 100%; max-width: 360px;
            background: var(--bg-surface-1);
            border: 0.5px solid var(--border-default);
            border-radius: var(--radius-card);
            padding: 28px 28px 24px;
        }

        .auth-card-title {
            font-family: var(--font-display); font-weight: 800; font-size: 20px;
            color: var(--text-primary); line-height: var(--leading-display);
            letter-spacing: var(--tracking-display); margin-bottom: 4px;
        }
        .auth-card-sub {
            font-family: var(--font-sans); font-size: 12px;
            color: var(--text-muted); margin-bottom: 24px;
        }

        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .form-label {
            font-family: var(--font-mono); font-size: 9px;
            color: var(--text-muted); letter-spacing: var(--tracking-mono-sm);
            text-transform: uppercase;
        }
        .form-input {
            background: var(--input-bg);
            border: 0.5px solid var(--input-border);
            border-radius: var(--radius-input);
            color: var(--text-primary);
            font-family: var(--font-sans); font-size: 13px;
            padding: 9px 12px; width: 100%;
            outline: none;
            transition: border-color var(--duration-fast);
        }
        .form-input::placeholder { color: var(--input-placeholder); }
        .form-input:focus { border-color: var(--input-border-focus); }
        .form-input.error { border-color: var(--tertiary); }

        .form-hint {
            font-family: var(--font-sans); font-size: 11px;
            color: var(--text-muted);
        }
        .form-error {
            font-family: var(--font-sans); font-size: 11px;
            color: var(--tertiary);
        }

        .btn-primary {
            width: 100%; padding: 10px;
            font-family: var(--font-mono); font-size: 11px;
            color: var(--bg-page); background: var(--accent);
            border: none; border-radius: var(--radius-btn);
            cursor: pointer; letter-spacing: var(--tracking-mono-sm);
            transition: background var(--duration-fast);
            margin-top: 8px;
        }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-primary:disabled { opacity: 0.45; cursor: not-allowed; }

        .btn-ghost {
            width: 100%; padding: 9px;
            font-family: var(--font-mono); font-size: 11px;
            color: var(--text-secondary);
            background: none; border: 0.5px solid var(--border-default);
            border-radius: var(--radius-btn); cursor: pointer;
            letter-spacing: var(--tracking-mono-sm);
            transition: border-color var(--duration-fast), color var(--duration-fast);
            margin-top: 6px;
        }
        .btn-ghost:hover { border-color: var(--border-strong); color: var(--text-primary); }

        .auth-divider {
            display: flex; align-items: center; gap: 10px; margin: 16px 0;
        }
        .auth-divider-line { flex: 1; height: 0.5px; background: var(--border-default); }
        .auth-divider-label {
            font-family: var(--font-mono); font-size: 9px;
            color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
        }

        .auth-footer {
            text-align: center; margin-top: 16px;
            font-family: var(--font-sans); font-size: 11px;
            color: var(--text-muted);
        }
        .auth-footer a {
            color: var(--accent);
            transition: color var(--duration-fast);
        }
        .auth-footer a:hover { color: var(--accent-hover); }

        .auth-notice {
            background: var(--accent-tint);
            border: 0.5px solid var(--accent-border);
            border-radius: var(--radius-btn);
            padding: 10px 12px; margin-bottom: 20px;
            font-family: var(--font-sans); font-size: 11px;
            color: var(--text-secondary);
        }
        .auth-notice-error {
            background: var(--tertiary-tint);
            border-color: var(--tertiary-border);
        }
        .auth-notice-success {
            background: var(--secondary-tint);
            border-color: var(--secondary-border);
        }

        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            font-family: var(--font-mono); font-size: 9px;
            color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
        }
        .password-toggle:hover { color: var(--accent); }

        .strength-bar {
            height: 2px; border-radius: 1px;
            background: var(--border-default); margin-top: 6px;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%; border-radius: 1px;
            transition: width var(--duration-base), background var(--duration-base);
        }
        .strength-fill.weak   { width: 25%; background: var(--tertiary); }
        .strength-fill.fair   { width: 50%; background: #D4A757; }
        .strength-fill.good   { width: 75%; background: var(--secondary); }
        .strength-fill.strong { width: 100%; background: var(--secondary); }

        .strength-label {
            font-family: var(--font-mono); font-size: 8px;
            letter-spacing: var(--tracking-mono-xs); margin-top: 3px;
        }
        .strength-label.weak   { color: var(--tertiary); }
        .strength-label.fair   { color: var(--accent); }
        .strength-label.good   { color: var(--secondary); }
        .strength-label.strong { color: var(--secondary); }

        /* OTP inputs */
        .otp-group {
            display: flex; gap: 8px; justify-content: center; margin: 20px 0;
        }
        .otp-input {
            width: 44px; height: 52px;
            background: var(--input-bg);
            border: 0.5px solid var(--input-border);
            border-radius: var(--radius-input);
            color: var(--text-primary);
            font-family: var(--font-display); font-weight: 700; font-size: 20px;
            text-align: center; outline: none;
            transition: border-color var(--duration-fast);
        }
        .otp-input:focus { border-color: var(--input-border-focus); }
        .otp-input.filled { border-color: var(--accent); }

        /* QR code placeholder */
        .qr-wrapper {
            display: flex; flex-direction: column; align-items: center; gap: 12px;
            margin: 20px 0;
        }
        .qr-box {
            width: 160px; height: 160px;
            background: #FFFFFF;
            border-radius: 6px; padding: 10px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-template-rows: repeat(7, 1fr);
            gap: 2px;
        }
        .qr-cell {
            border-radius: 1px;
            background: #0E0E10;
        }
        .qr-cell.w { background: #FFFFFF; }
        .qr-label {
            font-family: var(--font-mono); font-size: 9px;
            color: var(--text-muted); letter-spacing: var(--tracking-mono-xs);
            text-align: center;
        }

        /* Recovery code grid */
        .recovery-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 6px; margin: 16px 0;
        }
        .recovery-code {
            background: var(--bg-surface-2);
            border: 0.5px solid var(--border-default);
            border-radius: var(--radius-btn);
            padding: 7px 10px;
            font-family: var(--font-mono); font-size: 10px;
            color: var(--text-secondary); letter-spacing: 0.04em;
            text-align: center;
        }

        /* Step indicator */
        .step-indicator {
            display: flex; align-items: center; gap: 0; margin-bottom: 24px;
        }
        .step {
            display: flex; flex-direction: column; align-items: center; gap: 4px; flex: 1;
        }
        .step-dot {
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-mono); font-size: 8px;
            color: var(--text-faint);
            background: var(--bg-surface-2);
            border: 0.5px solid var(--border-default);
            position: relative; z-index: 1;
        }
        .step-dot.done   { background: var(--accent); border-color: var(--accent); color: var(--bg-page); }
        .step-dot.active { background: var(--accent-tint); border-color: var(--accent); color: var(--accent); }
        .step-label {
            font-family: var(--font-mono); font-size: 8px;
            color: var(--text-faint); letter-spacing: var(--tracking-mono-xs);
            text-align: center;
        }
        .step-label.active { color: var(--accent); }
        .step-line { flex: 1; height: 0.5px; background: var(--border-default); margin-bottom: 12px; }
        .step-line.done { background: var(--accent); }

        .page-sub-bg {
            position: fixed; inset: 0; z-index: -1;
            background: var(--bg-page);
        }
        /* subtle dot grid background */
        .page-sub-bg::after {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(212,167,87,0.04) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="page-sub-bg"></div>
    @yield('content')
    @stack('scripts')
</body>
</html>
