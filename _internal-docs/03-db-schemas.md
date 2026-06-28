# Dav/Devs Laravel CMS — Database Schemas

> Conventions: all tables use `id` (BIGINT UNSIGNED AUTO_INCREMENT PK) unless noted. `created_at` + `updated_at` timestamps on every table. Soft deletes (`deleted_at`) on content tables. Foreign keys use `_id` suffix. Indexes noted where non-obvious.

---

## `settings`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `key` | VARCHAR(191) UNIQUE | e.g. `frontend_date_format`, `brand_name`, `footer_copyright` |
| `value` | TEXT NULL | Stored as string; cast in app layer |
| `cast` | VARCHAR(50) DEFAULT 'string' | `string`, `boolean`, `integer`, `json` |
| `group` | VARCHAR(100) | `display`, `header`, `footer`, `lighthouse`, `api` |

---

## `images`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `cloudinary_id` | VARCHAR(500) UNIQUE | Cloudinary `public_id` |
| `url` | TEXT | `secure_url` from Cloudinary |
| `title` | VARCHAR(500) NULL | |
| `alt` | VARCHAR(500) NULL | Required for a11y — nudge in CMS |
| `caption` | TEXT NULL | |
| `credit` | VARCHAR(500) NULL | Photographer / source credit |
| `width` | SMALLINT UNSIGNED NULL | px |
| `height` | SMALLINT UNSIGNED NULL | px |
| `format` | VARCHAR(20) NULL | `jpg`, `png`, `webp`, `gif` |
| `bytes` | INT UNSIGNED NULL | File size |
| `is_qr_code` | BOOLEAN DEFAULT FALSE | Filtered separately in image browser |
| `deleted_at` | TIMESTAMP NULL | Soft delete |

**Indexes:** `cloudinary_id` (unique), `is_qr_code`

---

## `links`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `label` | VARCHAR(255) | Display text |
| `url` | TEXT | |
| `target` | VARCHAR(20) DEFAULT '_self' | `_self`, `_blank` |
| `rel` | VARCHAR(100) DEFAULT 'noopener noreferrer' | Auto-set for `_blank`; customisable |
| `description` | TEXT NULL | Internal note / context |
| `category` | VARCHAR(50) DEFAULT 'general' | `general`, `social`, `nav` |
| `icon_class` | VARCHAR(100) NULL | Tabler icon class or custom |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | |
| `is_active` | BOOLEAN DEFAULT TRUE | |

**Indexes:** `category`, `sort_order`

---

## `youtube_embeds`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `video_id` | VARCHAR(20) UNIQUE | YouTube video ID (e.g. `dQw4w9WgXcQ`) |
| `title` | VARCHAR(500) | From oEmbed |
| `description` | TEXT NULL | |
| `channel_name` | VARCHAR(255) NULL | |
| `thumbnail_url` | TEXT NULL | |
| `duration_seconds` | SMALLINT UNSIGNED NULL | |
| `published_at` | DATE NULL | YouTube publish date |

---

## `layouts`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `name` | VARCHAR(255) | Display name e.g. "Standard", "Product Page" |
| `slug` | VARCHAR(255) UNIQUE | |
| `blade_component` | VARCHAR(255) | e.g. `layouts.standard` |
| `description` | TEXT NULL | |
| `preview_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | |
| `is_active` | BOOLEAN DEFAULT TRUE | |

---

## `post_types`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `name` | VARCHAR(255) | `Article`, `Project`, `Tool`, etc. |
| `slug` | VARCHAR(255) UNIQUE | `article`, `project`, `tool`, `notebook`, `knowledge-sharing`, `fem`, `sermon`, `ebook`, `page` |
| `has_tools_react` | BOOLEAN DEFAULT FALSE | Loads React island on post detail |
| `excluded_from_main_list` | BOOLEAN DEFAULT FALSE | Jokes: excluded from homepage + sitemaps |
| `description` | TEXT NULL | |

---

## `categories`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_type_id` | BIGINT UNSIGNED FK → `post_types.id` | Scoped per type |
| `name` | VARCHAR(255) | |
| `slug` | VARCHAR(255) | |
| `description` | TEXT NULL | |
| `is_active` | BOOLEAN DEFAULT TRUE | |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** (`post_type_id`, `slug`) UNIQUE

