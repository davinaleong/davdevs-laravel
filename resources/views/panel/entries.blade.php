<x-cms-layout title="Entries">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">Entries</h1>
        <a href="{{ route('panel.entries.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+ New Entry</a>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title/excerpt/body..."
               style="flex:1;min-width:200px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
        <select name="type" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All types</option>
            @foreach($contentTypes as $ct)
            <option value="{{ $ct->id }}" {{ request('type') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
            @endforeach
        </select>
        <select name="status" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All statuses</option>
            @foreach(['draft','private','published','archived'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="category" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All categories</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="sort" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="newest" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Newest</option>
            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
            <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>A–Z</option>
        </select>
        <button type="submit" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);border-radius:5px;padding:7px 14px;font-size:12px;cursor:pointer;">Filter</button>
    </form>

    <form method="POST" action="{{ route('panel.entries.bulk') }}" x-data="{ selected: [] }">
        @csrf
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;" x-show="selected.length > 0" x-cloak>
            <span style="font-size:12px;color:var(--cms-text-muted);" x-text="selected.length + ' selected'"></span>
            <button type="submit" name="action" value="publish" style="background:var(--cms-status-published-bg);color:var(--cms-status-published);border:none;border-radius:5px;padding:5px 10px;font-size:11px;cursor:pointer;">Publish</button>
            <button type="submit" name="action" value="archive" style="background:var(--cms-status-archived-bg);color:var(--cms-status-archived);border:none;border-radius:5px;padding:5px 10px;font-size:11px;cursor:pointer;">Archive</button>
            <button type="submit" name="action" value="delete" onclick="return confirm('Delete selected entries?')" style="background:var(--cms-error-bg);color:var(--cms-error);border:none;border-radius:5px;padding:5px 10px;font-size:11px;cursor:pointer;">Delete</button>
        </div>

        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr style="background:var(--cms-table-header-bg);">
                    <th style="padding:10px 12px;width:30px;"><input type="checkbox" @change="selected = $event.target.checked ? [{{ $entries->pluck('id')->implode(',') }}] : []"></th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Title</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Type</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Status</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Published</th>
                    <th></th>
                </tr></thead>
                <tbody>
                @forelse($entries as $entry)
                    <tr style="border-top:0.5px solid var(--cms-table-border);">
                        <td style="padding:10px 12px;">
                            <input type="checkbox" name="ids[]" value="{{ $entry->id }}" x-model="selected">
                        </td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">
                            {{ $entry->title }}
                            @if($entry->featured)<span style="color:var(--cms-accent);margin-left:6px;" title="Featured">★</span>@endif
                        </td>
                        <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);">{{ $entry->contentType->name }}</td>
                        <td style="padding:10px 16px;">
                            @php
                            $statusColors = [
                                'published' => ['bg' => 'var(--cms-status-published-bg)', 'text' => 'var(--cms-status-published)'],
                                'draft'     => ['bg' => 'var(--cms-status-draft-bg)', 'text' => 'var(--cms-status-draft)'],
                                'private'   => ['bg' => 'var(--cms-status-private-bg)', 'text' => 'var(--cms-status-private)'],
                                'archived'  => ['bg' => 'var(--cms-status-archived-bg)', 'text' => 'var(--cms-status-archived)'],
                            ][$entry->status];
                            @endphp
                            <span style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:{{ $statusColors['bg'] }};color:{{ $statusColors['text'] }};">{{ $entry->status }}</span>
                        </td>
                        <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-faint);">{{ $entry->published_at?->format('Y-m-d') ?? '—' }}</td>
                        <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                            <a href="{{ route('panel.entries.edit', $entry) }}" style="font-size:12px;color:var(--cms-accent);text-decoration:none;margin-right:10px;">Edit</a>
                            <button type="button" @click.stop="$el.closest('td').querySelector('.dup-form').requestSubmit()" style="background:none;border:none;color:var(--cms-text-muted);font-size:12px;cursor:pointer;margin-right:10px;">Duplicate</button>
                            <button type="button" @click.stop="if(confirm('Delete this entry?')) $el.closest('td').querySelector('.del-form').requestSubmit()" style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                            <form class="dup-form" method="POST" action="{{ route('panel.entries.duplicate', $entry) }}" style="display:none;">@csrf</form>
                            <form class="del-form" method="POST" action="{{ route('panel.entries.destroy', $entry) }}" style="display:none;">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No entries found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div style="margin-top:20px;">{{ $entries->links() }}</div>
</x-cms-layout>
