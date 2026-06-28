# Dav/Devs — Database Schemas v2

> **What changed from v1:**
> - `posts` renamed and split into `entries` (free) and `publications` (paid)
> - Shared base columns extracted into a clear pattern so migration between the two is a copy-row operation
> - `jokes` table renamed to `quips`
> - `post_meta` renamed to `entry_meta` / `publication_meta`
> - All pivot tables renamed to match new entity names
> - `post_types` renamed to `content_types`
> - Better column naming throughout (no more `is_*` boolean soup)
> - Migration path documented at the bottom

---

## Naming conventions

| Pattern | Example | Means |
|---|---|---|
| `snake_case` | `published_at` | Standard |
| `*_at` suffix | `published_at`, `deleted_at` | Timestamp |
| `*_id` suffix | `content_type_id` | Foreign key |
| `status` enum | `draft/private/published/archived` | State machine |
| No `is_*` booleans where an enum fits better | `visibility` not `is_private` | Clearer intent |
| Boolean only for true binary flags | `featured`, `active`, `qr_code` | No ambiguity |

---

## Core lookup tables

### `content_types`
Defines the shape and behaviour of each content type.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `name` | VARCHAR(100) | `Article`, `Project`, `Tool`, `Notebook`, `Knowledge Sharing`, `Frontend Mentor`, `Sermon`, `Page` |
| `slug` | VARCHAR(100) UNIQUE | `article`, `project`, `tool`, `notebook`, `knowledge-sharing`, `fem`, `sermon`, `page` |
| `table_target` | ENUM('entries','publications') DEFAULT 'entries' | Which table this type lives in — key for migration |
| `react_island` | BOOLEAN DEFAULT FALSE | Loads a React bundle on the detail page (Tools) |
| `listed` | BOOLEAN DEFAULT TRUE | FALSE = excluded from homepage, sitemaps, listing pages (e.g. a helper Page type) |
| `description` | TEXT NULL | Internal note |

**Seed data:**

| name | slug | table_target | react_island | listed |
|---|---|---|---|---|
| Article | article | entries | false | true |
| Project | project | entries | false | true |
| Tool | tool | entries | true | true |
| Notebook | notebook | entries | false | true |
| Knowledge Sharing | knowledge-sharing | entries | false | true |
| Frontend Mentor | fem | entries | false | true |
| Sermon | sermon | entries | false | true |
| Page | page | entries | false | false |

> E-Book lives in `publications`, not `content_types`. It has its own type column.

---

### `categories`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `content_type_id` | BIGINT UNSIGNED FK → `content_types.id` NULL | NULL = cross-type category |
| `scope` | ENUM('entries','publications','all') DEFAULT 'entries' | Determines which pickers show this category |
| `name` | VARCHAR(100) | |
| `slug` | VARCHAR(100) | |
| `description` | TEXT NULL | |
| `active` | BOOLEAN DEFAULT TRUE | |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** (`content_type_id`, `slug`) UNIQUE WHERE `content_type_id` IS NOT NULL

---

### `tags`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `content_type_id` | BIGINT UNSIGNED FK → `content_types.id` NULL | NULL = cross-type tag |
| `scope` | ENUM('entries','publications','all') DEFAULT 'entries' | |
| `name` | VARCHAR(100) | |
| `slug` | VARCHAR(100) | |

**Indexes:** (`content_type_id`, `slug`) UNIQUE WHERE `content_type_id` IS NOT NULL

---

