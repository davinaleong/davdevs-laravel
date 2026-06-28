# Dav/Devs — Design Language: CMS (Admin Panel)

---

## Design Principles (CMS-specific)

The CMS is a **data-entry environment**, not a showcase. Every decision here optimises for:
- **Scannability** — dense tables, clear hierarchy, fast triage
- **Error prevention** — clear status colours, confirmation states, destructive action gating
- **Focus** — light mode default (easier on eyes for long writing/editing sessions)
- **Distinction from the frontend** — you should never mistake CMS for the public site. The amber topbar is the primary signal.

---

## Colour Tokens

### Named palette (same names, CMS-specific roles)

```css
--color-obsidian:  #0E0E10;
--color-parchment: #F5F0E8;
--color-gilded:    #D4A757;
--color-graphite:  #2A2A2F;
--color-sage:      #4A9B7F;
--color-ember:     #C4553A;
```

---

### Light mode (CMS default)

```css
[data-cms][data-theme="light"] {
  /* Chrome */
  --cms-topbar-bg:       #D4A757;   /* gilded — the unmistakable CMS signal      */
  --cms-topbar-text:     #0E0E10;   /* obsidian on gilded                        */
  --cms-topbar-badge-bg: rgba(0, 0, 0, 0.12);
  --cms-topbar-badge-text: #0E0E10;

  --cms-sidebar-bg:      #F4F2ED;
  --cms-sidebar-border:  #E5E3DD;
  --cms-sidebar-link:    #5A5850;
  --cms-sidebar-link-active: #0E0E10;
  --cms-sidebar-active-bar:  #D4A757;   /* gilded left border on active item      */
  --cms-sidebar-active-bg:   #FFFFFF;

  /* Page */
  --cms-bg-page:         #FAFAF8;   /* slightly warm white — easier than pure #fff */
  --cms-bg-surface:      #FFFFFF;
  --cms-bg-surface-2:    #F5F3EE;   /* inset / recessed areas                   */
  --cms-bg-hover:        #F0EDE8;

  /* Borders */
  --cms-border:          #E5E3DD;
  --cms-border-strong:   #D0CEC8;
  --cms-border-focus:    #D4A757;   /* gilded — all focus rings                 */

  /* Text */
  --cms-text-primary:    #1A1916;
  --cms-text-secondary:  #5A5850;
  --cms-text-muted:      #9A9590;
  --cms-text-faint:      #C5C3BC;
  --cms-text-on-accent:  #0E0E10;   /* text on gilded background                */

  /* Accent */
  --cms-accent:          #D4A757;   /* gilded — primary action buttons          */
  --cms-accent-hover:    #C49A48;
  --cms-accent-pressed:  #B08838;
  --cms-accent-tint:     rgba(212, 167, 87, 0.10);
  --cms-accent-border:   rgba(212, 167, 87, 0.30);

  /* Status colours */
  --cms-status-published:   #2B7A5E;   /* sage dark                             */
  --cms-status-draft:       #9A9590;   /* muted grey                            */
  --cms-status-private:     #7A5A2A;   /* gilded dark                           */
  --cms-status-archived:    #5A5850;   /* dark grey                             */

  --cms-status-published-bg:  rgba(43, 122, 94, 0.10);
  --cms-status-draft-bg:      rgba(154, 149, 144, 0.10);
  --cms-status-private-bg:    rgba(122, 90, 42, 0.10);
  --cms-status-archived-bg:   rgba(90, 88, 80, 0.10);

  /* Type badge colours (post type chips) */
  --cms-type-article:    #1A6BAA;   /* blue                                      */
  --cms-type-article-bg: #EBF5FF;
  --cms-type-project:    #8A5A0A;   /* amber-brown                               */
  --cms-type-project-bg: #FFF6E8;
  --cms-type-tool:       #1A6B4A;   /* green                                     */
  --cms-type-tool-bg:    #EAF5F0;
  --cms-type-notebook:   #6A3A9A;   /* purple                                    */
  --cms-type-notebook-bg:#F3EEFF;
  --cms-type-ebook:      #A03020;   /* ember dark                                */
  --cms-type-ebook-bg:   #FAEEE9;
  --cms-type-sermon:     #7A4A1A;   /* deep amber                                */
  --cms-type-sermon-bg:  #FFF3E0;
  --cms-type-page:       #3A5A7A;   /* slate                                     */
  --cms-type-page-bg:    #EEF3FA;
  --cms-type-fem:        #3A7A5A;   /* teal-green                                */
  --cms-type-fem-bg:     #EAF5EE;
  --cms-type-ks:         #5A5A8A;   /* indigo                                    */
  --cms-type-ks-bg:      #EEEEFF;

  /* Feedback */
  --cms-success:         #2B7A5E;
  --cms-success-bg:      rgba(43, 122, 94, 0.10);
  --cms-warning:         #D4A757;
  --cms-warning-bg:      rgba(212, 167, 87, 0.12);
  --cms-error:           #C4553A;
  --cms-error-bg:        rgba(196, 85, 58, 0.10);
  --cms-info:            #1A6BAA;
  --cms-info-bg:         rgba(26, 107, 170, 0.10);

  /* Table */
  --cms-table-header-bg: #F4F2ED;
  --cms-table-row-hover: #F8F6F2;
  --cms-table-border:    #E5E3DD;
  --cms-table-stripe:    #FAFAF8;

  /* Form inputs */
  --cms-input-bg:        #FFFFFF;
  --cms-input-border:    #D0CEC8;
  --cms-input-border-hover: #B8B6B0;
  --cms-input-border-focus: #D4A757;
  --cms-input-text:      #1A1916;
  --cms-input-placeholder: #C5C3BC;
  --cms-input-disabled-bg: #F4F2ED;

  /* Markdown editor */
  --cms-editor-bg:       #FFFFFF;
  --cms-editor-border:   #E5E3DD;
  --cms-editor-toolbar:  #F4F2ED;
  --cms-editor-text:     #1A1916;
  --cms-editor-preview:  #FAFAF8;

  /* Buttons */
  --cms-btn-primary-bg:  #D4A757;
  --cms-btn-primary-text:#0E0E10;
  --cms-btn-secondary-bg:#FFFFFF;
  --cms-btn-secondary-border: #D0CEC8;
  --cms-btn-secondary-text:   #5A5850;
  --cms-btn-danger-bg:   #C4553A;
  --cms-btn-danger-text: #FFFFFF;
  --cms-btn-ghost-text:  #9A9590;

  /* Log level colours */
  --cms-log-debug:       #9A9590;
  --cms-log-info:        #1A6BAA;
  --cms-log-warning:     #D4A757;
  --cms-log-error:       #C4553A;
  --cms-log-critical:    #7A1A0A;

  --cms-log-debug-bg:    rgba(154, 149, 144, 0.08);
  --cms-log-info-bg:     rgba(26, 107, 170, 0.08);
  --cms-log-warning-bg:  rgba(212, 167, 87, 0.10);
  --cms-log-error-bg:    rgba(196, 85, 58, 0.10);
  --cms-log-critical-bg: rgba(122, 26, 10, 0.10);
}
```

