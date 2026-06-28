# Dav/Devs — Design Language: Frontend (Public Site)

---

## Colour Tokens

### Named palette

```css
--color-obsidian:  #0E0E10;
--color-parchment: #F5F0E8;
--color-gilded:    #D4A757;
--color-graphite:  #2A2A2F;
--color-sage:      #4A9B7F;
--color-ember:     #C4553A;
```

---

### Dark mode (default)

```css
[data-theme="dark"] {
  /* Backgrounds */
  --bg-page:         #0E0E10;  /* obsidian       — page canvas                  */
  --bg-surface-1:    #1A1A1E;  /* obsidian +     — card, sidebar, nav hover      */
  --bg-surface-2:    #242428;  /* graphite –     — code blocks, stat cards       */
  --bg-surface-3:    #2A2A2F;  /* graphite       — modals, overlays              */

  /* Borders */
  --border-default:  #2A2A30;  /* graphite       — default card/section edges    */
  --border-strong:   #3A3A42;  /* graphite +     — hover borders, dividers       */
  --border-accent:   rgba(212, 167, 87, 0.30);  /* gilded tint                  */

  /* Text */
  --text-primary:    #F5F0E8;  /* parchment      — headings, titles              */
  --text-body:       #C8C3BC;  /* parchment –    — article prose, descriptions   */
  --text-secondary:  #A8A39C;  /* parchment ––   — excerpts, bios                */
  --text-muted:      #6A6A72;  /* mid-grey       — meta, dates, labels           */
  --text-faint:      #3A3A42;  /* graphite +     — year dividers, dots           */

  /* Accent — gilded */
  --accent:          #D4A757;  /* gilded         — slash, CTAs, active nav, tags */
  --accent-hover:    #E3B040;  /* gilded +       — hover on accent               */
  --accent-pressed:  #B88A3A;  /* gilded –       — pressed/active                */
  --accent-tint:     rgba(212, 167, 87, 0.10);
  --accent-border:   rgba(212, 167, 87, 0.20);

  /* Secondary — sage */
  --secondary:       #4A9B7F;  /* sage           — tool tags, teal badges        */
  --secondary-tint:  rgba(74, 155, 127, 0.10);
  --secondary-border:rgba(74, 155, 127, 0.20);

  /* Tertiary — ember */
  --tertiary:        #C4553A;  /* ember          — faith tags, e-book badges     */
  --tertiary-tint:   rgba(196, 85, 58, 0.10);
  --tertiary-border: rgba(196, 85, 58, 0.20);

  /* Nav */
  --nav-bg:          rgba(14, 14, 16, 0.95);
  --nav-border:      #2A2A30;
  --nav-link:        #6A6A72;
  --nav-link-active: #D4A757;

  /* Bottom tab nav (mobile) */
  --tab-bg:          #0A0A0C;
  --tab-icon:        #3A3A42;
  --tab-icon-active: #D4A757;
  --tab-label:       #3A3A42;
  --tab-label-active:#D4A757;

  /* Reading progress bar */
  --progress-bg:     #1A1A1E;
  --progress-fill:   #D4A757;

  /* Date box (listing) */
  --datebox-bg:      #1A1A1E;
  --datebox-border:  #2A2A30;
  --datebox-day:     #F5F0E8;
  --datebox-month:   #D4A757;

  /* Year divider (listing) */
  --divider-line:    #2A2A30;
  --divider-label:   #3A3A42;

  /* Like button */
  --like-bg:         #1A1A1E;
  --like-icon:       #6A6A72;
  --like-icon-liked: #C4553A;
  --like-count:      #3A3A42;

  /* Search overlay */
  --search-overlay:  rgba(0, 0, 0, 0.75);
  --search-bg:       #1A1A1E;
  --search-match:    #D4A757;
  --search-border:   #2A2A30;

  /* Joke page */
  --joke-card-bg:    #1A1A1E;
  --joke-ring-track: #242428;
  --joke-ring-fill:  #D4A757;
  --joke-timer-num:  #D4A757;
  --joke-answer-bg:  #242428;

  /* Lighthouse badges (footer) */
  --lh-bg:           #1A1A1E;
  --lh-num:          #D4A757;
  --lh-label:        #3A3A42;

  /* Footer */
  --footer-bg:       #0E0E10;
  --footer-border:   #2A2A30;
  --footer-text:     #3A3A42;
}
```

---

### Light mode

