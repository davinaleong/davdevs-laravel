# Content Migration Plan — Old Site → Dav/Devs Laravel CMS

Status: **planning only** — no import code has been written yet. This document defines scope, target schema mapping, and open decisions. Building and running the importer is a separate, later task.

## 1. Source and target

- **Source:** local Next.js (App Router) project at `C:\Development\proj-davdevs-2025`. Content is plain Markdown with YAML frontmatter under `app/content/<type>/*.md`, read via `gray-matter`. There is no typed content-collections layer — files are the source of truth.
- **Target:** this repo, `davdevs-laravel`. Content lives in the `entries` table (articles/projects/tools/etc.) and the `publications` table (ebooks), with pivot tables for images, video embeds, links, and tags. Images are stored in **Cloudinary** (via `cloudinary/cloudinary_php`), not S3 — the `s3` disk in `config/filesystems.php` is an unconfigured stock stub and is out of scope for this migration.

## 2. Source content inventory

| Old folder (`app/content/`) | Count | Target                                         |
| --------------------------- | ----- | ---------------------------------------------- |
| `articles`                  | 24    | `entries`, content_type = article              |
| `projects`                  | 32    | `entries`, content_type = project              |
| `tools`                     | 13    | `entries`, content_type = tool                 |
| `notebooks`                 | 27    | `entries`, content_type = notebook             |
| `knowledge-sharing`         | 4     | `entries`, content_type = knowledge-sharing    |
| `fem`                       | 24    | `entries`, content_type = fem                  |
| `sermons`                   | 92    | `entries`, content_type = sermon               |
| `static`                    | 5     | `entries`, content_type = page                 |
| `ebooks`                    | 7     | `publications` (separate table, not `entries`) |

Total: ~230 markdown files, plus 212 files under `public/` (mostly PNG, some JPG/SVG) and per-ebook page images under `public/books/<slug>/`.

The 8 content types already seeded (`database/seeders/ContentTypeSeeder.php`) — article, project, tool, notebook, knowledge-sharing, fem, sermon, page — cover all content types.

## 3. Frontmatter → schema field mapping

Common old frontmatter fields: `title, slug, description, date, author, tags[], featured, readingTime, published, images[{src,alt}], links[{label,href}], url`.

| Old field                         | New field                | Notes                                                                                                                                                                                                         |
| --------------------------------- | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `title`                           | `entries.title`          |                                                                                                                                                                                                               |
| `slug`                            | `entries.slug`           | confirm format during audit (see §8) before deciding whether a `redirects` row is needed per item                                                                                                             |
| `description`                     | `entries.excerpt`        |                                                                                                                                                                                                               |
| `date`                            | `entries.published_at`   |                                                                                                                                                                                                               |
| `published`                       | `entries.status`         | map boolean → `draft`/`published`                                                                                                                                                                             |
| `featured`                        | `entries.featured`       |                                                                                                                                                                                                               |
| `readingTime`                     | _(drop)_                 | `Entry` model auto-calculates `read_time` on save — don't import the old value                                                                                                                                |
| `tags[]`                          | `entry_tags` pivot       | see §6 for normalization                                                                                                                                                                                      |
| `images[]`                        | `entry_images` pivot     | requires images to already exist in Cloudinary — see §5                                                                                                                                                       |
| `links[]`                         | `entry_links` pivot      |                                                                                                                                                                                                               |
| `author`                          | _(drop or `entry_meta`)_ | confirm whether multi-author support is needed; likely single-author site                                                                                                                                     |
| body (markdown after frontmatter) | `entries.body`           | rendered through `MarkdownService` (league/commonmark, `html_input: strip`) — any raw HTML/JSX left in bodies will be silently stripped, so it must be converted to plain Markdown _before_ import, not after |

Ebooks use a distinct schema (`publishedAt, coverImage, backImage, price, storeStatus, lqCheckoutBase, lqProductId, formats[], links{digital,print}, design{...}`) — maps reasonably well to `publications` + `publication_store`, since that table already has Lemon Squeezy–shaped fields. See §7.2 for the harder part (bespoke page layouts).

## 4. Content type migration notes

- **Markdown texts** (articles, projects, notebooks, knowledge-sharing, fem, sermons, static): straightforward — parse frontmatter, map fields per §3, import body as Markdown text into `entries.body`.
- **Images**: see §5.
- **React components**: a scan of all 230 files for embedded JSX/components (`grep` for `<ComponentName>` patterns) found **zero matches** — content bodies are plain Markdown, not MDX. The components referenced in `REPOSITORY_MAP.md` (Gallery, Lightbox, QrCode, tool widgets) live in `app/components/` and are wired by page/type, not embedded inline in content. **This means there is no inline-JSX conversion problem to solve.** The one related gap: the Laravel `tool` content type has a `react_island` flag intended for interactive tool widgets, but no React toolchain exists in this Laravel project yet (confirmed in progress notes — deferred, no `package.json` entry point for it). Tool _content_ (markdown + images) migrates like any other entry; the interactive widget itself is a separate, unbuilt frontend feature and is **out of scope** for this migration.
- **Embeds**: no structured embed frontmatter or shortcode syntax was found in the old content — only prose mentions of "YouTube" as text, no iframe/embed patterns. Laravel's `video_embeds` table is ready to receive structured embeds, but populating it will require a manual/semi-automated pass over content bodies to find actual embed URLs (if any exist beyond prose mentions) rather than a mechanical frontmatter extraction.
- **Full pages (eBooks)**: the awkward case — see §7.2.

## 5. Image migration (Cloudinary)