---

### Dark mode (CMS)

```css
[data-cms][data-theme="dark"] {
  /* Chrome */
  --cms-topbar-bg:       #B88A3A;   /* gilded-600 — darker amber for dark mode  */
  --cms-topbar-text:     #0E0E10;
  --cms-topbar-badge-bg: rgba(0, 0, 0, 0.20);
  --cms-topbar-badge-text: #0E0E10;

  --cms-sidebar-bg:      #1A1A1E;
  --cms-sidebar-border:  #2A2A30;
  --cms-sidebar-link:    #6A6A72;
  --cms-sidebar-link-active: #F5F0E8;
  --cms-sidebar-active-bar:  #D4A757;
  --cms-sidebar-active-bg:   #242428;

  /* Page */
  --cms-bg-page:         #0E0E10;
  --cms-bg-surface:      #1A1A1E;
  --cms-bg-surface-2:    #242428;
  --cms-bg-hover:        #2A2A30;

  /* Borders */
  --cms-border:          #2A2A30;
  --cms-border-strong:   #3A3A42;
  --cms-border-focus:    #D4A757;

  /* Text */
  --cms-text-primary:    #F5F0E8;
  --cms-text-secondary:  #A8A39C;
  --cms-text-muted:      #6A6A72;
  --cms-text-faint:      #3A3A42;
  --cms-text-on-accent:  #0E0E10;

  /* Accent */
  --cms-accent:          #D4A757;
  --cms-accent-hover:    #E3B040;
  --cms-accent-pressed:  #B88A3A;
  --cms-accent-tint:     rgba(212, 167, 87, 0.10);
  --cms-accent-border:   rgba(212, 167, 87, 0.25);

  /* Status */
  --cms-status-published:   #4A9B7F;
  --cms-status-draft:       #6A6A72;
  --cms-status-private:     #D4A757;
  --cms-status-archived:    #4A4A52;

  --cms-status-published-bg:  rgba(74, 155, 127, 0.12);
  --cms-status-draft-bg:      rgba(106, 106, 114, 0.12);
  --cms-status-private-bg:    rgba(212, 167, 87, 0.12);
  --cms-status-archived-bg:   rgba(74, 74, 82, 0.12);

  /* Type badges — same hues, adjusted for dark bg */
  --cms-type-article:    #5AAAE0;
  --cms-type-article-bg: rgba(90, 170, 224, 0.12);
  --cms-type-project:    #D4A757;
  --cms-type-project-bg: rgba(212, 167, 87, 0.12);
  --cms-type-tool:       #4A9B7F;
  --cms-type-tool-bg:    rgba(74, 155, 127, 0.12);
  --cms-type-notebook:   #A07ADA;
  --cms-type-notebook-bg:rgba(160, 122, 218, 0.12);
  --cms-type-ebook:      #C4553A;
  --cms-type-ebook-bg:   rgba(196, 85, 58, 0.12);
  --cms-type-sermon:     #D4A757;
  --cms-type-sermon-bg:  rgba(212, 167, 87, 0.10);
  --cms-type-page:       #7AAAD0;
  --cms-type-page-bg:    rgba(122, 170, 208, 0.12);
  --cms-type-fem:        #4AB07A;
  --cms-type-fem-bg:     rgba(74, 176, 122, 0.12);
  --cms-type-ks:         #8A8ADA;
  --cms-type-ks-bg:      rgba(138, 138, 218, 0.12);

  /* Feedback */
  --cms-success:         #4A9B7F;
  --cms-success-bg:      rgba(74, 155, 127, 0.12);
  --cms-warning:         #D4A757;
  --cms-warning-bg:      rgba(212, 167, 87, 0.12);
  --cms-error:           #C4553A;
  --cms-error-bg:        rgba(196, 85, 58, 0.12);
  --cms-info:            #5AAAE0;
  --cms-info-bg:         rgba(90, 170, 224, 0.12);

  /* Table */
  --cms-table-header-bg: #1A1A1E;
  --cms-table-row-hover: #1E1E22;
  --cms-table-border:    #2A2A30;
  --cms-table-stripe:    #0E0E10;

  /* Form inputs */
  --cms-input-bg:        #1A1A1E;
  --cms-input-border:    #2A2A30;
  --cms-input-border-hover: #3A3A42;
  --cms-input-border-focus: #D4A757;
  --cms-input-text:      #F5F0E8;
  --cms-input-placeholder: #3A3A42;
  --cms-input-disabled-bg: #242428;

  /* Markdown editor */
  --cms-editor-bg:       #1A1A1E;
  --cms-editor-border:   #2A2A30;
  --cms-editor-toolbar:  #242428;
  --cms-editor-text:     #F5F0E8;
  --cms-editor-preview:  #0E0E10;

  /* Buttons */
  --cms-btn-primary-bg:  #D4A757;
  --cms-btn-primary-text:#0E0E10;
  --cms-btn-secondary-bg:#1A1A1E;
  --cms-btn-secondary-border: #2A2A30;
  --cms-btn-secondary-text:   #A8A39C;
  --cms-btn-danger-bg:   #C4553A;
  --cms-btn-danger-text: #F5F0E8;
  --cms-btn-ghost-text:  #6A6A72;

  /* Log level colours */
  --cms-log-debug:       #6A6A72;
  --cms-log-info:        #5AAAE0;
  --cms-log-warning:     #D4A757;
  --cms-log-error:       #C4553A;
  --cms-log-critical:    #FF6B4A;

  --cms-log-debug-bg:    rgba(106, 106, 114, 0.08);
  --cms-log-info-bg:     rgba(90, 170, 224, 0.08);
  --cms-log-warning-bg:  rgba(212, 167, 87, 0.10);
  --cms-log-error-bg:    rgba(196, 85, 58, 0.10);
  --cms-log-critical-bg: rgba(255, 107, 74, 0.12);
}
```

