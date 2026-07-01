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

## Next up

- Phase 9: Entry/Post Manager (the big one — markdown editor, image/video/link linkers, meta editor, type-specific fields)
- Phase 10: Joke (Quip) System
- Phase 11: Anonymous Like System
- Phase 12: Frontend public site
- Phase 13: Sharing links