```css
[data-theme="light"] {
  /* Backgrounds */
  --bg-page:         #FAF7F2;  /* cream-50       — page canvas                  */
  --bg-surface-1:    #F5F1EB;  /* cream-100      — card hover, sidebar           */
  --bg-surface-2:    #EDE9E2;  /* cream-200      — raised surfaces               */
  --bg-surface-3:    #E4E0D9;  /* cream-300      — modals                        */

  /* Borders */
  --border-default:  #D8D4CC;  /* cream-400      — default edges                 */
  --border-strong:   #C8C3BB;  /* cream-500      — hover borders                 */
  --border-accent:   rgba(139, 111, 71, 0.30);

  /* Text */
  --text-primary:    #1A1916;  /* cream-950      — headings, titles              */
  --text-body:       #2A2820;  /* cream-900      — article prose                 */
  --text-secondary:  #5A5850;  /* cream-800      — excerpts, bios                */
  --text-muted:      #88837C;  /* cream-700      — meta, dates                   */
  --text-faint:      #C8C3BB;  /* cream-500      — dividers, dots                */

  /* Accent — gilded (shifted warmer for light mode) */
  --accent:          #8B6F47;  /* gilded dark    — links, active nav             */
  --accent-hover:    #7A5E38;
  --accent-pressed:  #634A27;
  --accent-tint:     rgba(139, 111, 71, 0.10);
  --accent-border:   rgba(139, 111, 71, 0.20);

  /* Secondary — sage (slightly darker for contrast on light) */
  --secondary:       #2B7A5E;
  --secondary-tint:  rgba(43, 122, 94, 0.10);
  --secondary-border:rgba(43, 122, 94, 0.20);

  /* Tertiary — ember (unchanged, holds contrast on light) */
  --tertiary:        #C4553A;
  --tertiary-tint:   rgba(196, 85, 58, 0.10);
  --tertiary-border: rgba(196, 85, 58, 0.20);

  /* Nav */
  --nav-bg:          rgba(250, 247, 242, 0.95);
  --nav-border:      #D8D4CC;
  --nav-link:        #88837C;
  --nav-link-active: #8B6F47;

  /* Bottom tab nav (mobile) */
  --tab-bg:          #EDE9E2;
  --tab-icon:        #B0ACA6;
  --tab-icon-active: #8B6F47;
  --tab-label:       #B0ACA6;
  --tab-label-active:#8B6F47;

  /* Reading progress bar */
  --progress-bg:     #E4E0D9;
  --progress-fill:   #8B6F47;

  /* Date box */
  --datebox-bg:      #EDE9E2;
  --datebox-border:  #D8D4CC;
  --datebox-day:     #1A1916;
  --datebox-month:   #8B6F47;

  /* Year divider */
  --divider-line:    #D8D4CC;
  --divider-label:   #C8C3BB;

  /* Like button */
  --like-bg:         #EDE9E2;
  --like-icon:       #88837C;
  --like-icon-liked: #C4553A;
  --like-count:      #88837C;

  /* Search overlay */
  --search-overlay:  rgba(26, 25, 22, 0.60);
  --search-bg:       #FAF7F2;
  --search-match:    #8B6F47;
  --search-border:   #D8D4CC;

  /* Joke page */
  --joke-card-bg:    #F5F1EB;
  --joke-ring-track: #E4E0D9;
  --joke-ring-fill:  #8B6F47;
  --joke-timer-num:  #8B6F47;
  --joke-answer-bg:  #EDE9E2;

  /* Lighthouse badges */
  --lh-bg:           #EDE9E2;
  --lh-num:          #8B6F47;
  --lh-label:        #B0ACA6;

  /* Footer */
  --footer-bg:       #F5F1EB;
  --footer-border:   #D8D4CC;
  --footer-text:     #B0ACA6;
}
```

---

## Typography

### Families

```css
--font-display: 'Syne', sans-serif;
--font-mono:    'JetBrains Mono', monospace;
--font-sans:    'Inter', sans-serif;
--font-prose:   'Lora', serif;
```

```css
/* Google Fonts import */
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500&family=Lora:ital,wght@0,400;1,400&display=swap');
```

### Usage rules

