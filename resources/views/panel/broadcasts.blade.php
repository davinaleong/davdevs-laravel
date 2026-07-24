<x-cms-layout title="Broadcasts">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h1
            style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">
            Broadcasts</h1>
    </div>

    {{-- Send Form --}}
    @if (count($enabledPlatforms) > 0)
    <div x-data="{
        entries: {{ Js::from($entries->map(fn($e) => ['id' => $e->id, 'title' => $e->title])->values()) }},
        publications: {{ Js::from($publications->map(fn($p) => ['id' => $p->id, 'title' => $p->title])->values()) }},
        type: '',
        id: '',
        texts: {{ Js::from(array_fill_keys($enabledPlatforms, '')) }},
        aiLoading: false,
        sending: false,
        error: null,
        success: null,
        corrections: [],
        get items() {
            if (this.type === 'entry') return this.entries;
            if (this.type === 'publication') return this.publications;
            return [];
        },
        async generate() {
            if (!this.type || !this.id) { this.error = 'Select a content item first.'; return; }
            this.aiLoading = true; this.error = null;
            try {
                const r = await fetch('{{ route('panel.ai.generate-broadcast') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ broadcastable_type: this.type, broadcastable_id: Number(this.id), platforms: Object.keys(this.texts) }),
                });
                const d = await r.json();
                if (!r.ok || d.error) { this.error = d.error || 'Generation failed.'; return; }
                for (const [p, t] of Object.entries(d.platforms || {})) {
                    if (p in this.texts) this.texts[p] = t;
                }
            } catch { this.error = 'Request failed.'; } finally { this.aiLoading = false; }
        },
        async send() {
            if (!this.type || !this.id) { this.error = 'Select a content item first.'; return; }
            const empty = Object.entries(this.texts).filter(([,t]) => !t.trim()).map(([p]) => p);
            if (empty.length) { this.error = 'Fill in text for: ' + empty.join(', '); return; }
            this.sending = true; this.error = null; this.success = null; this.corrections = [];
            try {
                const platformText = {};
                Object.entries(this.texts).forEach(([p, t]) => { platformText[p] = t; });
                const r = await fetch('{{ route('panel.broadcasts.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ broadcastable_type: this.type, broadcastable_id: Number(this.id), platform_text: platformText }),
                });
                const d = await r.json();
                if (!r.ok || !d.success) { this.error = d.message || d.error || 'Send failed.'; return; }
                this.corrections = Object.keys(d.corrections || {});
                for (const [p, t] of Object.entries(d.corrections || {})) {
                    if (p in this.texts) this.texts[p] = t;
                }
                this.success = 'Broadcast queued! It will post shortly.';
                this.type = ''; this.id = '';
                for (const p in this.texts) this.texts[p] = '';
            } catch { this.error = 'Request failed.'; } finally { this.sending = false; }
        },
    }" style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;margin-bottom:24px;">

        <div style="font-size:14px;font-weight:600;color:var(--cms-text-primary);margin-bottom:16px;">📡 Send Broadcast</div>

        {{-- Alerts --}}
        <div x-show="error" x-cloak x-text="error"
            style="background:#fee2e2;border:1px solid var(--cms-error);border-radius:5px;padding:10px 14px;font-size:12px;color:var(--cms-error);margin-bottom:12px;"></div>
        <div x-show="success" x-cloak x-text="success"
            style="background:#dcfce7;border:1px solid var(--cms-success);border-radius:5px;padding:10px 14px;font-size:12px;color:var(--cms-success);margin-bottom:12px;"></div>
        <div x-show="corrections.length > 0" x-cloak
            style="background:#fef9c3;border:1px solid var(--cms-warning);border-radius:5px;padding:10px 14px;font-size:12px;color:var(--cms-text-secondary);margin-bottom:12px;">
            AI auto-corrected: <span x-text="corrections.join(', ')"></span>
        </div>

        {{-- Content selectors --}}
        <div style="display:grid;grid-template-columns:160px 1fr;gap:12px;margin-bottom:16px;">
            <div>
                <label
                    style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Type</label>
                <select x-model="type" @change="id = ''"
                    style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 10px;font-size:13px;color:var(--cms-input-text);">
                    <option value="">— select —</option>
                    <option value="entry">Entry</option>
                    <option value="publication">Publication</option>
                </select>
            </div>
            <div>
                <label
                    style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Published
                    Item</label>
                <select x-model="id"
                    style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 10px;font-size:13px;color:var(--cms-input-text);">
                    <option value="">— select item —</option>
                    <template x-for="item in items" :key="item.id">
                        <option :value="item.id" x-text="item.title"></option>
                    </template>
                </select>
            </div>
        </div>

        {{-- Platform textareas --}}
        @foreach ($enabledPlatforms as $platform)
        <div style="margin-bottom:12px;">
            <label
                style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">{{ ucfirst($platform) }}</label>
            <textarea x-model="texts['{{ $platform }}']" rows="4"
                placeholder="Write your {{ ucfirst($platform) }} post here..."
                style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);resize:vertical;"></textarea>
        </div>
        @endforeach

        {{-- Action buttons --}}
        <div style="display:flex;gap:10px;margin-top:4px;">
            <button type="button" @click="generate"
                :disabled="aiLoading || !type || !id"
                :style="(aiLoading || !type || !id) ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;'"
                style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:9px 16px;border-radius:5px;font-size:13px;">
                <span x-text="aiLoading ? 'Generating...' : '✨ AI Generate All'"></span>
            </button>
            <button type="button" @click="send"
                :disabled="sending"
                :style="sending ? 'opacity:0.5;cursor:not-allowed;' : 'cursor:pointer;'"
                style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:9px 16px;font-size:13px;font-weight:500;">
                <span x-text="sending ? 'Checking & Sending...' : '📤 Send'"></span>
            </button>
        </div>
    </div>
    @endif

    {{-- History --}}
    <div style="font-size:13px;font-weight:600;color:var(--cms-text-secondary);margin-bottom:10px;">History</div>

    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;">
        <select name="platform" onchange="this.form.submit()"
            style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All platforms</option>
            @foreach (['linkedin', 'facebook', 'instagram', 'threads'] as $p)
                <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>
                    {{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
            style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All statuses</option>
            @foreach (['pending', 'sent', 'failed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </form>

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--cms-table-header-bg);">
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Content</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Platform</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Status</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Post URL</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($broadcasts as $b)
                    @php
                        $item = $b->broadcastable;
                        $statusColors = [
                            'pending' => 'var(--cms-warning)',
                            'sent' => 'var(--cms-success)',
                            'failed' => 'var(--cms-error)',
                        ];
                    @endphp
                    <tr style="border-top:0.5px solid var(--cms-table-border);">
                        <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">
                            {{ $item?->title ?? '(deleted)' }}
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--cms-text-muted);display:block;">
                                {{ class_basename($b->broadcastable_type) }} #{{ $b->broadcastable_id }}
                            </span>
                        </td>
                        <td style="padding:10px 16px;">
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-secondary);">{{ $b->platform }}</span>
                        </td>
                        <td style="padding:10px 16px;">
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-bg-surface-2);color:{{ $statusColors[$b->status] ?? 'var(--cms-text-muted)' }};">
                                {{ $b->status }}
                            </span>
                            @if ($b->error)
                                <span style="font-size:11px;color:var(--cms-error);display:block;margin-top:2px;"
                                    title="{{ $b->error }}">{{ Str::limit($b->error, 60) }}</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;font-size:12px;">
                            @if ($b->post_url)
                                <a href="{{ $b->post_url }}" target="_blank"
                                    style="color:var(--cms-accent);text-decoration:none;">View →</a>
                            @else
                                <span style="color:var(--cms-text-muted);">—</span>
                            @endif
                        </td>
                        <td
                            style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);">
                            {{ $b->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No
                            broadcasts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $broadcasts->links() }}</div>
