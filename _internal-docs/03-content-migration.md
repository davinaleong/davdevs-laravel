# Dav/Devs — Content Migration Strategy v2
## Next.js (local) → Laravel CMS

---

## Overview

One-time structured import. Content lives in MDX/Markdown files on your local machine. Goal: every post, image, joke, and metadata field ends up in MySQL with correct relationships, zero content loss, and stable URLs.

---

## Step 1 — Audit source content

```bash
# In your Next.js project root — map all content files
find ./content -name "*.mdx" -o -name "*.md" | grep -v node_modules | sort

# Count per directory
find ./content -name "*.mdx" | sed 's|/[^/]*$||' | sort | uniq -c
```

**Directory → Post type mapping:**

| Next.js directory | Laravel post type |
|---|---|
| `articles/` | Article |
| `projects/` | Project |
| `tools/` | Tool |
| `notebooks/` | Notebook |
| `knowledge-sharing/` | Knowledge Sharing |
| `fem/` | Frontend Mentor |
| `sermons/` | Sermon |
| `ebooks/` | E-Book |
| `pages/` | Page |
| `funny/` (if file-based) | → `jokes` table (not `posts`) |

Jokes go into `jokes` table, not `posts`. If they're currently hardcoded (not MDX files), collect them manually.

---

## Step 2 — Standardise frontmatter

Run a Node.js audit script first to find missing/inconsistent fields before importing.

**Target frontmatter schema:**

```yaml
---
title: "AI Chatbot Integration with Azure"
slug: "20251224-ai-chatbot-integration"
type: article
status: published
published_at: "2025-12-24"
featured: true
category: development
tags: [ai, azure, openai]
excerpt: "Discover how I integrated..."
og_image: "/images/og-ai-chatbot.png"
layout: standard
# Optional meta (maps to post_meta table)
github_repo_url: "https://github.com/..."
live_demo_url: ""
fem_difficulty: ""
notebook_colab_url: ""
lighthouse_performance: 98
lighthouse_accessibility: 100
lighthouse_seo: 100
lighthouse_best_practices: 96
lighthouse_mobile_url: "https://lighthouse.gracesoft.dev/..."
lighthouse_desktop_url: "https://lighthouse.gracesoft.dev/..."
# E-Book only
ls_product_id: ""
ls_variant_id: ""
ls_store_url: ""
is_bundle: false
---
```

**Audit script** (`scripts/audit-frontmatter.js`):
```js
const fg = require('fast-glob')
const matter = require('gray-matter')
const fs = require('fs')

const files = fg.sync('content/**/*.mdx')
const required = ['title', 'slug', 'type', 'status', 'published_at', 'excerpt']
const report = []

for (const file of files) {
  const { data } = matter(fs.readFileSync(file, 'utf8'))
  const missing = required.filter(k => !data[k])
  if (missing.length) report.push({ file, missing })
}

console.table(report)
```

Fix all missing required fields before proceeding.

---

## Step 3 — Prepare images

**Batch upload to Cloudinary:**

```js
// scripts/upload-images.js
const cloudinary = require('cloudinary').v2
const fg = require('fast-glob')
const fs = require('fs')

cloudinary.config({ cloud_name: '...', api_key: '...', api_secret: '...' })

const images = fg.sync(['public/**/*.{jpg,jpeg,png,gif,webp,svg}'])
const mapping = {}

for (const img of images) {
  const result = await cloudinary.uploader.upload(img, {
    folder: 'davdevs',
    use_filename: true,
    unique_filename: false,
  })
  mapping[img] = {
    cloudinary_id: result.public_id,
    url: result.secure_url,
    width: result.width,
    height: result.height,
    format: result.format,
    bytes: result.bytes,
  }
}

fs.writeFileSync('scripts/image-mapping.json', JSON.stringify(mapping, null, 2))
```

**QR codes:** Upload normally — flag with `is_qr_code = true` in the import command (identify by filename convention or a separate list).

---

## Step 4 — Build Laravel import commands

All commands are idempotent (upsert on slug/cloudinary_id). Safe to re-run.

### `php artisan import:images {--mapping=scripts/image-mapping.json}`

```php
foreach ($mapping as $originalPath => $data) {
    Image::updateOrCreate(
        ['cloudinary_id' => $data['cloudinary_id']],
        [
            'url'        => $data['url'],
            'width'      => $data['width'],
            'height'     => $data['height'],
            'format'     => $data['format'],
            'bytes'      => $data['bytes'],
            'is_qr_code' => str_contains($originalPath, 'qr-'),
        ]
    );
}
```

### `php artisan import:posts {--dir=../davdevs-nextjs/content}`

