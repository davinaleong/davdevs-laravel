# Dav/Devs Laravel CMS — Milestone Checklist v2
> Stack: Laravel · Blade + Tailwind + Alpine · MySQL · Laravel Cloud · Cloudinary · React (Tools)
> New in v2: 2FA, data export, headless CMS / GraphQL (bonus), updated joke UX, link manager, post statuses, social sharing

---

## Phase 0 — Project Setup

- [x] `laravel new davdevs --git`
- [x] Configure `.env`: MySQL, Laravel Cloud, Cloudinary, TOTP secret
- [x] Install frontend: Tailwind CSS v4, Alpine.js, Vite
- [x] Install packages:
  - `spatie/laravel-permission`
  - `spatie/laravel-sitemap`
  - `spatie/laravel-sluggable`
  - `spatie/laravel-data` (typed DTOs for export + headless API)
  - `pragmarx/google2fa-laravel` (TOTP 2FA)
  - `bacon/bacon-qr-code` (2FA QR code generation)
  - `league/commonmark` (Markdown rendering)
  - `cloudinary-labs/cloudinary-laravel`
- [x] Install dev: Pest, Laravel Pint, Larastan, Laravel Debugbar
- [ ] Set up Laravel Cloud project, environments (production + staging), deploy pipeline
- [x] GitHub Actions CI: Pint lint + Pest tests on every PR
- [x] Security headers middleware: CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy
- [x] Global rate limiting middleware (public API routes + like endpoint)

> **Cloudinary vs R2:** Cloudinary is the right choice here — easier Laravel SDK, built-in image transformations (resize, format, quality via URL params), and good free tier. You already use R2 on Beacon; keep concerns separate. Revisit if Cloudinary costs scale.

---

## Phase 1 — Database Schema & Core Models

See the separate **DB Schemas** document for full column definitions.

- [x] Migrate all tables (see schema doc): `settings`, `images`, `links`, `youtube_embeds`, `layouts`, `post_types`, `categories`, `tags`, `posts`, `post_images`, `post_youtube_embeds`, `post_links`, `post_tags`, `post_meta`, `jokes`, `likes`, `logs`, `redirects`, `export_jobs`
- [x] Seed post types: Page, Project, Article, Tool, Notebook, Knowledge Sharing, Frontend Mentor, Sermon, E-Book
- [x] Create Eloquent models with relationships, casts, and accessors
- [ ] Model observers: auto-write to `logs` on create/update/delete for all content models
- [x] `Post::saving()` observer: recalculate `read_time_minutes` from word count before every save
- [x] Global scopes: `PublishedScope` (status = published, published_at <= now) for frontend queries

---

## Phase 2 — Authentication & 2FA

- [x] Laravel Breeze (Blade) for auth scaffolding
- [x] Disable public registration — invite-only or env-gated
- [ ] TOTP 2FA via `pragmarx/google2fa-laravel`:
  - [ ] Generate secret on first login, display setup QR code (via `bacon/bacon-qr-code`)
  - [ ] Store encrypted TOTP secret on `users` table
  - [ ] Middleware: require valid TOTP token after password auth before reaching CMS
  - [ ] Recovery codes: generate 8 single-use codes, store hashed
  - [ ] 2FA disable flow (requires password re-confirmation)
- [x] Session timeout: auto-logout after 2 hours of inactivity
- [x] Lock CMS routes behind `auth` + `2fa.verified` middleware group

---

## Phase 3 — CMS Shell & Settings

- [x] CMS layout: amber topbar (`~/dav/devs cms` + admin badge), collapsible sidebar, main content area
- [x] Light/dark mode toggle: Alpine + `localStorage` + `data-theme` on `<html>`, respected sitewide
- [x] CMS dashboard: post counts by type, recent logs, like counts, quick-create shortcuts
- [x] Settings page (grouped):
  - [x] **Display:** frontend date format, CMS date format
  - [x] **Header:** brand name, brand image (image picker modal)
  - [x] **Footer:** copyright text (supports `{year}` token)
  - [x] **Lighthouse:** toggle display of each metric (performance, accessibility, SEO, best practices) on public site
  - [x] **Headless CMS:** toggle to enable/disable public API (bonus)
- [x] Settings stored as key/value in `settings` table, cached (Redis or file), flushed via CMS button

---

## Phase 4 — Image Manager

