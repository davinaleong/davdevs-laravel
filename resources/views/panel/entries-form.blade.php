<x-cms-layout title="{{ $entry->exists ? 'Edit Entry' : 'New Entry' }}">
    <div x-data="{
        tab: 'content',
        preview: false,
        selectedImages: {{ json_encode($entry->images->pluck('id')->all()) }},
        selectedVideos: {{ json_encode($entry->videoEmbeds->pluck('id')->all()) }},
        selectedLinks: {{ json_encode($entry->links->pluck('id')->all()) }},
        selectedTags: {{ json_encode($entry->tags->pluck('id')->all()) }},
        metaRows: {{ json_encode($entry->meta->map(fn($m) => ['key' => $m->key, 'value' => $m->value])->values()->all() ?: [['key' => '', 'value' => '']]) }},
        contentTypeId: {{ Js::from((string) old('content_type_id', $entry->content_type_id ?? '')) }},
        reactIslandByType: {{ Js::from($contentTypes->pluck('react_island', 'id')) }},
        aiLoading: false,
        aiError: null,
        aiSuggestions: null,
        generateContent() {
            const title = document.querySelector('[name=title]').value;
            if (!title) { this.aiError = 'Add a title first.'; return; }
            this.aiLoading = true; this.aiError = null;
            fetch('{{ route('panel.ai.generate') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ title, type: '{{ $entry->contentType->name ?? 'Article' }}', tags: [] }),
            }).then(r => r.json()).then(d => {
                this.aiLoading = false;
                if (d.error) { this.aiError = d.error; return; }
                document.querySelector('[name=excerpt]').value = d.excerpt;
                document.getElementById('entry-body').value = d.body;
            }).catch(() => { this.aiLoading = false; this.aiError = 'Request failed.'; });
        },
        auditContent() {
            const title = document.querySelector('[name=title]').value;
            const body = document.getElementById('entry-body').value;
            if (!body) { this.aiError = 'Nothing to audit yet.'; return; }
            this.aiLoading = true; this.aiError = null; this.aiSuggestions = null;
            fetch('{{ route('panel.ai.audit') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ title, body }),
            }).then(r => r.json()).then(d => {
                this.aiLoading = false;
                if (d.error) { this.aiError = d.error; return; }
                this.aiSuggestions = d.suggestions;
            }).catch(() => { this.aiLoading = false; this.aiError = 'Request failed.'; });
        },
    }">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">{{ $entry->exists ? 'Edit Entry' : 'New Entry' }}</h1>
            @if($entry->exists)
            <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">Read time: {{ $entry->read_time }} min · Slug: {{ $entry->slug }}</span>
            @endif
        </div>

        <form method="POST" action="{{ $entry->exists ? route('panel.entries.update', $entry) : route('panel.entries.store') }}">
            @csrf
            @if($entry->exists) @method('PUT') @endif

            {{-- Tabs --}}
            <div style="display:flex;gap:2px;margin-bottom:16px;border-bottom:1px solid var(--cms-border);">
                @foreach(['content' => 'Content', 'media' => 'Media & Links', 'meta' => 'Meta', 'settings' => 'Settings'] as $key => $label)
                <button type="button" @click="tab = '{{ $key }}'"
                        :style="tab === '{{ $key }}' ? 'border-bottom:2px solid var(--cms-accent);color:var(--cms-text-primary);' : 'border-bottom:2px solid transparent;color:var(--cms-text-muted);'"
                        style="padding:8px 16px;background:none;border-top:none;border-left:none;border-right:none;font-size:13px;font-weight:500;cursor:pointer;">{{ $label }}</button>
                @endforeach
            </div>

            {{-- CONTENT TAB --}}
            <div x-show="tab === 'content'">
                <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Title</label>
                        <input type="text" name="title" value="{{ old('title', $entry->title) }}" required
                               style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:9px 12px;font-family:'Inter',sans-serif;font-size:15px;color:var(--cms-input-text);">
                        @error('title')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Excerpt</label>
                        <textarea name="excerpt" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('excerpt', $entry->excerpt) }}</textarea>
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <label style="font-size:12px;font-weight:500;color:var(--cms-text-secondary);">Body (Markdown)</label>
                            <div style="display:flex;gap:12px;align-items:center;">
                                <button type="button" @click="generateContent()" :disabled="aiLoading" style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">✨ Generate</button>
                                <button type="button" @click="auditContent()" :disabled="aiLoading" style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">🔍 Audit</button>
                                <button type="button" @click="preview = !preview" style="font-size:11px;color:var(--cms-accent);background:none;border:none;cursor:pointer;" x-text="preview ? 'Edit' : 'Preview'"></button>
                            </div>
                        </div>
                        <p x-show="aiLoading" style="font-size:11px;color:var(--cms-text-muted);margin:0 0 6px;">Working…</p>
                        <p x-show="aiError" x-text="aiError" style="font-size:11px;color:var(--cms-error);margin:0 0 6px;"></p>
                        <textarea name="body" id="entry-body" rows="18" x-show="!preview"
                                  style="width:100%;box-sizing:border-box;background:var(--cms-editor-bg,var(--cms-input-bg));border:1px solid var(--cms-input-border);border-radius:5px;padding:12px;font-family:'JetBrains Mono',monospace;font-size:13px;line-height:1.6;color:var(--cms-input-text);">{{ old('body', $entry->body) }}</textarea>
                        <div x-show="preview" x-cloak style="border:1px solid var(--cms-border);border-radius:5px;padding:16px;background:var(--cms-bg-surface-2);font-family:'Lora',serif;font-size:14px;line-height:1.8;color:var(--cms-text-primary);white-space:pre-wrap;" x-text="document.getElementById('entry-body').value"></div>

                        <div x-show="aiSuggestions && aiSuggestions.length" x-cloak style="margin-top:12px;border:1px solid var(--cms-border);border-radius:6px;padding:12px;background:var(--cms-bg-surface-2);">
                            <div style="font-size:11px;font-weight:600;color:var(--cms-text-primary);margin-bottom:8px;">AI Audit Suggestions</div>
                            <template x-for="s in aiSuggestions">
                                <div style="display:flex;gap:8px;margin-bottom:6px;font-size:12px;">
                                    <span style="font-family:'JetBrains Mono',monospace;font-size:9px;padding:2px 6px;border-radius:3px;background:var(--cms-accent-tint);color:var(--cms-accent);white-space:nowrap;height:fit-content;" x-text="s.category"></span>
                                    <span style="color:var(--cms-text-secondary);" x-text="s.note"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MEDIA & LINKS TAB --}}
            <div x-show="tab === 'media'" x-cloak>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

                    {{-- Images --}}
                    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">Images</div>
                        <div style="max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                            @foreach($images as $img)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px;border-radius:4px;" :style="selectedImages.includes({{ $img->id }}) ? 'background:var(--cms-accent-tint)' : ''">
                                <input type="checkbox" value="{{ $img->id }}" x-model="selectedImages" name="images[]" style="accent-color:var(--cms-accent);">
                                <img src="{{ $img->url }}" style="width:32px;height:32px;object-fit:cover;border-radius:3px;">
                                <span style="font-size:12px;color:var(--cms-text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $img->title ?: $img->alt ?: 'Untitled' }}</span>
                            </label>
                            @endforeach
                        </div>
                        <a href="{{ route('panel.images.create') }}" target="_blank" style="font-size:11px;color:var(--cms-accent);text-decoration:none;display:block;margin-top:8px;">+ Upload new image</a>
                    </div>

                    {{-- Video embeds --}}
                    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">YouTube Embeds</div>
                        <div style="max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                            @foreach($videoEmbeds as $v)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px;border-radius:4px;">
                                <input type="checkbox" value="{{ $v->id }}" x-model="selectedVideos" name="video_embeds[]" style="accent-color:var(--cms-accent);">
                                <img src="{{ $v->thumbnail_url }}" style="width:40px;height:24px;object-fit:cover;border-radius:3px;">
                                <span style="font-size:12px;color:var(--cms-text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $v->title }}</span>
                            </label>
                            @endforeach
                        </div>
                        <a href="{{ route('panel.video-embeds.create') }}" target="_blank" style="font-size:11px;color:var(--cms-accent);text-decoration:none;display:block;margin-top:8px;">+ Add new video</a>
                    </div>

                    {{-- Links --}}
                    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">Links</div>
                        <div style="max-height:200px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;">
                            @foreach($links as $l)
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px;border-radius:4px;">
                                <input type="checkbox" value="{{ $l->id }}" x-model="selectedLinks" name="links[]" style="accent-color:var(--cms-accent);">
                                <span style="font-size:12px;color:var(--cms-text-secondary);">{{ $l->label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;">
                        <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">Tags</div>
                        <div style="max-height:200px;overflow-y:auto;display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach($tags as $t)
                            <label style="display:flex;align-items:center;gap:4px;cursor:pointer;padding:3px 8px;border-radius:3px;background:var(--cms-bg-surface-2);">
                                <input type="checkbox" value="{{ $t->id }}" x-model="selectedTags" name="tags[]" style="accent-color:var(--cms-accent);">
                                <span style="font-size:11px;color:var(--cms-text-secondary);">{{ $t->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- META TAB --}}
            <div x-show="tab === 'meta'" x-cloak>
                <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;margin-bottom:16px;">
                    <div style="font-size:13px;font-weight:600;margin-bottom:10px;color:var(--cms-text-primary);">Custom Meta Fields</div>
                    <p style="font-size:11px;color:var(--cms-text-muted);margin-bottom:12px;">e.g. github_repo_url, live_demo_url, fem_difficulty, sermon_speaker, lighthouse_performance</p>
                    <template x-for="(row, i) in metaRows" :key="i">
                        <div style="display:flex;gap:8px;margin-bottom:8px;">
                            <input type="text" :name="'meta_key[]'" x-model="row.key" placeholder="key"
                                   style="flex:0 0 200px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cms-input-text);">
                            <input type="text" :name="'meta_value[]'" x-model="row.value" placeholder="value"
                                   style="flex:1;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
                            <button type="button" @click="metaRows.splice(i, 1)" style="background:none;border:none;color:var(--cms-error);cursor:pointer;font-size:16px;">×</button>
                        </div>
                    </template>
                    <button type="button" @click="metaRows.push({key:'',value:''})" style="font-size:12px;color:var(--cms-accent);background:none;border:none;cursor:pointer;">+ Add field</button>
                </div>
            </div>

            {{-- SETTINGS TAB --}}
            <div x-show="tab === 'settings'" x-cloak>
                <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:16px;display:flex;flex-direction:column;gap:16px;margin-bottom:16px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Content Type</label>
                            <select name="content_type_id" x-model="contentTypeId" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                @foreach($contentTypes as $ct)
                                <option value="{{ $ct->id }}" {{ old('content_type_id', $entry->content_type_id) == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Layout</label>
                            <select name="layout_id" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                @foreach($layouts as $l)
                                <option value="{{ $l->id }}" {{ old('layout_id', $entry->layout_id) == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="reactIslandByType[contentTypeId]" x-cloak>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">React Component</label>
                            <select name="react_component_id" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach($reactComponents as $rc)
                                <option value="{{ $rc->id }}" {{ old('react_component_id', $entry->react_component_id) == $rc->id ? 'selected' : '' }}>{{ $rc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Category</label>
                            <select name="category_id" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id', $entry->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">OG Image</label>
                            <select name="og_image_id" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="">— none —</option>
                                @foreach($images as $img)
                                <option value="{{ $img->id }}" {{ old('og_image_id', $entry->og_image_id) == $img->id ? 'selected' : '' }}>{{ $img->title ?: $img->alt ?: 'Image #'.$img->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Status</label>
                            <select name="status" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                @foreach(['draft','private','published','archived'] as $s)
                                <option value="{{ $s }}" {{ old('status', $entry->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Visibility</label>
                            <select name="visibility" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                                <option value="public" {{ old('visibility', $entry->visibility) === 'public' ? 'selected' : '' }}>Public</option>
                                <option value="unlisted" {{ old('visibility', $entry->visibility) === 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Publish at</label>
                            <input type="datetime-local" name="published_at" value="{{ old('published_at', $entry->published_at?->format('Y-m-d\TH:i')) }}"
                                   style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        </div>
                        <div style="display:flex;align-items:end;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="checkbox" name="featured" value="1" {{ old('featured', $entry->featured) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                                <span style="font-size:12px;color:var(--cms-text-muted);">Featured</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <div style="display:flex;gap:10px;align-items:center;">
                <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:9px 18px;font-size:13px;font-weight:500;cursor:pointer;">Save Entry</button>
                <a href="{{ route('panel.entries.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:9px 18px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
                @if($entry->exists)
                <a href="{{ in_array($entry->status, ['draft','archived']) ? \Illuminate\Support\Facades\URL::temporarySignedRoute('site.show', now()->addHours(24), [$entry->contentType->slug, $entry->slug]) : route('site.show', [$entry->contentType->slug, $entry->slug]) }}"
                   target="_blank" style="margin-left:auto;font-size:12px;color:var(--cms-accent);text-decoration:none;">Preview on frontend →</a>
                @endif
            </div>
        </form>
    </div>
</x-cms-layout>
