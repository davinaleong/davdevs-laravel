# Dav/Devs — Design Tokens

## Colour Palettes

Each palette follows the Tailwind-style 50–950 spectrum. The **primary slot** (the main brand usage) is marked with ★.

---

### Amber — brand accent

> Primary usage: accent colour, slash mark, CTA buttons, tags, active states, section labels, date-box month, cursor, Lighthouse badges.
> ★ Primary slot: **500**

| Token | Hex | Usage |
|---|---|---|
| `amber-50` | `#FDF8EC` | Hover tint backgrounds |
| `amber-100` | `#FAF0D0` | Tag backgrounds (light mode) |
| `amber-200` | `#F5DFA0` | Tag borders (light mode) |
| `amber-300` | `#EEC96A` | Disabled accent states |
| `amber-400` | `#E3B040` | Hover on primary accent |
| `amber-500` ★ | `#D4A757` | **Main brand accent — slash, CTAs, active nav** |
| `amber-600` | `#B88A3A` | Pressed/active state |
| `amber-700` | `#8F6A27` | Dark mode tag text |
| `amber-800` | `#634A18` | Dark mode tag borders |
| `amber-900` | `#3D2D0D` | Dark mode deep tints |
| `amber-950` | `#221908` | Darkest amber surface |

---

### Ink — neutrals (the backbone)

> Primary usage: backgrounds, surfaces, borders, text, navigation chrome.
> ★ Primary slot: **900** (page background) and **50** (primary text)

| Token | Hex | Usage |
|---|---|---|
| `ink-50` ★ | `#F5F0E8` | **Primary text (dark mode), hero text, post titles** |
| `ink-100` | `#E8E2D8` | Secondary text (dark mode), body copy |
| `ink-200` | `#C8C3BC` | Muted text, excerpts, article body |
| `ink-300` | `#A8A39C` | Meta text, dates, read-time |
| `ink-400` | `#888780` | Placeholder, disabled text |
| `ink-500` | `#6A6A72` | Muted UI labels, icon default |
| `ink-600` | `#4A4A52` | Subtle borders (hover) |
| `ink-700` | `#3A3A42` | Border colour, year divider, meta dots |
| `ink-800` | `#2A2A30` | Default border, card edges |
| `ink-850` | `#242428` | Surface 2 — code blocks, stat cards |
| `ink-900` | `#1A1A1E` | Surface 1 — card hover, sidebar |
| `ink-950` ★ | `#0E0E10` | **Page background** |

> Note: `ink-850` is a non-standard stop added between 800 and 900 because the jump is significant and several surfaces live in that gap.

---

### Teal — secondary accent

> Primary usage: tool tags, secondary category badges, teal-coded content types (Tools, Notebooks, FEM), teal nav active alternative.
> ★ Primary slot: **500**

| Token | Hex | Usage |
|---|---|---|
| `teal-50` | `#EBF7F3` | Tag background (light mode) |
| `teal-100` | `#C5EBE0` | Light tint surfaces |
| `teal-200` | `#8DD5C0` | Tag borders (light mode) |
| `teal-300` | `#5CBFA3` | Hover state |
| `teal-400` | `#3DAD8D` | Icon accent, hover |
| `teal-500` ★ | `#4A9B7F` | **Secondary accent — tool tags, teal badges** |
| `teal-600` | `#357A63` | Pressed state |
| `teal-700` | `#245C49` | Dark mode tag text |
| `teal-800` | `#163D30` | Dark mode tag borders |
| `teal-900` | `#0C261E` | Deep tint |
| `teal-950` | `#061510` | Darkest teal surface |

---

### Coral — tertiary accent

> Primary usage: faith/e-book tags, coral-coded content types (Sermons, E-Books), CTA variant, error states.
> ★ Primary slot: **500**