For each MDX file:
1. Parse frontmatter + body via `spatie/yaml-front-matter`
2. Skip if `type === 'joke'` (handled by `import:jokes`)
3. Resolve `post_type_id`, `layout_id`, `category_id`, `og_image_id` (from image mapping)
4. Upsert `posts` row on `slug`
5. Compute `read_time_minutes` (count words in Markdown body, divide by 200, ceil)
6. Sync tags: `$post->tags()->sync($tagIds)`
7. Sync images from inline `![](...)` references (resolve via image mapping)
8. Insert `post_meta` rows for all extra frontmatter keys
9. Log result per file

### `php artisan import:jokes {--file=scripts/jokes.json}`

Prepare `jokes.json` manually from your current source (hardcoded array or MDX):
```json
[
  { "type": "qa", "question": "What kind of seed can open doors magically?", "answer": "A pas-key!" },
  { "type": "statement", "answer": "I told my computer I needed a break. Now it won't stop sending me Kit-Kat ads." }
]
```
Import inserts jokes into the `jokes` table, skipping duplicates on exact answer match.

### `php artisan import:youtube-embeds {--dir=../davdevs-nextjs/content}`

Scan all MDX files for YouTube embed patterns:
```bash
grep -rh "youtube.com/watch\|youtu.be\|video_id:" content/ --include="*.mdx"
```
Extract video IDs, fetch metadata via oEmbed, insert into `youtube_embeds`, link to posts via `post_youtube_embeds`.

---

## Step 5 — Handle MDX-specific content

```bash
# Find all custom React components used in MDX
grep -rh "<[A-Z][A-Za-z]*" content/ --include="*.mdx" | grep -v "<!--" | sed 's/[^<]*<\([A-Za-z]*\).*/\1/' | sort | uniq -c | sort -rn
```

**Conversion table:**

| MDX component | Action |
|---|---|
| `<Callout>`, `<Note>`, `<Warning>` | Replace with blockquote: `> **Note:** text` |
| `<CodeBlock lang="js">` | Replace with fenced: ` ```js ``` ` |
| `<YouTube id="abc">` | Extract ID → `youtube_embeds` table, remove from body |
| `<Image src="/foo.png">` | Replace with `![alt](/cloudinary-url)` using mapping |
| `<LemonSqueezyButton>` | Remove from body; store LS IDs in `post_meta` |
| Tool React components | Leave slug-based convention; Vite loads them dynamically |
| `<Tabs>`, `<Accordion>` | Convert to plain Markdown sections |

Write a simple Node.js transform script to do the replacements before running the Laravel import commands.

---

## Step 6 — Validate

| Check | How |
|---|---|
| Row counts | `SELECT post_type_id, COUNT(*) FROM posts GROUP BY post_type_id` vs file counts |
| Slug integrity | Spot-check 10 posts per type — slugs must match original filenames |
| Images loading | Open 10 random posts, verify Cloudinary URLs serve correctly |
| OG tags | `curl -s -A Twitterbot https://davinaleong.com/articles/20251224-ai-chatbot-integration | grep og:` |
| Search | Search for 5 known post titles — all should appear |
| Jokes | `/funny` — refresh 10 times, both Q/A and statement variants seen |
| Likes | Like a post, unlike it, re-like — count correct each time |
| 2FA | Log out, log back in, confirm TOTP challenge appears |
| Export | Trigger export job, download ZIP, verify JSON files have expected record counts |

---

## Step 7 — URL stability

Your `YYYYMMDD-slug` convention maps directly to Laravel routes — no redirects needed if routes are:

```php
Route::get('/{type}/{slug}', [PostController::class, 'show']);
// e.g. /articles/20251224-ai-chatbot-integration
```

If any URLs must change: seed the `redirects` table and ensure the redirect middleware runs before the 404 handler.

---

## Step 8 — Cutover

1. **Content freeze:** disable Next.js Vercel deploys (or set `NEXT_PUBLIC_MAINTENANCE=true`)
2. **Final pull:** `git pull` in the Next.js project to get last content
3. **Final import run:** re-run all import commands (idempotent)
4. **Staging smoke test:** full walkthrough on Laravel Cloud staging URL
5. **DNS cutover:** point `davinaleong.com` to Laravel Cloud production
6. **Monitor:** check Laravel Cloud logs + error tracking for 24h
7. **Archive:** keep Next.js build at `old.davinaleong.com` for 30 days

---

## NPM packages needed (Next.js scripts)

```bash
npm install gray-matter fast-glob cloudinary
```

## Composer packages needed (Laravel)

```bash
composer require spatie/yaml-front-matter league/commonmark
```

---

## Timeline

| Step | Effort |
|---|---|
| Audit + frontmatter normalisation | 1–2 days |
| Image upload to Cloudinary | 2–4 hours |
| MDX component transform script | 1–2 days |
| Writing import commands | 2–3 days |
| Validation + URL checks | 1 day |
| Cutover | 2–4 hours |
| **Total** | **~1 week** |