---

## `tags`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_type_id` | BIGINT UNSIGNED FK → `post_types.id` | |
| `name` | VARCHAR(255) | |
| `slug` | VARCHAR(255) | |

**Indexes:** (`post_type_id`, `slug`) UNIQUE

---

## `posts`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `ulid` | CHAR(26) UNIQUE | Public-safe ID for preview tokens |
| `post_type_id` | BIGINT UNSIGNED FK → `post_types.id` | |
| `layout_id` | BIGINT UNSIGNED FK → `layouts.id` | |
| `category_id` | BIGINT UNSIGNED NULL FK → `categories.id` | |
| `og_image_id` | BIGINT UNSIGNED NULL FK → `images.id` | |
| `title` | VARCHAR(500) | |
| `slug` | VARCHAR(500) UNIQUE | Preserves `YYYYMMDD-slug` convention |
| `excerpt` | TEXT NULL | |
| `content` | LONGTEXT NULL | Raw Markdown |
| `read_time_minutes` | TINYINT UNSIGNED DEFAULT 1 | Computed on save: ceil(word_count / 200) |
| `status` | ENUM('draft','private','published') DEFAULT 'draft' | |
| `is_featured` | BOOLEAN DEFAULT FALSE | |
| `published_at` | TIMESTAMP NULL | Future date = scheduled |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** `slug` (unique), `status`, `published_at`, `post_type_id`, FULLTEXT (`title`, `excerpt`, `content`)

---

## `post_meta`

Extensible key/value store per post. Replaces hard-coded columns for one-off fields.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | |
| `key` | VARCHAR(191) | See key conventions below |
| `value` | TEXT NULL | |

**Indexes:** (`post_id`, `key`) UNIQUE

**Key conventions:**

| Key | Used by | Example value |
|---|---|---|
| `ls_product_id` | E-Books | `a1b2c3d4-...` |
| `ls_variant_id` | E-Books | `e5f6g7h8-...` |
| `ls_store_url` | E-Books | `https://davinaleong.lemonsqueezy.com/...` |
| `is_bundle` | E-Books | `true` |
| `bundle_volume_ids` | E-Book bundles | `[1,2,3]` (JSON array of post IDs) |
| `github_repo_url` | Projects, Tools | `https://github.com/...` |
| `live_demo_url` | Projects, Tools | `https://...` |
| `fem_difficulty` | Frontend Mentor | `junior`, `intermediate`, `advanced` |
| `notebook_colab_url` | Notebooks | `https://colab.research.google.com/...` |
| `lighthouse_performance` | Any | `98` |
| `lighthouse_accessibility` | Any | `100` |
| `lighthouse_seo` | Any | `100` |
| `lighthouse_best_practices` | Any | `96` |
| `lighthouse_mobile_url` | Any | URL to hosted Lighthouse report |
| `lighthouse_desktop_url` | Any | URL to hosted Lighthouse report |

---

## `post_images`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | |
| `image_id` | BIGINT UNSIGNED FK → `images.id` | |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | |
| `caption_override` | TEXT NULL | Per-attachment caption |

**Indexes:** (`post_id`, `image_id`) UNIQUE, (`post_id`, `sort_order`)

---

## `post_youtube_embeds`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | |
| `youtube_embed_id` | BIGINT UNSIGNED FK → `youtube_embeds.id` | |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | |

**Indexes:** (`post_id`, `youtube_embed_id`) UNIQUE

---

## `post_links`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | |
| `link_id` | BIGINT UNSIGNED FK → `links.id` | |
| `sort_order` | SMALLINT UNSIGNED DEFAULT 0 | |