| Family | Where | Never |
|---|---|---|
| Syne 700/800 | Wordmark, page titles, post titles, card titles, date-box day, stat numbers | Below 12px; body copy; tags |
| JetBrains Mono 400/500 | Nav links, section labels, tags, badges, dates, meta, code, breadcrumbs, year dividers | Long-form reading; anything over 14px |
| Inter 400/500 | Buttons, excerpts, bios, UI labels, sidebar items, search results | Display/hero contexts |
| Lora 400/400i | Article/post body text only — the reading column | UI chrome; cards; navigation; anywhere outside the post content well |

### Type scale

```css
/* Display */
--text-display-xl:  clamp(32px, 5vw, 48px);  /* Hero wordmark                  */
--text-display-lg:  clamp(24px, 4vw, 32px);  /* Listing page title             */
--text-display-md:  26px;                     /* Post detail title (desktop)    */
--text-display-sm:  20px;                     /* Post detail title (mobile)     */

/* Headings (Syne) */
--text-heading-lg:  18px;   /* Post body section headings    */
--text-heading-md:  16px;   /* Featured card titles          */
--text-heading-sm:  14px;   /* Standard card titles          */
--text-heading-xs:  13px;   /* Small card titles, date-box day (mobile) */

/* Prose (Lora — reading column only) */
--text-prose-lg:    14px;   /* Article body (desktop)        */
--text-prose-md:    13px;   /* Article body (mobile)         */

/* Body (Inter) */
--text-body-lg:     13px;   /* Bios, hero description        */
--text-body-md:     12px;   /* Excerpts, card descriptions   */
--text-body-sm:     11px;   /* Secondary descriptions        */

/* UI (Inter) */
--text-ui-md:       11px;   /* Buttons, sidebar items        */
--text-ui-sm:       10px;   /* Small labels                  */

/* Mono (JetBrains Mono) */
--text-mono-lg:     11px;   /* Nav links, breadcrumbs        */
--text-mono-md:     10px;   /* Section labels, meta          */
--text-mono-sm:     9px;    /* Dates, read-time              */
--text-mono-xs:     8px;    /* Tags, badges, year dividers   */
```

### Line heights

```css
--leading-display: 1.05;   /* Syne display — tight          */
--leading-heading: 1.25;   /* Syne headings                 */
--leading-prose:   1.80;   /* Lora body — generous          */
--leading-body:    1.65;   /* Inter body                    */
--leading-ui:      1.40;   /* Inter UI labels               */
--leading-mono:    1.50;   /* JetBrains Mono                */
```

### Letter spacing

```css
--tracking-display:  -0.02em;   /* Syne headings — tighten       */
--tracking-mono-sm:   0.06em;   /* Mono labels                   */
--tracking-mono-xs:   0.10em;   /* Tags, section labels          */
--tracking-ui:        0.00em;   /* Inter — never tracked out     */
```

---

## Spacing & Layout

```css
/* Page */
--page-max:        1280px;
--page-padding-x:  36px;    /* desktop gutter */
--page-padding-x-md: 20px;  /* tablet         */
--page-padding-x-sm: 16px;  /* mobile         */

/* Nav */
--nav-height:      48px;

/* Sidebar (listing pages) */
--sidebar-width:   200px;

/* Right rail (post detail) */
--rail-width:      180px;

/* Reading column (post detail) */
--prose-max:       680px;

/* Card grid gaps */
--grid-gap:        1px;     /* between cells (hairline, filled by border colour) */
--card-pad:        16px 18px;

/* Section padding */
--section-pad:     32px 36px;
```

---

## Border Radius

```css
--radius-page:     0px;     /* Page/section containers — flush edges */
--radius-card:     8px;     /* Post grid, tool grid, ebook row       */
--radius-datebox:  7px;     /* Date box                              */
--radius-tag:      2px;     /* Tags, badges                          */
--radius-btn:      5px;     /* Buttons, search pill                  */
--radius-input:    5px;     /* Search input                          */
--radius-modal:    10px;    /* Search overlay box, joke card         */
--radius-code:     6px;     /* Code blocks                           */
--radius-favicon:  22%;     /* Favicon canvas (≈10px at 48px canvas) */
```

---

## Shadows

The dark-mode frontend uses very minimal shadow — depth is communicated through surface colour steps, not drop shadows.

```css
/* Dark mode — almost never used */
--shadow-card:     none;
--shadow-modal:    0 24px 48px rgba(0, 0, 0, 0.60);
--shadow-search:   0 16px 32px rgba(0, 0, 0, 0.50);

/* Light mode — used more freely */
--shadow-card-lt:  0 1px 3px rgba(26, 25, 22, 0.08);
--shadow-modal-lt: 0 16px 40px rgba(26, 25, 22, 0.15);
```

