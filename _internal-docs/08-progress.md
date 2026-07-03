# Dav/Devs Laravel CMS — Implementation Progress

> Last updated: 2026-07-01

---

## Phase 0 — Project Setup ✅

- [x] Packages installed (composer): spatie/laravel-permission, laravel-sitemap, laravel-sluggable, laravel-data, pragmarx/google2fa-laravel, bacon/bacon-qr-code, league/commonmark, cloudinary/cloudinary_php
- [x] Dev packages: Pest, Pint, Larastan, Debugbar
- [x] npm: alpinejs, @alpinejs/intersect
- [x] DB: .env switched to MySQL (`davdevs` database)
- [x] Vite: cms.css + cms.js entry points added
- [x] Security headers middleware (`SecurityHeaders`)
- [ ] Session timeout (2h inactivity) — TODO
- [ ] Global rate limiting middleware — TODO
- [ ] GitHub Actions CI — TODO
- [ ] Laravel Cloud setup — deferred (deployment phase)

## Phase 1 — Database Schema & Core Models ✅

- [x] All migrations created and run successfully (30 migrations)
- [x] Tables: users (2FA fields), settings, images, links, video_embeds, layouts, content_types, categories, tags, entries, entry_meta, entry_images, entry_video_embeds, entry_links, entry_tags, publications, publication_store, publication_bundles, publication_meta, publication_images, publication_links, publication_tags, quips, reactions, redirects, activity_log, export_jobs
- [x] Eloquent models created with relationships, casts, accessors
- [x] Entry.saving() observer: auto-calculates read_time
- [x] Entry.creating() + Publication.creating(): auto-generates ULID
- [x] PublishedScope: scopePublished() on Entry and Publication
- [x] Seeders: ContentTypeSeeder (8 types), LayoutSeeder (5 layouts), SettingSeeder (14 settings)
- [ ] Model observers for activity_log — TODO (Phase 17)

## Phase 2 — Authentication & 2FA ✅

- [x] Laravel Breeze (Blade) scaffolded
- [x] Registration disabled (403 on all register routes)
- [x] TOTP 2FA via pragmarx/google2fa-laravel:
  - [x] TwoFactorController: setup, storeSetup, challenge, verify, recovery, verifyRecovery, disable
  - [x] TwoFactorMiddleware: routes protected, forces setup if not configured
  - [x] 2FA views: setup, challenge, save-codes, recovery
  - [x] Recovery codes: 8 single-use codes, stored hashed
  - [x] Disable flow (requires password re-confirmation)