---

## `post_tags`

| Column | Type | Notes |
|---|---|---|
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | Composite PK |
| `tag_id` | BIGINT UNSIGNED FK → `tags.id` | Composite PK |

---

## `jokes`

Separate from `posts` — jokes are not publicly listed, not in sitemaps, and served randomly.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `type` | ENUM('qa','statement') | |
| `question` | TEXT NULL | Q/A variant only |
| `answer` | TEXT | The punchline / statement text |
| `is_active` | BOOLEAN DEFAULT TRUE | Inactive jokes never served |
| `deleted_at` | TIMESTAMP NULL | |

**Indexes:** `type`, `is_active`

---

## `likes`

Anonymous like system. No personal data stored.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `post_id` | BIGINT UNSIGNED FK → `posts.id` | |
| `token_hash` | CHAR(64) | SHA-256 of visitor UUID cookie |
| `ip_hash` | CHAR(64) | SHA-256(ip + daily_salt) |
| `created_at` | TIMESTAMP | No `updated_at` — immutable |

**Indexes:** (`post_id`, `token_hash`) UNIQUE — prevents double-like per visitor per post

---

## `logs`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `channel` | VARCHAR(50) | `http`, `auth`, `cms`, `model`, `ai`, `export` |
| `level` | VARCHAR(20) | `debug`, `info`, `warning`, `error`, `critical` |
| `message` | TEXT | |
| `context` | JSON NULL | Arbitrary structured data |
| `ip_hash` | CHAR(64) NULL | SHA-256(ip + daily_salt) |
| `user_agent` | TEXT NULL | |
| `url` | VARCHAR(2000) NULL | |
| `method` | VARCHAR(10) NULL | `GET`, `POST`, etc. |
| `status_code` | SMALLINT UNSIGNED NULL | HTTP response status |
| `duration_ms` | SMALLINT UNSIGNED NULL | Request duration |
| `created_at` | TIMESTAMP | No `updated_at` — immutable |

**Indexes:** `channel`, `level`, `created_at` — used by log viewer filters and prune job

---

## `redirects`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `from_path` | VARCHAR(500) UNIQUE | e.g. `/old-articles/foo` |
| `to_path` | VARCHAR(500) | e.g. `/articles/foo` |
| `status_code` | SMALLINT UNSIGNED DEFAULT 301 | `301` permanent, `302` temporary |
| `is_active` | BOOLEAN DEFAULT TRUE | |

Checked by middleware before 404. Cached for performance.

---

## `export_jobs`

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED PK | |
| `status` | ENUM('pending','processing','complete','failed') DEFAULT 'pending' | |
| `file_path` | VARCHAR(1000) NULL | Path after export completes |
| `download_url` | TEXT NULL | Temporary signed URL |
| `expires_at` | TIMESTAMP NULL | Auto-delete after 24h |
| `error_message` | TEXT NULL | Set on failure |

---

## `users`

Extend default Laravel `users` table with:

| Column | Type | Notes |
|---|---|---|
| `totp_secret` | TEXT NULL | Encrypted TOTP secret (`encrypted` cast) |
| `totp_enabled` | BOOLEAN DEFAULT FALSE | |
| `recovery_codes` | JSON NULL | Hashed single-use recovery codes |
| `two_factor_confirmed_at` | TIMESTAMP NULL | When 2FA was confirmed |

---

## Entity Relationship Summary

```
settings (standalone)
users (standalone + 2FA fields)

images ←── post_images ──→ posts
links  ←── post_links  ──→ posts
youtube_embeds ←── post_youtube_embeds ──→ posts
tags ←── post_tags ──→ posts
post_meta → posts

posts → post_types
posts → layouts
posts → categories
posts → images (og_image_id)

categories → post_types
tags → post_types

jokes (standalone)
likes → posts
logs (standalone)
redirects (standalone)
export_jobs (standalone)
```