- [x] Upload to Cloudinary with metadata form: alt text, title, caption, credit
- [x] Auto-store from Cloudinary response: `public_id`, `secure_url`, `width`, `height`, `format`, `bytes`
- [x] `is_qr_code` boolean flag — same table, filtered separately in browser (no separate system needed)
- [x] CMS image browser: grid view, search by title/alt, filter by type (image / QR code), date sort, cursor-paginated
- [x] Lightbox: full-size preview with metadata panel (Alpine modal, keyboard nav ←→, ESC to close)
- [x] Edit metadata, replace image file (new Cloudinary upload, same DB record ID), soft-delete with usage check
- [x] Usage tracker: "Used in N posts" label with link list

---

## Phase 5 — Link Manager

Links are standalone records (reusable across posts, footer, navbar, etc.).

- [x] CRUD: label, URL, target (`_self` / `_blank`), rel (`noopener noreferrer` auto-applied for `_blank`), description, is_active, sort_order
- [x] CMS: searchable list, drag-to-reorder sort (Alpine + SortableJS)
- [x] Links attachable to posts via `post_links` pivot (with sort_order)
- [x] Social links: a `category` field (`social` / `general`) lets the footer renderer filter to social links only

---

## Phase 6 — YouTube Embed Manager

- [x] Add by video ID or full URL — auto-fetch title, channel, thumbnail, duration via YouTube oEmbed
- [x] Store: `video_id`, `title`, `description`, `channel_name`, `thumbnail_url`, `duration_seconds`, `published_at`
- [x] CMS: browse, search, edit, delete
- [ ] Public rendering: click-to-load iframe (`youtube-nocookie.com`) — no YouTube cookies until user clicks. Show thumbnail + play button by default (privacy-first)

---

## Phase 7 — Layout Manager

- [x] Layouts defined as Blade components (`layouts.standard`, `layouts.product`, `layouts.tool`, `layouts.ebook`, `layouts.sermon`, `layouts.fem`)
- [x] CMS CRUD: name, slug, `blade_component` name, description, preview image (image picker)
- [x] Validate Blade component exists before saving
- [x] Seed default layouts matching all post types

---

## Phase 8 — Post Type, Category & Tag Managers

- [x] Post type manager: name, slug, `has_tools_react` flag, `excluded_from_main_list` flag (for Jokes), description
- [x] Category manager: name, slug, scoped by `post_type_id`, description, active toggle
- [x] Tag manager: name, slug, scoped by `post_type_id`
- [ ] All category/tag dropdowns in post editor filtered by selected post type

---

## Phase 9 — Post Manager

- [x] CMS post list: filter by type / status / category / featured / date range, full-text search, sortable columns, cursor-paginated
- [ ] Post create/edit form:
  - [x] Title, slug (auto-generated, editable), excerpt
  - [x] Post type → drives layout options, category/tag scope, and conditional fields
  - [x] Layout selector
  - [x] **Status:** Draft / Private / Published (Private = authenticated users only; Published = public)
  - [x] `published_at` datetime picker (schedule future posts)
  - [x] Featured toggle
  - [x] Category (scoped), tags (multi-select, scoped)
  - [x] OG image picker (image browser modal)
  - [x] Markdown editor (CodeMirror or EasyMDE with live preview split-pane)
  - [x] Computed read time (shown live in editor sidebar, stored on save)
  - [x] Image linker: multi-select from image browser, sortable, optional caption override per attachment
  - [x] YouTube embed linker: multi-select, sortable
  - [x] Link linker: multi-select from link manager, sortable
  - [x] Post meta editor: add/remove key-value rows (for LS UUIDs, GitHub URLs, Lighthouse scores, etc.)
  - [x] **E-Book specific fields** (shown when type = E-Book): `ls_product_id`, `ls_variant_id`, `ls_store_url`, `is_bundle` toggle
  - [x] **Joke fields** (shown when type = Joke): `joke_type` (qa / statement), `joke_question` (for Q/A variant)
  - [ ] **Social sharing links** (shown on published posts): pre-built share URLs for LinkedIn, Facebook, Instagram, Threads — one-click copy + open
- [x] Post preview: opens frontend URL in new tab; draft posts use a signed preview token
- [x] Post duplicate action
- [x] Bulk actions: publish, archive, delete selected

---

## Phase 10 — Joke System

Jokes are a special post type: stored in the `jokes` table (not `posts`), excluded from all main post lists and sitemaps, and served randomly on the `/funny` page.