| Token | Hex | Usage |
|---|---|---|
| `coral-50` | `#FAEEE9` | Tag background (light mode) |
| `coral-100` | `#F5D0C4` | Light tint |
| `coral-200` | `#EDA898` | Tag borders (light mode) |
| `coral-300` | `#DF7E6A` | Hover |
| `coral-400` | `#D36450` | Icon accent |
| `coral-500` ★ | `#C4553A` | **Tertiary accent — faith tags, e-book badges** |
| `coral-600` | `#A0402A` | Pressed state |
| `coral-700` | `#782E1C` | Dark mode tag text |
| `coral-800` | `#511E11` | Dark mode tag borders |
| `coral-900` | `#301109` | Deep tint |
| `coral-950` | `#1A0904` | Darkest coral surface |

---

### Cream — light mode surfaces

> Used exclusively in light mode. Dark mode never references these.
> ★ Primary slot: **50** (light mode page background)

| Token | Hex | Usage |
|---|---|---|
| `cream-50` ★ | `#FAF7F2` | **Light mode page background** |
| `cream-100` | `#F5F1EB` | Light mode nav / footer background |
| `cream-200` | `#EDE9E2` | Light mode card hover |
| `cream-300` | `#E4E0D9` | Light mode card background |
| `cream-400` | `#D8D4CC` | Light mode default border |
| `cream-500` | `#C8C3BB` | Light mode strong border |
| `cream-600` | `#A8A39B` | Light mode muted text |
| `cream-700` | `#88837C` | Light mode secondary text |
| `cream-800` | `#5A5850` | Light mode body text |
| `cream-900` | `#2A2820` | Light mode headings |
| `cream-950` | `#1A1916` | Light mode primary text |

---

## Semantic Aliases

Map semantic roles to palette slots. Reference these in Tailwind config or CSS variables — never hardcode the hex directly in components.

```js
// tailwind.config.js — extend.colors
colors: {
  // Surfaces (dark mode default)
  'surface-page':    '#0E0E10', // ink-950
  'surface-1':       '#1A1A1E', // ink-900  — card, sidebar
  'surface-2':       '#242428', // ink-850  — code blocks, stat cards
  'surface-3':       '#2A2A30', // ink-800  — raised modals

  // Borders
  'border-default':  '#2A2A30', // ink-800
  'border-strong':   '#3A3A42', // ink-700
  'border-accent':   '#D4A757', // amber-500

  // Text
  'text-primary':    '#F5F0E8', // ink-50
  'text-secondary':  '#A8A39C', // ink-300
  'text-muted':      '#6A6A72', // ink-500
  'text-faint':      '#3A3A42', // ink-700

  // Accent roles
  'accent':          '#D4A757', // amber-500
  'accent-hover':    '#E3B040', // amber-400
  'accent-pressed':  '#B88A3A', // amber-600
  'accent-tint':     'rgba(212,167,87,0.10)',
  'accent-border':   'rgba(212,167,87,0.20)',

  'secondary':       '#4A9B7F', // teal-500
  'secondary-tint':  'rgba(74,155,127,0.10)',
  'secondary-border':'rgba(74,155,127,0.20)',

  'tertiary':        '#C4553A', // coral-500
  'tertiary-tint':   'rgba(196,85,58,0.10)',
  'tertiary-border': 'rgba(196,85,58,0.20)',
}
```

---

## Tag Colour System

Three tag colours map to content categories — consistent across all post types.

| Colour | Token | Used for |
|---|---|---|
| Amber | `amber-500` | AI, security, dev tools, azure, general dev |
| Teal | `teal-500` | Frameworks, languages, JS, Laravel, Python |
| Coral | `coral-500` | Faith, e-books, sermons, personal |

Tag anatomy (dark mode):
```
background: {colour}-500 at 10% opacity
border:     {colour}-500 at 20% opacity
text:       {colour}-500
font:       JetBrains Mono, 7px
padding:    1px 5px
radius:     2px
```

---

## Typography

### Font stack

| Role | Family | Weights | Used for |
|---|---|---|---|
| Display | Syne | 700, 800 | Wordmark, page titles, post titles, section headings, date-box day numbers, stat numbers |
| Mono | JetBrains Mono | 400, 500 | Nav links, section labels, tags, dates, meta text, code, search, breadcrumbs, year dividers, the logo |
| Body | Inter | 400, 500 | Body copy, UI labels, buttons, excerpts, sidebar filters |
| Prose | Lora | 400, 400 italic | Article/post body text only (reading context) |