### `layouts`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `name` | VARCHAR(100) | `Standard`, `Product`, `Tool`, `Sermon`, `Publication` |
| `slug` | VARCHAR(100) UNIQUE | |
| `blade_component` | VARCHAR(200) | e.g. `layouts.standard` — validated on save |
| `description` | TEXT NULL | |
| `preview_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | |
| `active` | BOOLEAN DEFAULT TRUE | |

---

## Media tables

### `images`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `cloudinary_id` | VARCHAR(500) UNIQUE | Cloudinary `public_id` |
| `url` | TEXT | `secure_url` |
| `title` | VARCHAR(500) NULL | |
| `alt` | VARCHAR(500) NULL | Required for a11y |
| `caption` | TEXT NULL | |
| `credit` | VARCHAR(300) NULL | Photographer / source |
| `width` | SMALLINT UNSIGNED NULL | px |
| `height` | SMALLINT UNSIGNED NULL | px |
| `format` | VARCHAR(20) NULL | `jpg`, `png`, `webp`, `gif` |
| `bytes` | INT UNSIGNED NULL | |
| `qr_code` | BOOLEAN DEFAULT FALSE | Shown in QR filter in image browser |
| `deleted_at` | TIMESTAMP NULL | |

---

### `links`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `label` | VARCHAR(255) | Display text |
| `url` | TEXT | |
| `target` | ENUM('_self','_blank') DEFAULT '_self' | |
| `rel` | VARCHAR(100) DEFAULT 'noopener noreferrer' | Auto-set for `_blank` |
| `description` | TEXT NULL | Internal note |
| `category` | ENUM('general','social','nav') DEFAULT 'general' | |
| `icon_class` | VARCHAR(100) NULL | Tabler icon class |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | |
| `active` | BOOLEAN DEFAULT TRUE | |

---

### `video_embeds`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `video_id` | VARCHAR(20) UNIQUE | YouTube video ID |
| `title` | VARCHAR(500) | From oEmbed |
| `description` | TEXT NULL | |
| `channel_name` | VARCHAR(255) NULL | |
| `thumbnail_url` | TEXT NULL | |
| `duration_seconds` | SMALLINT UNSIGNED NULL | |
| `published_at` | DATE NULL | YouTube publish date |

---

## Free content — `entries`

All content types in `content_types` where `table_target = 'entries'`.

### `entries`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `ulid` | CHAR(26) UNIQUE | Public-safe ID for preview tokens |
| `content_type_id` | BIGINT UNSIGNED FK → `content_types.id` | |
| `layout_id` | BIGINT UNSIGNED FK → `layouts.id` | |
| `category_id` | BIGINT UNSIGNED NULL FK → `categories.id` | |
| `og_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | |
| `title` | VARCHAR(500) | |
| `slug` | VARCHAR(500) UNIQUE | `YYYYMMDD-slug` convention |
| `excerpt` | TEXT NULL | |
| `body` | LONGTEXT NULL | Raw Markdown |
| `read_time` | TINYINT UNSIGNED DEFAULT 1 | Minutes. Computed on save: `ceil(word_count / 200)` |
| `status` | ENUM('draft','private','published','archived') DEFAULT 'draft' | |
| `visibility` | ENUM('public','unlisted') DEFAULT 'public' | `unlisted` = accessible by direct URL only, not listed anywhere |
| `featured` | BOOLEAN DEFAULT FALSE | |
| `published_at` | TIMESTAMP NULL | Future = scheduled |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** `slug` (unique), `status`, `published_at`, `content_type_id`, `featured`
**Fulltext:** (`title`, `excerpt`, `body`)

---

### `entry_meta`

Extensible key/value store for free content. Add new metadata types by convention — no migration required.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `entry_id` | BIGINT UNSIGNED FK → `entries.id` | |
| `key` | VARCHAR(191) | See key conventions below |
| `value` | TEXT NULL | |

**Index:** (`entry_id`, `key`) UNIQUE

**Key conventions for `entries`:**

| Key | Types | Example |
|---|---|---|
| `github_repo_url` | Project, Tool | `https://github.com/...` |
| `live_demo_url` | Project, Tool | `https://...` |
| `fem_difficulty` | Frontend Mentor | `junior` / `intermediate` / `advanced` |
| `fem_challenge_url` | Frontend Mentor | `https://frontendmentor.io/...` |
| `notebook_colab_url` | Notebook | `https://colab.research.google.com/...` |
| `sermon_series` | Sermon | `Grace Revolution` |
| `sermon_speaker` | Sermon | `Pastor Joseph Prince` |
| `lighthouse_performance` | Any | `98` |
| `lighthouse_accessibility` | Any | `100` |
| `lighthouse_seo` | Any | `100` |
| `lighthouse_best_practices` | Any | `96` |
| `lighthouse_report_mobile` | Any | URL |
| `lighthouse_report_desktop` | Any | URL |