1. Enumerate `public/**/*.{png,jpg,jpeg,svg,gif,webp}` (212 files) in the old project.
2. Upload each to Cloudinary, capturing `public_id, secure_url, width, height, format, bytes`.
3. Insert/upsert into the `images` table using that metadata.
4. Build an old-path → `images.id` lookup so entry/publication import can resolve `images[]` frontmatter and any inline `![](...)` Markdown references into `entry_images` / `publication_images` pivot rows with correct `sort_order`.
5. QR code images (if any) should be flagged consistently with however the `images` table's QR handling is designed — confirm the current schema/UI convention before import rather than assuming a naming pattern.

## 6. Tags and categories

Old tags are freeform strings per file (`tags: [ai, azure, openai]`). Target schema has normalized `tags` and `categories` tables scoped by content type. Before import:

- Collect the full distinct tag vocabulary across all 230 files.
- Decide on casing/synonym normalization (e.g. `AI` vs `ai`) and whether any tags should instead become `categories`.
- Pre-seed `tags`/`categories` rows, then resolve frontmatter tag arrays to IDs during import rather than creating tags ad hoc mid-import.

## 7. Open decisions (need answers before building the importer)

### 7.1 eBooks — bespoke layouts

On the old site, each ebook is a hand-built page at `app/ebooks/<slug>/page.tsx` + `layout.tsx` + `styles.css`, with page-turn-style images at `public/books/<slug>/000N.png`. The corresponding `app/content/ebooks/<slug>.md` file holds only summary frontmatter and one paragraph — it is **not** the source of the actual page content. The Laravel `publications` table + `publication` layout Blade component is metadata/body-driven, not a per-title custom layout system.

This means eBook migration is not a mechanical frontmatter-to-row import — it requires a decision:

- **Option A (recommended for a CMS-driven future):** migrate only the metadata (title, price, cover/back images, links, tagline) into `publications`, attach the page images as an ordered gallery via `publication_images`, and accept that the bespoke page-turn visual treatment is replaced by whatever the standard `publication` layout renders. Content-wise nothing is lost, but the custom presentation is not preserved as-is.
- **Option B:** keep the 7 ebook pages as static/custom Blade views outside the `publications`-driven rendering path, and only use `publications` rows for store/purchase metadata (price, links) so they still appear in listings. More faithful visually, but means ebooks are not "real" CMS content and any future edits require code changes.

This should be resolved with the user before writing the ebook importer, since it affects both the importer and possibly the `publication` Blade layout.

### 7.2 Slug/URL stability

Need to confirm the actual old-site slug convention (e.g. is it date-prefixed, or a plain freeform slug field?) before deciding whether imported entries can reuse old slugs directly or need `redirects` table entries for changed URLs.

### 7.3 Author field

Old frontmatter has an `author` field; current Laravel schema doesn't appear to have per-entry authorship. Confirm whether this is single-author (drop the field) or needs to be preserved somewhere.

## 8. Suggested process (once decisions above are made)

1. **Audit** — script that reads every old `.md` file, reports missing/inconsistent required frontmatter fields, and prints the actual slug format and full tag vocabulary (don't assume — verify).
2. **Images** — run the Cloudinary upload pass (§5), producing the path→`images.id` mapping.
3. **Tags/categories** — seed normalized vocabulary (§6).
4. **Import per content type** — one pass per old folder, mapping frontmatter (§3) and body into `entries`, resolving images/links/tags via the mappings built above.
5. **Ebooks** — handled separately per whichever option is chosen in §7.1.
6. **Embeds** — manual/semi-automated pass over bodies for real embed URLs (§4), inserting into `video_embeds` + `entry_video_embeds`.
7. **Validate** — row counts per type vs. file counts, spot-check rendered body output (confirm no content was silently stripped by `MarkdownService`'s `html_input: strip`), spot-check images load, spot-check tag/category assignment.
8. **Cutover** — decide URL strategy (§7.2), then point the live domain at the Laravel app once validation passes.

## 9. Explicitly out of scope for this migration

- S3 storage — current image pipeline is Cloudinary; no S3 migration is planned here.
- Building the `tool` content type's interactive React island — unrelated frontend feature, not a content migration task. (Note: a React build toolchain is being introduced regardless for the Component Manager, §10.1 — but rendering the tool island itself remains unbuilt and out of scope.)
- Anything resembling a "jokes/quips" import — no corresponding content folder was found in the old site's source; the `quips` table appears to need its own content source if one exists outside this repo.

## 10. New CMS manager features (schema additions)

These are new CMS features, not part of importing old content — added here because they change the `entries`/`publications` schema before the importer is built. Full column definitions are in `07-db-schemas-v2.md`.

### 10.1 React Component Manager

- Folder: `resources/js/components/` — one `.jsx`/`.tsx` file per component, PascalCase file names (standard frontend convention). This requires introducing a React build toolchain to the project, which does not exist yet (see §9).
- The manager scans this folder and lists all discovered components alphabetically by name in the CMS UI.
- New `react_components` lookup table, synced from the folder scan (id, name, slug, file_path, description, active — inactive rather than deleted when a file disappears, so existing references don't break).
- **Schema change:** `entries.react_component_id` — nullable FK → `react_components.id`. When creating/editing an entry, an optional field lets the author attach one component from the list.

### 10.2 Publication Template Manager

- Folder: `resources/views/publications/templates/<publication_type>/` (Laravel Blade view convention) — hand-crafted Blade templates, one subfolder per publication type (currently just `ebook`).
- New `publication_templates` lookup table, synced from the folder scan (id, publication_type, name, slug, blade_path, description, active), scoped by `publication_type` so the manager only lists templates matching the publication being edited.
- **Schema change:** `publications.publication_template_id` — nullable FK → `publication_templates.id`. When creating/editing a publication, an optional field lets the author pick a template from the type-matched list.