---

## Motion

```css
--duration-fast:   120ms;   /* Hover states, icon swaps      */
--duration-base:   200ms;   /* Card hover backgrounds        */
--duration-slow:   350ms;   /* Modal open/close              */
--duration-joke:   30000ms; /* Joke countdown timer          */
--easing-default:  cubic-bezier(0.16, 1, 0.3, 1);
--easing-linear:   linear;

/* Cursor blink (hero wordmark + search input) */
@keyframes cursor-blink {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0; }
}
--cursor-blink: cursor-blink 1.1s step-end infinite;
```

---

## Component Glossary

Quick token-to-component reference for the most-used elements.

### Nav bar
```
bg:            --nav-bg  (backdrop-blur: 8px)
border-bottom: 0.5px solid --border-default
height:        --nav-height
logo font:     --font-mono, 13px, --accent (slash only)
link font:     --font-mono, --text-mono-lg
link colour:   --nav-link  /  active: --nav-link-active
```

### Post card (standard)
```
bg:            --bg-page
bg hover:      --bg-surface-1
border:        part of grid gap (no individual border)
title:         --font-display 700, --text-heading-sm, --text-primary
excerpt:       --font-sans 400, --text-body-md, --text-secondary
meta:          --font-mono 400, --text-mono-xs, --text-faint
tag:           see Tag below
transition:    background --duration-base --easing-default
```

### Post card (featured)
```
bg:            --bg-surface-1
bg hover:      --bg-surface-2
grid-column:   span 2
title:         --font-display 700, --text-heading-md, --text-primary
excerpt:       2-line clamp
```

### Tag
```
font:          --font-mono 400, --text-mono-xs
padding:       1px 5px
radius:        --radius-tag
gilded tag:    color --accent,     bg --accent-tint,     border --accent-border
sage tag:      color --secondary,  bg --secondary-tint,  border --secondary-border
ember tag:     color --tertiary,   bg --tertiary-tint,   border --tertiary-border
```

### Date box (listing)
```
bg:            --datebox-bg
border:        0.5px solid --datebox-border
radius:        --radius-datebox
day:           --font-display 800, 20px (desktop) / 13px (mobile), --datebox-day
month:         --font-mono 400, 8px, --datebox-month, uppercase, tracking 0.05em
width:         52px (desktop) / 34px (mobile)
```

### Year divider (listing)
```
line:          0.5px solid --divider-line, flex:1
label:         --font-mono 400, 9px, --divider-label, tracking 0.08em
margin:        20px 0 10px  (8px 0 6px on first divider)
```

### Like button
```
icon:          ti-heart, --like-icon  /  liked: --like-icon-liked (ember)
count:         --font-mono 400, --text-mono-xs, --like-count
no bg by default — just icon + count inline
```

### Reading progress bar
```
position:      fixed top-0, above nav
height:        2px
bg:            --progress-bg
fill:          --progress-fill
z-index:       above nav
```

### Search modal
```
overlay:       --search-overlay, backdrop-blur 4px
box bg:        --search-bg
box radius:    --radius-modal
input font:    --font-mono 400, 10px
match highlight: --search-match (no background — colour only)
shortcut hint: --font-mono 400, 8px, --text-faint
keyboard nav:  ↑↓ keys, active result bg --bg-surface-2
```

### Joke page
```
card bg:       --joke-card-bg
question:      --font-display 700, --text-heading-md, --text-primary
timer ring track: --joke-ring-track
timer ring fill:  --joke-ring-fill (stroke, not fill)
timer number:  --font-mono 500, 11px, --joke-timer-num
skip button:   --font-mono 400, 8px, --text-muted, border --border-default
answer reveal: fade-in, bg --joke-answer-bg
```

### Footer
```
bg:            --footer-bg
border-top:    0.5px solid --footer-border
logo:          --font-mono, --footer-text (slash: --accent)
links:         --font-mono 400, 9px, --text-faint
lh badges:     see Lighthouse below
grid:          1fr auto 1fr (logo | links | badges)
```

### Lighthouse badges
```
bg:            --lh-bg
border:        0.5px solid --border-default
radius:        --radius-btn
number:        --font-mono 500, 13px, --lh-num
label:         --font-mono 400, 7px, --lh-label
4 badges:      perf / a11y / seo / bp
```