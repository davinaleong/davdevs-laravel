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

## Phase 3 — CMS Shell & Settings (partial)

- [x] CMS layout (layouts/cms.blade.php): amber topbar, collapsible sidebar, dark/light toggle
- [x] CMS CSS (resources/css/cms.css): full design token system from spec
- [x] CMS JS (resources/js/cms.js): Alpine.js, theme persistence
- [x] Dashboard view: stat cards, quick actions, recent logs
- [x] DashboardController: entry counts, recent logs
- [ ] Settings page — TODO
- [ ] Settings controller — TODO
- [ ] Cache flush — TODO

## Routes registered

- GET  /                      → site.home
- GET  /login                 → auth login
- GET  /2fa/setup             → 2FA setup (auth)
- GET  /2fa/challenge         → 2FA verify (auth)
- GET  /panel                 → CMS dashboard (auth + 2fa)
- GET  /panel/entries         → entry list
- ... (96 routes total)

## Next up

- Phase 3 remainder: Settings controller + view
- Phase 4: Image Manager (CRUD + Cloudinary + lightbox)
- Phase 5: Link Manager
- Phase 6: Video Embed Manager
- Phase 7: Layout Manager
- Phase 8: Category + Tag Managers
- Phase 9: Entry/Post Manager (the big one)
