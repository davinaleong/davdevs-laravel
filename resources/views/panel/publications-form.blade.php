<x-cms-layout title="{{ $publication->exists ? 'Edit Publication' : 'New Publication' }}">
    <div x-data="{
        tab: 'content',
        selectedImages: {{ json_encode($publication->images->pluck('id')->all()) }},
        selectedLinks: {{ json_encode($publication->links->pluck('id')->all()) }},
        selectedTags: {{ json_encode($publication->tags->pluck('id')->all()) }},
        selectedBundleMembers: {{ json_encode($publication->bundleMembers->pluck('id')->all()) }},
        isBundle: {{ $publication->bundle ? 'true' : 'false' }},
        metaRows: {{ json_encode($publication->meta->map(fn($m) => ['key' => $m->key, 'value' => $m->value])->values()->all() ?: [['key' => '', 'value' => '']]) }},
        variantRows: {{ json_encode($publication->variants->map(fn($v) => ['name' => $v->name, 'price_display' => $v->price_display])->values()->all() ?: []) }},
        aiLoading: false,
        aiError: null,
        aiPrompt: '',
        generateTitle() {
            const title = document.querySelector('[name=title]').value;
            const topic = title || this.aiPrompt;
            if (!topic) { this.aiError = 'Enter a title or topic first.'; return; }
            this.aiLoading = true;
            this.aiError = null;
            fetch('{{ route('panel.ai.generate-title') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ topic, type: 'publication' }),
            }).then(r => r.json()).then(d => {
                this.aiLoading = false;
                if (d.error) { this.aiError = d.error; return; }
                document.querySelector('[name=title]').value = d.title;
            }).catch(() => {
                this.aiLoading = false;
                this.aiError = 'Request failed.';
            });
        },
        generateExcerpt() {
            const title = document.querySelector('[name=title]').value;
            if (!title) { this.aiError = 'Add a title first.'; return; }
            this.aiLoading = true;
            this.aiError = null;
            fetch('{{ route('panel.ai.generate-excerpt') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ title, type: 'publication' }),
            }).then(r => r.json()).then(d => {
                this.aiLoading = false;
                if (d.error) { this.aiError = d.error; return; }
                document.querySelector('[name=excerpt]').value = d.excerpt;
            }).catch(() => {
                this.aiLoading = false;
                this.aiError = 'Request failed.';
            });
        },
        generateContent() {
            const title = document.querySelector('[name=title]').value;
            if (!title) { this.aiError = 'Add a title first.'; return; }
            this.aiLoading = true;
            this.aiError = null;
            fetch('{{ route('panel.ai.generate') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ title, type: 'publication', tags: [], instructions: this.aiPrompt }),
            }).then(r => r.json()).then(d => {
                this.aiLoading = false;
                if (d.error) { this.aiError = d.error; return; }
                document.querySelector('[name=excerpt]').value = d.excerpt;
                const mde = window._easyMdeInstances['pub-body'];
                if (mde) mde.value(d.body);
                else document.getElementById('pub-body').value = d.body;
            }).catch(() => {
                this.aiLoading = false;
                this.aiError = 'Request failed.';
            });
        },
    }">
        <h1
            style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">
            {{ $publication->exists ? 'Edit Publication' : 'New Publication' }}</h1>

        <form method="POST"
            action="{{ $publication->exists ? route('panel.publications.update', $publication) : route('panel.publications.store') }}">
            @csrf
            @if ($publication->exists)
                @method('PUT')
            @endif

            <div style="display:flex;gap:5px;margin-bottom:16px;border-bottom:1px solid var(--cms-border);">
                @foreach (['content' => 'Content', 'store' => 'Store', 'media' => 'Media & Links', 'meta' => 'Meta', 'settings' => 'Settings'] as $key => $label)
                    <button type="button" @click="tab = '{{ $key }}'"
                        :style="tab === '{{ $key }}' ?
                            'border-bottom:2px solid var(--cms-accent);color:var(--cms-text-primary);' :
                            'border-bottom:2px solid transparent;color:var(--cms-text-muted);'"
                        style="padding:10px 20px;background:none;border-top:none;border-left:none;border-right:none;font-size:13px;font-weight:500;cursor:pointer;">{{ $label }}</button>
                @endforeach
            </div>

            <div x-show="tab === 'content'">
                <div
                    style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;">
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label style="font-size:12px;font-weight:500;color:var(--cms-text-secondary);">Title</label>
                            <button type="button" @click="generateTitle()" :disabled="aiLoading"
                                style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">✨
                                Generate title</button>
                        </div>
                        <input type="text" name="title" value="{{ old('title', $publication->title) }}" required
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:9px 12px;font-size:15px;color:var(--cms-input-text);">
                    </div>
                    <div>
                        <label
                            style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Tagline</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $publication->tagline) }}"
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label
                                style="font-size:12px;font-weight:500;color:var(--cms-text-secondary);">Excerpt</label>
                            <button type="button" @click="generateExcerpt()" :disabled="aiLoading"
                                style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">✨
                                Generate excerpt</button>
                        </div>
                        <textarea name="excerpt" rows="2"
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('excerpt', $publication->excerpt) }}</textarea>
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label style="font-size:12px;font-weight:500;color:var(--cms-text-secondary);">Body
                                (Markdown)</label>
                            <div style="display:flex;gap:12px;align-items:center;">
                                <button type="button" @click="generateContent()" :disabled="aiLoading"
                                    style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">✨
                                    Generate</button>
                            </div>
                        </div>
                        <input type="text" x-model="aiPrompt"
                            placeholder="AI prompt (e.g. 'focus on beginners, use simple language')"
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);margin-bottom:6px;font-style:italic;">
                        <p x-show="aiLoading" style="font-size:11px;color:var(--cms-text-muted);margin:0 0 6px;">
                            Working…</p>
                        <p x-show="aiError" x-text="aiError"
                            style="font-size:11px;color:var(--cms-error);margin:0 0 6px;"></p>
                        <textarea name="body" rows="16" id="pub-body" data-markdown-editor style="width:100%;box-sizing:border-box;">{{ old('body', $publication->body) }}</textarea>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'store'" x-cloak>
                <div
                    style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;">
                    <div>
                        <label
                            style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Checkout
                            URL</label>
                        <input type="text" name="ls_store_url"
                            value="{{ old('ls_store_url', $publication->store?->ls_store_url) }}"
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Price
                                Display</label>
                            <input type="text" name="price_display"
                                value="{{ old('price_display', $publication->store?->price_display) }}"
                                placeholder="SGD 9.90"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Currency</label>
                            <input type="text" name="currency"
                                value="{{ old('currency', $publication->store?->currency ?? 'SGD') }}" maxlength="3"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        </div>
                    </div>
                    <div>
                        <label
                            style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Free
                            Sample URL</label>
                        <input type="text" name="free_sample_url"
                            value="{{ old('free_sample_url', $publication->store?->free_sample_url) }}"
                            style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
                    </div>

                    <div>
                        <div style="font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">
                            Pricing Variants (optional — e.g. "Ebook Only" vs "Ebook + Exercise Pack")</div>
                        <template x-for="(row, i) in variantRows" :key="i">
                            <div style="display:flex;gap:8px;margin-bottom:8px;">
                                <input type="text" name="variant_name[]" x-model="row.name"
                                    placeholder="Variant name"
                                    style="flex:1;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
                                <input type="text" name="variant_price_display[]" x-model="row.price_display"
                                    placeholder="SGD 13"
                                    style="flex:0 0 110px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
                                <button type="button" @click="variantRows.splice(i, 1)"
                                    style="background:none;border:none;color:var(--cms-error);cursor:pointer;font-size:16px;">×</button>
                            </div>
                        </template>
                        <button type="button" @click="variantRows.push({name:'',price_display:''})"
                            style="font-size:12px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">+
                            Add variant</button>
                    </div>

                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="bundle" value="1" x-model="isBundle"
                            style="width:16px;height:16px;accent-color:var(--cms-accent);">
                        <span style="font-size:12px;color:var(--cms-text-muted);">This is a bundle of other
                            publications</span>
                    </label>
                    <div x-show="isBundle" x-cloak>
                        <label
                            style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Bundle
                            members</label>
                        <div style="max-height:160px;overflow-y:auto;display:flex;flex-direction:column;gap:4px;">
                            @foreach ($bundleCandidates as $bc)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="checkbox" value="{{ $bc->id }}"
                                        x-model="selectedBundleMembers" name="bundle_members[]"
                                        style="accent-color:var(--cms-accent);">
                                    <span
                                        style="font-size:12px;color:var(--cms-text-secondary);">{{ $bc->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'media'" x-cloak>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div
                        style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">
                            Images</div>
                        <div style="max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                            @foreach ($images as $img)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="checkbox" value="{{ $img->id }}" x-model="selectedImages"
                                        name="images[]" style="accent-color:var(--cms-accent);">
                                    <img src="{{ $img->url }}"
                                        style="width:32px;height:32px;object-fit:cover;border-radius:3px;">
                                    <span
                                        style="font-size:12px;color:var(--cms-text-secondary);">{{ $img->title ?: 'Untitled' }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div
                        style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">
                            Links</div>
                        <div style="max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                            @foreach ($links as $l)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                    <input type="checkbox" value="{{ $l->id }}" x-model="selectedLinks"
                                        name="links[]" style="accent-color:var(--cms-accent);">
                                    <span style="display:flex;flex-direction:column;overflow:hidden;">
                                        <span
                                            style="font-size:12px;color:var(--cms-text-secondary);">{{ $l->label }}</span>
                                        <span
                                            style="font-size:11px;color:var(--cms-text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $l->url }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div
                        style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">
                            Tags</div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach ($tags as $t)
                                <label
                                    style="display:flex;align-items:center;gap:4px;cursor:pointer;padding:3px 8px;border-radius:3px;background:var(--cms-bg-surface-2);">
                                    <input type="checkbox" value="{{ $t->id }}" x-model="selectedTags"
                                        name="tags[]" style="accent-color:var(--cms-accent);">
                                    <span
                                        style="font-size:11px;color:var(--cms-text-secondary);">{{ $t->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'meta'" x-cloak>
                <div
                    style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;margin-bottom:16px;">
                    <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">
                        Custom Meta (page_count, word_count, isbn, edition, publisher...)</div>
                    <template x-for="(row, i) in metaRows" :key="i">
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <input type="text" name="meta_key[]" x-model="row.key" placeholder="key"
                                style="flex:0 0 200px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cms-input-text);">
                            <input type="text" name="meta_value[]" x-model="row.value" placeholder="value"
                                style="flex:1;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
                            <button type="button" @click="metaRows.splice(i, 1)"
                                style="background:none;border:none;color:var(--cms-error);cursor:pointer;font-size:16px;">×</button>
                        </div>
                    </template>
                    <button type="button" @click="metaRows.push({key:'',value:''})"
                        style="font-size:12px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">+
                        Add field</button>
                </div>
            </div>

            <div x-show="tab === 'settings'" x-cloak>
                <div
                    style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Content
                                Type</label>
                            <select name="content_type_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach ($contentTypes as $ct)
                                    <option value="{{ $ct->id }}"
                                        {{ old('content_type_id', $publication->content_type_id) == $ct->id ? 'selected' : '' }}>
                                        {{ $ct->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Layout</label>
                            <select name="layout_id" required
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                @foreach ($layouts as $l)
                                    <option value="{{ $l->id }}"
                                        {{ old('layout_id', $publication->layout_id) == $l->id ? 'selected' : '' }}>
                                        {{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Page
                                Template</label>
                            <select name="publication_template_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— use default publication page —</option>
                                @foreach ($templates as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('publication_template_id', $publication->publication_template_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">React
                                Component (optional interactive extra)</label>
                            <select name="react_component_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach ($reactComponents as $rc)
                                    <option value="{{ $rc->id }}"
                                        {{ old('react_component_id', $publication->react_component_id) == $rc->id ? 'selected' : '' }}>
                                        {{ $rc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Category</label>
                            <select name="category_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('category_id', $publication->category_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Cover
                                Image</label>
                            <select name="cover_image_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach ($images as $img)
                                    <option value="{{ $img->id }}"
                                        {{ old('cover_image_id', $publication->cover_image_id) == $img->id ? 'selected' : '' }}>
                                        {{ $img->title ?: 'Image #' . $img->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">OG
                                Image</label>
                            <select name="og_image_id"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach ($images as $img)
                                    <option value="{{ $img->id }}"
                                        {{ old('og_image_id', $publication->og_image_id) == $img->id ? 'selected' : '' }}>
                                        {{ $img->title ?: 'Image #' . $img->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Status</label>
                            <select name="status" required
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                @foreach (['draft', 'private', 'published', 'archived'] as $s)
                                    <option value="{{ $s }}"
                                        {{ old('status', $publication->status) === $s ? 'selected' : '' }}>
                                        {{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Publish
                                at</label>
                            <input type="date" name="published_at"
                                value="{{ old('published_at', $publication->published_at?->format('Y-m-d')) }}"
                                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        </div>
                        <div style="display:flex;align-items:end;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="checkbox" name="featured" value="1"
                                    {{ old('featured', $publication->featured) ? 'checked' : '' }}
                                    style="width:16px;height:16px;accent-color:var(--cms-accent);">
                                <span style="font-size:12px;color:var(--cms-text-muted);">Featured</span>
                            </label>
                        </div>
                        <div>
                            <label
                                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Main
                                Foreground Colour</label>
                            <input type="color" name="design_color_primary"
                                value="{{ old('design_color_primary', $publication->getMeta('design_color_primary') ?? '#0E0E10') }}"
                                style="width:100%;height:36px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:2px 4px;cursor:pointer;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Broadcast section --}}
            @php
                $broadcastSettings = \App\Models\Setting::whereIn('key', [
                    'broadcast_linkedin_enabled',
                    'broadcast_facebook_enabled',
                    'broadcast_instagram_enabled',
                    'broadcast_threads_enabled',
                ])->pluck('value', 'key');
                $enabledPlatforms = collect(['linkedin', 'facebook', 'instagram', 'threads'])->filter(
                    fn($p) => filter_var($broadcastSettings["broadcast_{$p}_enabled"] ?? '0', FILTER_VALIDATE_BOOLEAN),
                );
            @endphp
            @if ($enabledPlatforms->isNotEmpty())
                <div
                    style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;margin-bottom:16px;">
                    <div style="font-size:12px;font-weight:600;color:var(--cms-text-secondary);margin-bottom:10px;">📡
                        Broadcast when saving as Published</div>
                    <div style="display:flex;gap:16px;flex-wrap:wrap;">
                        @foreach ($enabledPlatforms as $platform)
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" name="broadcast[]" value="{{ $platform }}"
                                    style="width:14px;height:14px;accent-color:var(--cms-accent);">
                                <span
                                    style="font-size:12px;color:var(--cms-text-secondary);">{{ ucfirst($platform) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="display:flex;gap:10px;">
                <button type="submit"
                    style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Save
                    Publication</button>
                <a href="{{ route('panel.publications.index') }}"
                    style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</x-cms-layout>