---

## Typography

### Families

```css
--cms-font-ui:    'Inter', sans-serif;
--cms-font-mono:  'JetBrains Mono', monospace;
--cms-font-label: 'Inter', sans-serif;
```

The CMS uses **no Syne and no Lora**. Syne is too editorial for data-entry. Lora is for reading long-form content. The CMS is built on Inter + JetBrains Mono only.

```css
/* Import — CMS only needs two families */
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Inter:wght@400;500;600&display=swap');
```

Note: Inter 600 is used in the CMS (table column headers, button labels, active nav) — this weight is not loaded on the frontend.

### Usage rules

| Family | Where | Never |
|---|---|---|
| Inter 400/500/600 | All UI — tables, forms, labels, buttons, sidebar, dashboard | Post content display |
| JetBrains Mono 400/500 | Slugs, log entries, code fields, post type badges, status chips, date/time values, IDs | Running UI text, paragraph content |

### Type scale

```css
/* Section headings */
--cms-text-page-title:   20px;   /* Inter 600 — e.g. "All Posts", "Image Manager" */
--cms-text-section:      13px;   /* Inter 600 — card/panel section headers        */
--cms-text-label:        12px;   /* Inter 500 — form labels, column headers       */

/* Body */
--cms-text-body:         13px;   /* Inter 400 — general UI text                  */
--cms-text-body-sm:      12px;   /* Inter 400 — secondary descriptions           */
--cms-text-body-xs:      11px;   /* Inter 400 — helper text, hints               */

/* Mono */
--cms-text-mono-md:      12px;   /* JetBrains — slugs, log messages, IDs         */
--cms-text-mono-sm:      11px;   /* JetBrains — badges, status chips, dates      */
--cms-text-mono-xs:      10px;   /* JetBrains — compact table cells, timestamps  */

/* Buttons */
--cms-text-btn-md:       13px;   /* Inter 500 — primary/secondary buttons        */
--cms-text-btn-sm:       12px;   /* Inter 500 — compact buttons                  */
```