---

### Pivot tables for `entries`

#### `entry_images`
| Column | Type |
|---|---|
| `id` | BIGINT UNSIGNED PK |
| `entry_id` | BIGINT UNSIGNED FK → `entries.id` |
| `image_id` | BIGINT UNSIGNED FK → `images.id` |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 |
| `caption_override` | TEXT NULL |

**Index:** (`entry_id`, `image_id`) UNIQUE

#### `entry_video_embeds`
| Column | Type |
|---|---|
| `id` | BIGINT UNSIGNED PK |
| `entry_id` | BIGINT UNSIGNED FK → `entries.id` |
| `video_embed_id` | BIGINT UNSIGNED FK → `video_embeds.id` |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 |

**Index:** (`entry_id`, `video_embed_id`) UNIQUE

#### `entry_links`
| Column | Type |
|---|---|
| `id` | BIGINT UNSIGNED PK |
| `entry_id` | BIGINT UNSIGNED FK → `entries.id` |
| `link_id` | BIGINT UNSIGNED FK → `links.id` |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 |

#### `entry_tags`
| Column | Type |
|---|---|
| `entry_id` | BIGINT UNSIGNED FK → `entries.id` (composite PK) |
| `tag_id` | BIGINT UNSIGNED FK → `tags.id` (composite PK) |

---

## Paid content — `publications`

Handles e-books. Designed so a future paid content type (paid course, premium article, etc.) can be added with minimal schema change.

### `publications`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `ulid` | CHAR(26) UNIQUE | |
| `layout_id` | BIGINT UNSIGNED FK → `layouts.id` | |
| `category_id` | BIGINT UNSIGNED NULL FK → `categories.id` | |
| `og_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | |
| `cover_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | Distinct from OG image — the book cover |
| `publication_type` | ENUM('ebook') DEFAULT 'ebook' | Extend enum when new paid types added |
| `title` | VARCHAR(500) | |
| `slug` | VARCHAR(500) UNIQUE | |
| `tagline` | VARCHAR(500) NULL | Short sell line under the title |
| `excerpt` | TEXT NULL | Longer description for the listing card |
| `body` | LONGTEXT NULL | Markdown — description / sample chapter |
| `status` | ENUM('draft','private','published','archived') DEFAULT 'draft' | |
| `featured` | BOOLEAN DEFAULT FALSE | |
| `bundle` | BOOLEAN DEFAULT FALSE | TRUE = this publication groups others |
| `published_at` | TIMESTAMP NULL | |
| `deleted_at` | TIMESTAMP NULL | |

**Note:** No `read_time` — publications are sold, not read on-site. No `content_type_id` — `publication_type` serves that role.

**Indexes:** `slug` (unique), `status`, `publication_type`, `featured`, `bundle`
**Fulltext:** (`title`, `tagline`, `excerpt`)

---

### `publication_store`

All commerce/storefront data isolated in a dedicated table. One-to-one with `publications`.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `publication_id` | BIGINT UNSIGNED UNIQUE FK → `publications.id` | |
| `ls_product_id` | VARCHAR(100) NULL | Lemon Squeezy product UUID |
| `ls_variant_id` | VARCHAR(100) NULL | Lemon Squeezy variant UUID |
| `ls_store_url` | TEXT NULL | Full checkout URL |
| `price_display` | VARCHAR(50) NULL | e.g. `SGD 9.90` — display only, not used for logic |
| `currency` | CHAR(3) DEFAULT 'SGD' | ISO 4217 |
| `free_sample_url` | TEXT NULL | Link to a free chapter / preview PDF |

> Why separate? Store data changes independently of editorial data. Rotating a LS product ID doesn't touch the `publications` row. Easier to null out or replace commerce details without touching title/body/status.

---

### `publication_bundles`

Links a bundle publication to its member publications.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `bundle_id` | BIGINT UNSIGNED FK → `publications.id` | The bundle (must have `bundle = TRUE`) |
| `member_id` | BIGINT UNSIGNED FK → `publications.id` | A member publication |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | Display order inside the bundle |

**Index:** (`bundle_id`, `member_id`) UNIQUE

---

### `publication_meta`

