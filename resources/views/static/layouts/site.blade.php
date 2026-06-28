<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Dav/Devs'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500&family=Lora:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-display: 'Syne', sans-serif;
            --font-mono:    'JetBrains Mono', monospace;
            --font-sans:    'Inter', sans-serif;
            --font-prose:   'Lora', serif;

            --page-max:           1280px;
            --page-padding-x:     36px;
            --page-padding-x-md:  20px;
            --page-padding-x-sm:  16px;
            --nav-height:         48px;
            --sidebar-width:      200px;
            --rail-width:         180px;
            --prose-max:          680px;

            --radius-card:    8px;
            --radius-datebox: 7px;
            --radius-tag:     2px;
            --radius-btn:     5px;
            --radius-modal:   10px;

            --duration-fast: 120ms;
            --duration-base: 200ms;
            --easing-default: cubic-bezier(0.16, 1, 0.3, 1);

            --leading-display: 1.05;
            --leading-heading: 1.25;
            --leading-prose:   1.80;
            --leading-body:    1.65;
            --leading-ui:      1.40;
            --leading-mono:    1.50;

            --tracking-display: -0.02em;
            --tracking-mono-sm:  0.06em;
            --tracking-mono-xs:  0.10em;
        }

        [data-theme="dark"] {
            --bg-page:          #0E0E10;
            --bg-surface-1:     #1A1A1E;
            --bg-surface-2:     #242428;
            --bg-surface-3:     #2A2A2F;
            --border-default:   #2A2A30;
            --border-strong:    #3A3A42;
            --border-accent:    rgba(212,167,87,0.30);
            --text-primary:     #F5F0E8;
            --text-body:        #C8C3BC;
            --text-secondary:   #A8A39C;
            --text-muted:       #6A6A72;
            --text-faint:       #3A3A42;
            --accent:           #D4A757;
            --accent-hover:     #E3B040;
            --accent-pressed:   #B88A3A;
            --accent-tint:      rgba(212,167,87,0.10);
            --accent-border:    rgba(212,167,87,0.20);
            --secondary:        #4A9B7F;
            --secondary-tint:   rgba(74,155,127,0.10);
            --secondary-border: rgba(74,155,127,0.20);
            --tertiary:         #C4553A;
            --tertiary-tint:    rgba(196,85,58,0.10);
            --tertiary-border:  rgba(196,85,58,0.20);
            --nav-bg:           rgba(14,14,16,0.95);
            --nav-border:       #2A2A30;
            --nav-link:         #6A6A72;
            --nav-link-active:  #D4A757;
            --tab-bg:           #0A0A0C;
            --tab-icon:         #3A3A42;
            --tab-icon-active:  #D4A757;
            --tab-label:        #3A3A42;
            --tab-label-active: #D4A757;
            --progress-bg:      #1A1A1E;
            --progress-fill:    #D4A757;
            --datebox-bg:       #1A1A1E;
            --datebox-border:   #2A2A30;
            --datebox-day:      #F5F0E8;
            --datebox-month:    #D4A757;
            --divider-line:     #2A2A30;
            --divider-label:    #3A3A42;
            --like-bg:          #1A1A1E;
            --like-icon:        #6A6A72;
            --like-icon-liked:  #C4553A;
            --like-count:       #3A3A42;
            --footer-bg:        #0E0E10;
            --footer-border:    #2A2A30;
            --footer-text:      #3A3A42;
            --lh-bg:            #1A1A1E;
            --lh-num:           #D4A757;
            --lh-label:         #3A3A42;
        }

        [data-theme="light"] {
            --bg-page:          #FAF7F2;
            --bg-surface-1:     #F5F1EB;
            --bg-surface-2:     #EDE9E2;
            --bg-surface-3:     #E4E0D9;
            --border-default:   #D8D4CC;
            --border-strong:    #C8C3BB;
            --border-accent:    rgba(139,111,71,0.30);
            --text-primary:     #1A1916;
            --text-body:        #2A2820;
            --text-secondary:   #5A5850;
            --text-muted:       #88837C;
            --text-faint:       #C8C3BB;
            --accent:           #8B6F47;
            --accent-hover:     #7A5E38;
            --accent-pressed:   #634A27;
            --accent-tint:      rgba(139,111,71,0.10);
            --accent-border:    rgba(139,111,71,0.20);
            --secondary:        #2B7A5E;
            --secondary-tint:   rgba(43,122,94,0.10);
            --secondary-border: rgba(43,122,94,0.20);
            --tertiary:         #C4553A;
            --tertiary-tint:    rgba(196,85,58,0.10);
            --tertiary-border:  rgba(196,85,58,0.20);
            --nav-bg:           rgba(250,247,242,0.95);
            --nav-border:       #D8D4CC;
            --nav-link:         #88837C;
            --nav-link-active:  #8B6F47;
            --tab-bg:           #EDE9E2;
            --tab-icon:         #B0ACA6;
            --tab-icon-active:  #8B6F47;
            --tab-label:        #B0ACA6;
            --tab-label-active: #8B6F47;
            --progress-bg:      #E4E0D9;
            --progress-fill:    #8B6F47;
            --datebox-bg:       #EDE9E2;
            --datebox-border:   #D8D4CC;
            --datebox-day:      #1A1916;
            --datebox-month:    #8B6F47;
            --divider-line:     #D8D4CC;
            --divider-label:    #C8C3BB;
            --like-bg:          #EDE9E2;
            --like-icon:        #88837C;
            --like-icon-liked:  #C4553A;
            --like-count:       #88837C;
            --footer-bg:        #F5F1EB;
            --footer-border:    #D8D4CC;
            --footer-text:      #B0ACA6;
            --lh-bg:            #EDE9E2;
            --lh-num:           #8B6F47;
            --lh-label:         #B0ACA6;
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
        }

        a { color: inherit; text-decoration: none; }
    </style>
    @stack('head')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