---

## Spacing & Layout

```css
/* Topbar */
--cms-topbar-height:     40px;

/* Sidebar */
--cms-sidebar-width:     200px;
--cms-sidebar-collapsed: 52px;   /* icon-only collapsed state                   */

/* Content area */
--cms-content-pad:       24px;
--cms-content-max:       1100px; /* max width for content well (not full bleed) */

/* Form layout */
--cms-form-gap:          16px;   /* gap between form fields                     */
--cms-field-label-gap:   6px;    /* gap between label and input                 */

/* Table */
--cms-table-row-height:  44px;   /* comfortable tap/click target                */
--cms-table-header-height: 36px;

/* Card/panel */
--cms-card-pad:          20px;
--cms-card-radius:       8px;
```

---

## Border Radius

```css
--cms-radius-card:    8px;    /* Dashboard stat cards, content panels      */
--cms-radius-input:   5px;    /* All form inputs, selects, textareas       */
--cms-radius-btn:     5px;    /* All buttons                               */
--cms-radius-badge:   3px;    /* Status chips, type badges                 */
--cms-radius-tag:     3px;    /* Tags in post editor                       */
--cms-radius-modal:   10px;   /* Confirmation dialogs, image picker        */
--cms-radius-toast:   6px;    /* Notification toasts                       */
```

---

## Component Glossary

### Topbar
```
bg:          --cms-topbar-bg          (gilded — always, both modes)
text:        --cms-topbar-text        (obsidian)
height:      --cms-topbar-height
logo:        --cms-font-mono 500, 11px
badge:       --cms-topbar-badge-bg / text — "admin" pill
purpose:     THE visual signal you are in the CMS, not the public site
```

### Sidebar nav
```
bg:          --cms-sidebar-bg
border-right: 0.5px solid --cms-sidebar-border
width:        --cms-sidebar-width
item font:   --cms-font-ui 400, --cms-text-body
item active: bg --cms-sidebar-active-bg, text --cms-sidebar-link-active,
             left border 2px solid --cms-sidebar-active-bar
section dots: small coloured dots (gilded/sage/muted) per nav group
```