### Why these four

**Syne** — geometric, high x-height, slightly unusual at heavy weights. The 800 weight for the `Dav/Devs` wordmark has a compressed authority that neither Inter nor a standard grotesque achieves. Not overused; only reaches for it when something needs to be a statement.

**JetBrains Mono** — the terminal register. Every UI label, date, tag, and nav item that should feel like it was typed, not designed. It's what makes the aesthetic feel like a developer's tool rather than a generic portfolio. Used at a small size (8–10px) almost everywhere — it's not trying to be readable prose, it's trying to be infrastructure.

**Inter** — the neutral workhorse. Buttons, body labels, excerpts, form elements. Chosen because it disappears — it never competes with Syne or JetBrains Mono, it just carries information cleanly.

**Lora** — serif for long-form reading only. Used exclusively inside article/post body text. The contrast between reading in Lora and navigating in Inter + JetBrains Mono reinforces the mental switch from "browsing the site" to "reading a piece." Italic weight for pull quotes and emphasis.

### Type scale

| Name | Size | Weight | Family | Used for |
|---|---|---|---|---|
| `display-xl` | 48px | 800 | Syne | Hero wordmark (desktop) |
| `display-lg` | 32px | 800 | Syne | Hero wordmark (mobile), listing page title |
| `display-md` | 26px | 800 | Syne | Post detail title (desktop) |
| `display-sm` | 20px | 800 | Syne | Post detail title (mobile), stat numbers |
| `heading-lg` | 18px | 700 | Syne | Section headings in post body |
| `heading-md` | 16px | 700 | Syne | Card titles (featured), sidebar headings |
| `heading-sm` | 14px | 700 | Syne | Card titles (standard), date-box day |
| `body-lg` | 14px | 400 | Lora | Article body text (desktop) |
| `body-md` | 13px | 400 | Inter | Excerpts, bio, general UI copy |
| `body-sm` | 11px | 400 | Inter | Card excerpts, secondary descriptions |
| `ui-md` | 11px | 400/500 | Inter | Button labels, sidebar items, form labels |
| `mono-md` | 11px | 400/500 | JetBrains Mono | Nav links, breadcrumbs, code snippets |
| `mono-sm` | 10px | 400 | JetBrains Mono | Section labels, meta text, dates |
| `mono-xs` | 8–9px | 400 | JetBrains Mono | Tags, badges, year dividers, icon labels |

### Google Fonts import

```css
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500&family=Lora:ital,wght@0,400;1,400&display=swap');
```

### Tailwind font config

```js
// tailwind.config.js — extend.fontFamily
fontFamily: {
  display: ['Syne', 'sans-serif'],
  mono:    ['JetBrains Mono', 'monospace'],
  sans:    ['Inter', 'sans-serif'],
  prose:   ['Lora', 'serif'],
}
```

### Usage rules

- **Syne only for display sizes** — never use Syne below 12px. Below that weight, the geometric construction breaks down and it reads as noise.
- **JetBrains Mono at 8–11px** — this is its home in the system. Don't use it for body text or long-form reading.
- **Lora only inside post/article content** — not in UI chrome, not in cards, not in navigation.
- **Inter for everything else** — buttons, labels, excerpts, forms. The default when nothing else fits.
- **Two weights only per family in practice** — 400 (regular) and 500 (medium) for Inter and JetBrains Mono. 700 and 800 for Syne. 400 and 400 italic for Lora. No 600, no 900.

---

## Favicon Recommendation

**Option C — amber block** (`amber-500` fill, `ink-950` glyph):

```
background: #D4A757  (amber-500)
glyph:      /  in #0E0E10  (ink-950)
font:       JetBrains Mono 500
radius:     22% of canvas size  (≈ 10px at 48px canvas)
```

Rationale: self-contained — works in dark and light browser chrome without adaptation. The amber block is visually distinct in a tab row of mostly white favicons. Collapses gracefully to a pure amber square at 16px, which still reads as the brand mark.