- [x] `jokes` table: `id`, `type` (qa/statement), `question` (nullable), `answer`, `is_active`, `created_at`, `updated_at`
- [x] CMS: dedicated joke manager (separate from post manager) — CRUD, bulk toggle active, filter by type
- [ ] Public `/funny` page:
  - [ ] On load: fetch a random active joke from the server (no full page reload — Alpine + fetch)
  - [ ] Refresh button: fetches a new random joke (guaranteed different from current)
  - [ ] **Q/A variant UX:** Display question immediately. Show a circular countdown timer (30s, CSS animation). After countdown, reveal answer with a fade-in. User can also tap "Show answer" to skip the timer.
  - [ ] **Statement variant UX:** Display the statement directly, no timer needed.
  - [ ] Disclaimer text (carry over from current site)
  - [ ] No joke IDs in the URL (they're not individually linkable)

---

## Phase 11 — Anonymous Like System

- [x] On first visit: generate UUID token, store in signed HttpOnly SameSite=Strict cookie (1 year)
- [x] `likes` table: `post_id`, `token_hash` (SHA-256 of token), `ip_hash` (SHA-256 of IP + daily salt), `created_at`
- [x] Like/unlike endpoint: rate-limited (5 req/min per IP), CSRF-protected, idempotent, returns updated count
- [x] Public: like button with Alpine reactive counter + optimistic UI
- [ ] CMS: like counts in post list column

---

## Phase 12 — Frontend (public site)

- [x] Global layout: header (brand, nav, search trigger `Cmd/Ctrl+K`, dark mode toggle), footer (social links, copyright, Lighthouse score badges if enabled)
- [x] Homepage: hero with `~/dav/devs _` typing animation, section teasers per post type (latest 4 each, excluding Jokes)
- [x] Listing pages: `/projects`, `/articles`, `/tools`, `/notebooks`, `/knowledge-sharing`, `/fem`, `/sermons`, `/ebooks`
- [x] Each listing: filter by category, filter by tag, sort (newest/oldest/alpha), cursor-paginated
- [x] Mobile infinite scroll: Intersection Observer + Alpine + `htmx` or Livewire (append next page on scroll)
- [x] Post detail: dynamic Blade layout resolved from `layout.blade_component`, reading progress bar, OG tags, share links, like button, read time badge
- [ ] **Tool posts:** React island via Vite dynamic import by slug. Blade template provides the mount point; Vite loads the correct React component.
- [x] **E-Book posts:** Lemon Squeezy buy button rendered from `ls_store_url` / product ID in post meta. Bundle posts list included volumes.
- [x] **Sermon posts:** YouTube embed (click-to-load) + Markdown content
- [x] **Joke page:** `/funny` — see Phase 10
- [x] **Private posts:** redirect to login if not authenticated
- [x] Full-text search: MySQL FULLTEXT index on `title` + `excerpt` + `content`. Search modal (Cmd/Ctrl+K), results grouped by type, keyboard nav, debounced input.
- [x] Light/dark mode: system default, user-overridable, stored in `localStorage`
- [x] Lighthouse score display: footer badges (performance / accessibility / SEO / best practices), values pulled from `settings` table, shown only if enabled in settings

---

## Phase 13 — Sharing Links

- [x] Per-post share panel (CMS + public): LinkedIn, Facebook, Instagram, Threads
  - LinkedIn: `https://www.linkedin.com/sharing/share-offsite/?url={url}`
  - Facebook: `https://www.facebook.com/sharer/sharer.php?u={url}`
  - Instagram: copy-to-clipboard only (no direct share URL API)
  - Threads: `https://www.threads.net/intent/post?text={title}+{url}`
- [x] One-click copy canonical URL (Alpine clipboard API)
- [x] OG tags, Twitter Card, JSON-LD structured data rendered per post for correct link previews

---

## Phase 14 — AI Features (bonus)

- [ ] AI provider settings in CMS: encrypted API key, model selector (start with OpenAI GPT-4o), abstract via `AiProvider` interface
- [ ] Post editor: "Generate content" — sends title + type + tags → fills excerpt + content draft
- [ ] Post editor: "Audit content" — returns structured suggestions (clarity, SEO, tone, scripture accuracy for faith content)

---

## Phase 15 — Data Export

- [ ] CMS: export all data as ZIP containing:
  - `posts.json` — all posts with meta, tags, categories
  - `images.json` — all image metadata (not the files themselves; Cloudinary URLs included)
  - `jokes.json`
  - `links.json`
  - `youtube_embeds.json`
  - `settings.json`
  - `logs.json` (last 90 days)
- [ ] Export runs as a queued job (async) — CMS shows progress, then download link
- [ ] `export_jobs` table: tracks status (pending / processing / complete / failed), file path, created_at
- [ ] Exported ZIP stored temporarily on Cloudinary or Laravel Cloud disk, auto-deleted after 24h

---

## Phase 16 — Sitemap, SEO & Robots

- [x] `spatie/laravel-sitemap` — generate sitemap.xml for all published posts + listing pages
- [x] Exclude Jokes from sitemap
- [x] Schedule sitemap regeneration daily (Laravel Scheduler)
- [x] `robots.txt`: allow all, point to sitemap, disallow `/admin`
- [x] Canonical URLs on all public pages
- [x] JSON-LD: `Article`, `SoftwareApplication` (Tools), `Book` (E-Books), `VideoObject` (Sermons)

---

## Phase 17 — Logging

- [x] Model observer → `logs` table on every create/update/delete
- [x] HTTP request logging middleware (method, URL, status, duration, ip_hash, user_agent)
- [x] Auth event logging (login, logout, failed login, 2FA success/fail)
- [x] CMS action logging (publish, image upload, settings save, export)
- [ ] AI API call logging (tokens, latency, errors)
- [x] Log metadata: `id`, `channel`, `level`, `message`, `context` (JSON), `ip_hash`, `user_agent`, `url`, `method`, `status_code`, `duration_ms`, `created_at`
- [x] Never store raw IPs — SHA-256(ip + daily_salt) only
- [x] CMS log viewer: filter by channel / level / date range / keyword, cursor-paginated, colour-coded by level
- [x] Scheduled prune: delete logs older than 90 days

---

## Phase 18 — Performance

- [ ] Eager-load all relationships (zero N+1 — enforce with Debugbar in dev)
- [ ] Cache: homepage sections, sitemap, settings (Redis, TTL appropriate per data change frequency)
- [ ] Cloudinary: responsive image URLs (width + format + quality params in Blade component)
- [ ] Vite: dynamic import per Tool React component (only loads on that tool's page)
- [ ] Cursor-based pagination on all public listings
- [ ] HTTP response caching headers for static assets (Cloudflare in front of Laravel Cloud)

---

## Phase 19 — Privacy & Security Hardening

- [x] PDPA privacy policy page
- [x] Cookie consent banner (essential cookies only — no tracking)
- [x] No third-party analytics scripts (Cloudflare Analytics or Umami self-hosted)
- [x] YouTube: `youtube-nocookie.com` + click-to-load (already in Phase 6)
- [x] All external links: `rel="noopener noreferrer"`
- [x] Image uploads: server-side MIME validation, EXIF stripping
- [x] Markdown: sanitise rendered HTML (no XSS via post content) — use CommonMark's allow-list config
- [x] CSRF on all forms, SameSite cookies, HttpOnly session
- [x] CMS IP allowlist middleware (optional, env-configured)
- [x] Regular `composer audit` + `npm audit` in CI

---

## Phase 20 — Headless CMS / GraphQL API (bonus)

- [ ] Toggle in Settings: "Enable public API"
- [ ] REST endpoints (JSON) for read-only public data:
  - `GET /api/posts` (filter by type, status=published, paginated)
  - `GET /api/posts/{slug}`
  - `GET /api/post-types`
  - `GET /api/categories`
  - `GET /api/tags`
- [ ] GraphQL via `rebing/graphql-laravel` or `nuwave/lighthouse`:
  - `Post`, `Category`, `Tag`, `Image`, `YoutubeEmbed` types
  - `posts(type, category, tag, limit)` query
  - `post(slug)` query
- [ ] API authentication: optional Bearer token for private post access
- [ ] Rate limiting on all API routes (60 req/min per IP)
- [ ] API docs auto-generated (GraphQL introspection; REST via Scribe or similar)

---

## Phase 21 — Laravel Cloud & Deployment

- [ ] Staging and production environments on Laravel Cloud
- [ ] All secrets in Cloud environment config (never in `.env` committed to git)
- [ ] Scheduled jobs: sitemap regeneration, log pruning, export cleanup
- [ ] Queue worker for: AI calls, export jobs, sitemap generation
- [ ] Health check endpoint: `GET /up`
- [ ] Zero-downtime deploys (Cloud rolling deploy)
- [ ] Database backups: Cloud managed + weekly export to Cloudinary or R2

---

## Phase 22 — Content Migration

See the separate **Migration Strategy** document.

- [ ] Audit Next.js content files, map to post types
- [ ] Normalise frontmatter across all MDX files
- [ ] Batch-upload images to Cloudinary, save mapping JSON
- [ ] Write `php artisan import:images`, `import:posts`, `import:jokes`, `import:youtube-embeds`
- [ ] Handle MDX JSX components (strip or convert)
- [ ] Validate: counts, slugs, images, OG tags, canonical URLs
- [ ] Content freeze on Next.js, final import run, DNS cutover

---

## Recommended sequence

```
0 → 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9 → 10 → 11 → 12 → 13 → 16 → 17 → 21
```
Ship v1 here (complete CMS + public site). Then:
```
14 (AI) → 15 (Export) → 18 (Perf) → 19 (Hardening) → 20 (Headless) → 22 (Migration)
```