### Data table
```
header bg:   --cms-table-header-bg
header font: --cms-font-label 600, --cms-text-mono-xs (JetBrains),
             uppercase, tracking 0.06em, --cms-text-muted
row height:  --cms-table-row-height
row hover:   --cms-table-row-hover
row border:  0.5px solid --cms-table-border
stripe:      alternate rows --cms-table-stripe (subtle, optional)
```

### Status chip
```
font:     --cms-font-mono 400, --cms-text-mono-sm
radius:   --cms-radius-badge
padding:  2px 7px

published: color --cms-status-published, bg --cms-status-published-bg
draft:     color --cms-status-draft,     bg --cms-status-draft-bg
private:   color --cms-status-private,   bg --cms-status-private-bg
archived:  color --cms-status-archived,  bg --cms-status-archived-bg
```

### Post type badge
```
font:     --cms-font-mono 400, --cms-text-mono-sm
radius:   --cms-radius-badge
padding:  2px 7px
colours:  see --cms-type-* tokens above (one per post type)
```

### Form input
```
bg:           --cms-input-bg
border:       1px solid --cms-input-border
border hover: --cms-input-border-hover
border focus: --cms-input-border-focus  (gilded, 1.5px)
border-radius:--cms-radius-input
font:         --cms-font-ui 400, --cms-text-body, --cms-input-text
placeholder:  --cms-input-placeholder
focus ring:   outline: 2px solid --cms-accent-tint, outline-offset: 2px
```

### Markdown editor
```
wrapper border: 1px solid --cms-editor-border
toolbar bg:     --cms-editor-toolbar
editor bg:      --cms-editor-bg
editor font:    --cms-font-mono 400, 13px, --cms-editor-text
preview bg:     --cms-editor-preview
preview prose:  rendered as frontend post detail (Lora, prose scale)
split pane:     50/50 editor | preview, resizable divider
```

### Buttons
```
primary:   bg --cms-btn-primary-bg,  text --cms-btn-primary-text,  radius --cms-radius-btn
secondary: bg --cms-btn-secondary-bg, border 1px solid --cms-btn-secondary-border,
           text --cms-btn-secondary-text
danger:    bg --cms-btn-danger-bg,   text --cms-btn-danger-text
ghost:     bg transparent, text --cms-btn-ghost-text

font:      --cms-font-ui 500, --cms-text-btn-md
padding:   7px 14px (md) / 5px 10px (sm)
```

### Log viewer rows
```
font:       --cms-font-mono 400, --cms-text-mono-sm
channel:    --cms-text-muted, monospace label e.g. [http] [auth] [cms]
level:      colour-coded chip (see --cms-log-* tokens)
message:    --cms-text-primary, mono
timestamp:  --cms-text-faint, mono xs, right-aligned
row bg:     matches level bg at low opacity (only on warning/error/critical)
```

### Image browser (lightbox)
```
grid:       auto-fill, minmax(120px, 1fr), gap 8px
cell bg:    --cms-bg-surface-2
cell hover: --cms-bg-hover + border --cms-border-focus (gilded)
QR filter:  toggle pill — same style as status filter above
lightbox overlay: rgba(0,0,0,0.85)
lightbox panel: --cms-bg-surface, max-width 800px, radius --cms-radius-modal
```

### Toast notifications
```
radius:    --cms-radius-toast
font:      --cms-font-ui 400, --cms-text-body-sm
position:  bottom-right, stacked, z-index highest
success:   --cms-success-bg, left border 3px --cms-success
warning:   --cms-warning-bg, left border 3px --cms-warning
error:     --cms-error-bg,   left border 3px --cms-error
info:      --cms-info-bg,    left border 3px --cms-info
auto-dismiss: 4s (success/info), manual dismiss (error)
```

### 2FA setup screen
```
QR code container: --cms-bg-surface, border --cms-border, radius --cms-radius-card
TOTP input:        6-digit split input, --cms-font-mono 500, 20px, letter-spacing 0.2em
recovery codes:    --cms-font-mono 400, --cms-text-mono-md, bg --cms-bg-surface-2,
                   one per line, monospace, copyable
```