</x-cms-layout>

        <select name="platform" onchange="this.form.submit()"
            style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All platforms</option>
            @foreach (['linkedin', 'facebook', 'instagram', 'threads'] as $p)
                <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>
                    {{ ucfirst($p) }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
            style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All statuses</option>
            @foreach (['pending', 'sent', 'failed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </form>

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--cms-table-header-bg);">
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Content</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Platform</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Status</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Post URL</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">
                        Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($broadcasts as $b)
                    @php
                        $item = $b->broadcastable;
                        $statusColors = [
                            'pending' => 'var(--cms-warning)',
                            'sent' => 'var(--cms-success)',
                            'failed' => 'var(--cms-error)',
                        ];
                    @endphp
                    <tr style="border-top:0.5px solid var(--cms-table-border);">
                        <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">
                            {{ $item?->title ?? '(deleted)' }}
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:9px;color:var(--cms-text-muted);display:block;">
                                {{ class_basename($b->broadcastable_type) }} #{{ $b->broadcastable_id }}
                            </span>
                        </td>
                        <td style="padding:10px 16px;">
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-secondary);">{{ $b->platform }}</span>
                        </td>
                        <td style="padding:10px 16px;">
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-bg-surface-2);color:{{ $statusColors[$b->status] ?? 'var(--cms-text-muted)' }};">
                                {{ $b->status }}
                            </span>
                            @if ($b->error)
                                <span style="font-size:11px;color:var(--cms-error);display:block;margin-top:2px;"
                                    title="{{ $b->error }}">{{ Str::limit($b->error, 60) }}</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;font-size:12px;">
                            @if ($b->post_url)
                                <a href="{{ $b->post_url }}" target="_blank"
                                    style="color:var(--cms-accent);text-decoration:none;">View →</a>
                            @else
                                <span style="color:var(--cms-text-muted);">—</span>
                            @endif
                        </td>
                        <td
                            style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);">
                            {{ $b->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No
                            broadcasts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $broadcasts->links() }}</div>
</x-cms-layout>