- [ ] Session timeout (2h) — TODO
- [x] Routes: auth + two_factor middleware on all /panel/* routes

## Phase 3 — CMS Shell & Settings ✅

- [x] CMS layout (layouts/cms.blade.php): amber topbar, collapsible sidebar, dark/light toggle
- [x] CMS CSS (resources/css/cms.css): full design token system from spec
- [x] CMS JS (resources/js/cms.js): Alpine.js, theme persistence
- [x] Dashboard view: stat cards, quick actions, recent logs
- [x] DashboardController: entry counts, recent logs
- [x] Settings page: grouped by area (display, header, footer, lighthouse, api)
- [x] SettingController: index, update (handles bool/int/string casts), flushCache
- [x] Cache flush button

## Phase 4 — Image Manager ✅

- [x] CloudinaryService: upload, destroy, transformedUrl wrapper around cloudinary/cloudinary_php SDK
- [x] ImageController: index (search + type filter, cursor-paginated), create, store, edit, update, destroy
- [x] Usage tracker: shows "used in N posts" with links, blocks delete if in use
- [x] Grid browser with lightbox (Alpine modal, click to open/close on Escape)
- [x] QR code filter toggle
- [x] Replace image file on edit (re-uploads to Cloudinary, same DB record)

## Phase 5 — Link Manager ✅

- [x] LinkController: full CRUD + reorder endpoint (for future drag-to-reorder JS)
- [x] Auto-sets rel="noopener noreferrer" when target=_blank
- [x] Category field (general/social/nav) for footer/nav filtering
- [x] Searchable list view

## Phase 6 — YouTube Embed Manager ✅

- [x] VideoEmbedController: add by ID or full URL (regex extraction)
- [x] Auto-fetch title/channel/thumbnail via YouTube oEmbed API
- [x] Duplicate detection (video_id unique)
- [x] Browse/search/edit/delete views

## Phase 7 — Layout Manager ✅

- [x] LayoutController: CRUD, validates blade_component exists via view()->exists() before save
- [x] Blocks delete if layout is in use by entries/publications
- [x] Placeholder Blade components created for all 5 seeded layouts (standard, product, tool, sermon, publication) — real markup comes in Phase 12

## Phase 8 — Content Type, Category & Tag Managers ✅

- [x] ContentTypeController: CRUD, react_island + listed flags, blocks delete if in use
- [x] CategoryController: CRUD, scoped by content_type_id (nullable = cross-type), scope enum
- [x] TagController: CRUD, same scoping pattern as categories
- [x] All list views show content type name / scope, block delete if referenced

## Routes registered

97 routes total, all /panel/* behind auth + two_factor middleware.

## Phase 9 — Entry/Post Manager ✅

- [x] EntryController: filters (type/status/category/featured/date), full-text search, sort, cursor pagination, bulk actions
- [x] Tabbed edit form (Content/Media & Links/Meta/Settings): markdown editor w/ preview, image/video/link linkers, tag multi-select, meta key/value editor
- [x] Duplicate action (deep-copies meta/images/videos/links/tags)
- [x] Auto slug (YYYYMMDD-slug, collision-safe)
- [x] PublicationController + form: same pattern as Entry, plus Store tab (Lemon Squeezy fields) and bundle member picker

## Phase 10 — Joke (Quip) System ✅

- [x] QuipController: CRUD, variant filter, bulk activate/deactivate
- [x] Form: Q/A vs Statement variant toggle (Alpine-driven conditional fields)
- [x] List view shows question+punchline preview, active/inactive chip

## Phase 11 — Anonymous Like System ✅

- [x] ReactionController (Api namespace): toggle endpoint, UUID cookie
      (1yr, HttpOnly, SameSite=Strict, signed via cookie() helper),
      SHA-256 token_hash + SHA-256(ip+daily salt) ip_hash
- [x] Rate limited 5 req/min via throttle:5,1 middleware
- [x] Morph map registered in AppServiceProvider (entry/publication →
      short enum-safe names, matches reactionable_type column)
- [x] Idempotent toggle (like/unlike), returns updated count as JSON

## Phase 12 — Frontend public site ✅

- [x] SiteController: home, listing (dynamic per content type), show
      (entry detail), ebooks index/show, funny, randomQuip API, search API
- [x] Global site layout (layouts/site.blade.php via SiteLayout
      component): nav with dynamic content-type links, Cmd/Ctrl+K search
      modal, dark/light toggle (localStorage), footer with social links
      + Lighthouse badges, reading progress bar
- [x] Homepage: hero with typing-cursor wordmark, per-content-type
      section teasers (latest 4, excluding unlisted types)
- [x] Listing pages: dynamic route `{typeSlug}` resolves via
      content_types.slug, category + sort filters, cursor pagination
- [x] Mobile infinite scroll: Alpine + @alpinejs/intersect, fetches
      JSON from same listing endpoint (Accept header branch in
      controller), desktop keeps classic pagination links
- [x] Post detail: dynamic route `{typeSlug}/{slug}`, tags, embedded
      images/videos (click-to-load youtube-nocookie), like button
      (calls reaction API), share links, reading progress bar via
      scroll listener
- [x] E-book listing + detail: cover image, Lemon Squeezy buy button,
      bundle member list
- [x] Joke page `/funny`: Q/A variant with 30s countdown + skip button,
      statement variant shown directly, refresh fetches a different
      quip (excludes current ID), disclaimer text
- [x] Private entries redirect to login if unauthenticated
- [x] Draft/archived entries require a valid signed URL (404 otherwise)
- [x] Full-text search: MySQL FULLTEXT via api/search endpoint,
      Cmd/Ctrl+K modal, debounced input, grouped by type
- [x] Settings-driven brand name, footer copyright, Lighthouse badge
      visibility (cached 1hr, flushed from CMS settings page)
- Verified end-to-end via local dev server: home, listing, entry
  detail, ebooks, funny, search API, quip API all return 200 with
  real seeded data

## Phase 13 — Sharing Links ✅

- [x] Share row on entry/publication detail (LinkedIn, Facebook, Threads, copy-link) — Instagram omitted per spec (no direct share URL API)
- [x] Canonical URL, OG tags, Twitter Card meta rendered per page via SiteLayout component (`title`/`description`/`ogImage`/`jsonLd` props)
- [x] JSON-LD structured data: `Article` (default), `SoftwareApplication` (Tool), `VideoObject` (Sermon), `Book` (E-Book with Offer)

## Phase 16 — Sitemap, SEO & Robots ✅

- [x] `sitemap:generate` Artisan command using spatie/laravel-sitemap:
      home, ebooks index, per-content-type listing pages, all published
      public entries + publications with lastmod/priority
- [x] Quips/jokes excluded (not part of entries/publications tables)
- [x] Scheduled daily via `withSchedule()` in bootstrap/app.php
- [x] `public/robots.txt`: disallows /panel, /login, /2fa, /api; points to /sitemap.xml
- [x] Canonical URLs on all public pages (see Phase 13)

## Phase 17 — Logging ✅

- [x] `LogsActivity` trait (created/updated/deleted → activity_log)
      attached to Entry, Publication, Image, Link, VideoEmbed, Layout,
      ContentType, Category, Tag, Quip
- [x] `LogHttpRequests` middleware: method/URL/status/duration/ip_hash/
      user_agent on every request (skips debugbar/build asset noise)
- [x] Auth event logging: `LogAuthEvents` listener on Login/Logout/Failed
      events, plus explicit 2FA verify success/fail logs in
      TwoFactorController
- [x] CMS action logging: settings save logs explicitly; publish/
      upload/etc. captured generically via the model observer trail
- [x] Log metadata matches schema: channel, level, message, context
      (JSON), ip_hash (SHA-256, never raw IP), user_agent, url, method,
      status_code, duration_ms
- [x] CMS log viewer (`panel.logs.index`): filter by channel/level/
      date range/keyword, cursor-paginated, colour-coded by level
- [x] Scheduled prune: `logs:prune` deletes entries older than 90 days
      (daily), `exports:prune` cleans expired export files (hourly)

Verified via tinker: creating an Entry writes exactly one activity_log
row with the correct message. Sitemap command runs clean, generates
valid XML with home/listing/entry/ebook URLs.

## Phase 19 — Privacy & Security Hardening ✅

- [x] `site.privacy` route + PDPA-aligned privacy policy page (what's
      collected: reaction cookie, hashed IPs, standard logs; what
      isn't: no third-party analytics, no ad cookies, no data selling)
- [x] Cookie consent banner (Alpine + localStorage dismiss), essential-
      cookies-only messaging, links to privacy page
- [x] No third-party analytics scripts anywhere in the codebase
- [x] YouTube click-to-load via youtube-nocookie.com (already Phase 6/12)
- [x] All external links `rel="noopener noreferrer"` — enforced at the
      model level (Link::target === '_blank' auto-sets rel)
- [x] Image uploads: server-side MIME validation (`mimes:jpg,jpeg,png,
      webp,gif` — Laravel checks actual file content, not just
      extension) + Cloudinary `image_metadata: false` strips EXIF on
      upload
- [x] Markdown → HTML: `MarkdownService` wraps league/commonmark with
      `html_input: strip` (raw HTML in post bodies is stripped, not
      rendered — no stored XSS via post content) and
      `allow_unsafe_links: false`; wired via a new `@markdown` Blade
      directive, used on entry-detail and publication-detail
- [x] CSRF on all forms (Laravel default), SameSite=Strict + HttpOnly
      session cookies (`SESSION_SAME_SITE=strict` in .env)
- [x] Session timeout: `SESSION_LIFETIME=120` (2h inactivity, Laravel
      default behaviour)
- [x] `CmsIpAllowlist` middleware (`cms.ip` alias), applied to all
      `/panel/*` routes, no-ops when `CMS_IP_ALLOWLIST` env is empty
- [x] `composer audit` and `npm audit`: both clean, zero advisories
- [x] GitHub Actions CI (`.github/workflows/ci.yml`): Pint lint job +
      Pest test job against a real MySQL 8 service container

## Test suite fix

Enabled `RefreshDatabase` in `tests/Pest.php` (was commented out —
stock Breeze scaffold). This surfaced that MySQL `fullText()` indexes
aren't supported by SQLite (used for the in-memory test DB); both
migrations that declare fulltext indexes (entries, publications) now
guard the call behind a `supportsFullText()` driver check so tests run
against SQLite while production (MySQL) still gets the real index.
Confirmed via `migrate:fresh` against the real MySQL database that the
fulltext index is still created there. Full Pint pass applied
project-wide (many pre-existing style violations across models/
controllers, none behavioural).

## Phase 18 — Performance ✅

- [x] Eager-loading audited across all controllers (Entry/Publication
      index + form, SiteController home/listing/show) — no N+1 in any
      view that iterates relations
- [x] Cache: settings (1hr TTL, flushed explicitly on save) shared by
      SiteLayout, SiteController, and EnsurePublicApiEnabled via the
      same 'settings' cache key/shape
- [x] Cloudinary responsive image URLs: `Image::responsiveUrl($width)`
      injects `w_{n},q_auto,f_auto` into the delivery URL path; wired
      into ebook cards, publication detail, and entry image galleries
- [x] Cursor-based pagination on all public listings and CMS list views
- [ ] Tool posts React island (Vite dynamic import) — deferred; no
      React toolchain exists in this project yet, would need its own
      package.json entry point and is out of scope for this pass
- [ ] Cloudflare response-caching headers — infrastructure-level,
      configured at the CDN/deploy layer (Phase 21), not the app layer

## Phase 20 — Headless CMS / GraphQL API (bonus) ✅ (REST only)

- [x] Settings toggle "Enable public API" (`api_enabled`, already
      seeded in Phase 3) gates every route below via a dedicated
      `EnsurePublicApiEnabled` middleware (404 when disabled)
- [x] REST endpoints: `GET /api/posts` (filter by type, published-only,
      paginated), `GET /api/posts/{slug}`, `GET /api/post-types`,
      `GET /api/categories`, `GET /api/tags`
- [x] Rate limited 60 req/min via throttle middleware
- [ ] GraphQL — not implemented. REST covers the same data surface;
      adding `rebing/graphql-laravel` or `nuwave/lighthouse` is a
      genuinely separate package integration (schema files, resolver
      wiring) that wasn't justified given REST already satisfies the
      "headless" requirement. Flagged here rather than silently
      skipped — pull in one of those packages if GraphQL is a hard
      requirement.
- [ ] Bearer token auth for private post access — not implemented;
      current REST API only exposes published/public content, which
      sidesteps the need until private content is actually requested

Verified end-to-end: toggling `api_enabled` via direct DB update (then
flushing the settings cache, matching what the CMS Settings controller
does automatically) correctly flips all `/api/*` routes between 404
and 200.

## Phase 15 — Data Export (bonus) ✅

- [x] `BuildDataExport` queued job: builds a ZIP with entries.json
      (+ meta/tags/category/contentType), publications.json (+ meta/
      tags/store), images.json, links.json, youtube_embeds.json,
      quips.json, settings.json, logs.json (last 90 days only)
- [x] `ExportController`: index (last 20 jobs), store (creates
      `ExportJob` row, dispatches the job), download (protected —
      requires CMS auth, checks status=complete, checks not expired,
      checks file still exists on disk)
- [x] Download served through a protected `/panel/exports/{id}/download`
      route rather than a public storage URL — `download_url` on the
      model stores a storage-relative path, never a public link, so
      export contents (which include full post bodies + settings)
      can't leak via a guessable/public URL
- [x] `exports:prune` (already scheduled hourly in Phase 17) deletes
      the on-disk ZIP + row once `expires_at` (24h) has passed
- [x] CMS nav: "Export" added under System group

Verified via tinker: `BuildDataExport::handle()` produces a valid ZIP
on disk, `ExportJob` status transitions queued → processing → complete
correctly, and file existence is confirmed via `Storage::exists()`.

## Next up

- Phase 14: AI Features (bonus) — not implemented; requires choosing
  and wiring a specific LLM provider SDK, out of scope without a
  concrete API key/provider decision from the user
- Phase 21: Laravel Cloud & Deployment config
- Phase 22: Content Migration (requires local access to the Next.js
  source project — out of scope for this environment, documented as
  a manual follow-up)