Same extensible key/value pattern as `entry_meta`.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `publication_id` | BIGINT UNSIGNED FK → `publications.id` | |
| `key` | VARCHAR(191) | |
| `value` | TEXT NULL | |

**Index:** (`publication_id`, `key`) UNIQUE

**Key conventions for `publications`:**

| Key | Notes |
|---|---|
| `page_count` | `184` |
| `word_count` | `42000` |
| `language` | `English` |
| `isbn` | If registered |
| `edition` | `1st`, `2nd` |
| `publisher` | `GraceSoft` / self |
| `goodreads_url` | Optional |
| `launch_date_display` | e.g. `December 2024` — formatted for display |

---

### Pivot tables for `publications`

#### `publication_images`
| Column | Type |
|---|---|
| `id` | BIGINT UNSIGNED PK |
| `publication_id` | BIGINT UNSIGNED FK → `publications.id` |
| `image_id` | BIGINT UNSIGNED FK → `images.id` |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 |
| `caption_override` | TEXT NULL |

#### `publication_links`
| Column | Type |
|---|---|
| `id` | BIGINT UNSIGNED PK |
| `publication_id` | BIGINT UNSIGNED FK → `publications.id` |
| `link_id` | BIGINT UNSIGNED FK → `links.id` |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 |

#### `publication_tags`
| Column | Type |
|---|---|
| `publication_id` | BIGINT UNSIGNED FK → `publications.id` (composite PK) |
| `tag_id` | BIGINT UNSIGNED FK → `tags.id` (composite PK) |

---

## Interaction tables

### `quips`

Jokes. Separate from both `entries` and `publications` — different schema, different access pattern (random fetch, never by slug, never listed).

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `variant` | ENUM('qa','statement') | |
| `question` | TEXT NULL | Q/A variant only |
| `punchline` | TEXT | The answer or the statement |
| `active` | BOOLEAN DEFAULT TRUE | Inactive quips never served |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** `variant`, `active`

---

### `reactions`

Anonymous like/unlike system. Works across both `entries` and `publications` via a polymorphic pattern.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `reactionable_type` | ENUM('entry','publication') | Polymorphic type |
| `reactionable_id` | BIGINT UNSIGNED | FK to `entries.id` or `publications.id` |
| `token_hash` | CHAR(64) | SHA-256 of visitor UUID cookie |
| `ip_hash` | CHAR(64) | SHA-256(ip + daily_salt) |
| `created_at` | TIMESTAMP | Immutable — no `updated_at` |

**Indexes:** (`reactionable_type`, `reactionable_id`, `token_hash`) UNIQUE

> Polymorphic over separate `entry_reactions` / `publication_reactions` tables because the logic is identical and you'd never query them separately. One query gets total reactions for any content.

---

### `redirects`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `from_path` | VARCHAR(500) UNIQUE | e.g. `/old-path/slug` |
| `to_path` | VARCHAR(500) | e.g. `/new-path/slug` |
| `status_code` | SMALLINT UNSIGNED DEFAULT 301 | |
| `active` | BOOLEAN DEFAULT TRUE | |

---

## System tables

### `settings`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `key` | VARCHAR(191) UNIQUE | e.g. `frontend_date_format` |
| `value` | TEXT NULL | |
| `cast` | VARCHAR(50) DEFAULT 'string' | `string`, `boolean`, `integer`, `json` |
| `group` | VARCHAR(100) | `display`, `header`, `footer`, `lighthouse`, `api` |

---

### `activity_log`

All model events, HTTP requests, auth events, CMS actions, AI calls.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `channel` | VARCHAR(50) | `http`, `auth`, `cms`, `model`, `ai`, `export` |
| `level` | VARCHAR(20) | `debug`, `info`, `warning`, `error`, `critical` |
| `message` | TEXT | |
| `context` | JSON NULL | Structured data |
| `ip_hash` | CHAR(64) NULL | SHA-256(ip + daily_salt) — never raw IP |
| `user_agent` | TEXT NULL | |
| `url` | VARCHAR(2000) NULL | |
| `method` | VARCHAR(10) NULL | |
| `status_code` | SMALLINT UNSIGNED NULL | |
| `duration_ms` | SMALLINT UNSIGNED NULL | |
| `created_at` | TIMESTAMP | Immutable |

**Indexes:** `channel`, `level`, `created_at`

---

### `export_jobs`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `status` | ENUM('queued','processing','complete','failed') DEFAULT 'queued' | |
| `download_url` | TEXT NULL | Signed URL, set on completion |
| `expires_at` | TIMESTAMP NULL | Auto-delete after 24h |
| `error_message` | TEXT NULL | |

---

### `users`

Extends Laravel default with 2FA fields.

| Column | Type | Notes |
|---|---|---|
| `totp_secret` | TEXT NULL | Encrypted TOTP secret |
| `totp_enabled` | BOOLEAN DEFAULT FALSE | |
| `totp_confirmed_at` | TIMESTAMP NULL | |
| `recovery_codes` | JSON NULL | Hashed single-use codes |

---

## Migration path: entries → publications

When a free content type (e.g. a premium article series) needs to become paid:

### Step 1 — Create the publication row
```sql
INSERT INTO publications (
  ulid, layout_id, category_id, og_image_id,
  publication_type, title, slug, tagline,
  excerpt, body, status, featured, published_at
)
SELECT
  ulid, layout_id, category_id, og_image_id,
  'ebook',          -- set publication_type
  title, slug, NULL,
  excerpt, body, status, featured, published_at
FROM entries
WHERE id = :entry_id;
```

### Step 2 — Copy meta
```sql
INSERT INTO publication_meta (publication_id, key, value)
SELECT :new_publication_id, key, value
FROM entry_meta
WHERE entry_id = :entry_id;
```

### Step 3 — Copy pivots
```sql
-- images
INSERT INTO publication_images (publication_id, image_id, sort_order, caption_override)
SELECT :new_publication_id, image_id, sort_order, caption_override
FROM entry_images WHERE entry_id = :entry_id;

-- tags
INSERT INTO publication_tags (publication_id, tag_id)
SELECT :new_publication_id, tag_id
FROM entry_tags WHERE entry_id = :entry_id;

-- links
INSERT INTO publication_links (publication_id, link_id, sort_order)
SELECT :new_publication_id, link_id, sort_order
FROM entry_links WHERE entry_id = :entry_id;
```

### Step 4 — Add store record
```sql
INSERT INTO publication_store (publication_id, ls_product_id, ls_variant_id, ls_store_url, currency)
VALUES (:new_publication_id, :ls_product_id, :ls_variant_id, :ls_store_url, 'SGD');
```

### Step 5 — Copy reactions
```sql
INSERT INTO reactions (reactionable_type, reactionable_id, token_hash, ip_hash, created_at)
SELECT 'publication', :new_publication_id, token_hash, ip_hash, created_at
FROM reactions
WHERE reactionable_type = 'entry' AND reactionable_id = :entry_id;
```

### Step 6 — Add redirect
```sql
INSERT INTO redirects (from_path, to_path, status_code)
VALUES ('/articles/old-slug', '/ebooks/new-slug', 301);
```

### Step 7 — Archive the original entry
```sql
UPDATE entries SET status = 'archived' WHERE id = :entry_id;
```

> The Artisan command `php artisan content:promote {entry_id}` should wrap steps 1–7 in a DB transaction with a `--dry-run` flag that prints what it would do without committing.

---

## Entity relationship summary

```
content_types ──────────────────┐
                                ↓
categories ←── entries ── entry_meta
tags       ←── entries ── entry_images      ──→ images
layouts    ──→ entries ── entry_video_embeds ──→ video_embeds
images     ──→ entries ── entry_links        ──→ links
               entries ── entry_tags         ──→ tags

               publications ── publication_meta
images     ──→ publications ── publication_images  ──→ images
categories ←── publications ── publication_links   ──→ links
layouts    ──→ publications ── publication_tags     ──→ tags
               publications ── publication_store
               publications ── publication_bundles  ──→ publications

reactions → entries      (reactionable_type = 'entry')
reactions → publications (reactionable_type = 'publication')

quips (standalone)
settings (standalone)
activity_log (standalone)
export_jobs (standalone)
redirects (standalone)
users (standalone + 2FA)
```