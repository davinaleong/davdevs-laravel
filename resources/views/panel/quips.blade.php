<x-cms-layout title="Quip Manager">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">Jokes (Quips)</h1>
        <a href="{{ route('panel.quips.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+ New Quip</a>
    </div>

    <form method="GET" style="margin-bottom:16px;display:flex;gap:8px;">
        <select name="variant" onchange="this.form.submit()" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All variants</option>
            <option value="qa" {{ request('variant') === 'qa' ? 'selected' : '' }}>Q/A</option>
            <option value="statement" {{ request('variant') === 'statement' ? 'selected' : '' }}>Statement</option>
        </select>
    </form>

    <form method="POST" action="{{ route('panel.quips.bulk-toggle') }}" x-data="{ selected: [] }">
        @csrf
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;" x-show="selected.length > 0" x-cloak>
            <span style="font-size:12px;color:var(--cms-text-muted);" x-text="selected.length + ' selected'"></span>
            <button type="submit" name="active" value="1" style="background:var(--cms-status-published-bg);color:var(--cms-status-published);border:none;border-radius:5px;padding:5px 10px;font-size:11px;cursor:pointer;">Activate</button>
            <button type="submit" name="active" value="0" style="background:var(--cms-status-draft-bg);color:var(--cms-status-draft);border:none;border-radius:5px;padding:5px 10px;font-size:11px;cursor:pointer;">Deactivate</button>
        </div>

        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr style="background:var(--cms-table-header-bg);">
                    <th style="padding:10px 12px;width:30px;"></th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Variant</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Content</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Status</th>
                    <th></th>
                </tr></thead>
                <tbody>
                @forelse($quips as $quip)
                    <tr style="border-top:0.5px solid var(--cms-table-border);">
                        <td style="padding:10px 12px;"><input type="checkbox" name="ids[]" value="{{ $quip->id }}" x-model="selected"></td>
                        <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);">{{ $quip->variant }}</td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            @if($quip->question)<strong>{{ $quip->question }}</strong> — @endif{{ $quip->punchline }}
                        </td>
                        <td style="padding:10px 16px;">
                            <span style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:{{ $quip->active ? 'var(--cms-status-published-bg)' : 'var(--cms-status-draft-bg)' }};color:{{ $quip->active ? 'var(--cms-status-published)' : 'var(--cms-status-draft)' }};">{{ $quip->active ? 'active' : 'inactive' }}</span>
                        </td>
                        <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                            <a href="{{ route('panel.quips.edit', $quip) }}" style="font-size:12px;color:var(--cms-accent);text-decoration:none;margin-right:10px;">Edit</a>
                            <button type="button" @click="if(confirm('Delete?')) $el.nextElementSibling.requestSubmit()" style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                            <form method="POST" action="{{ route('panel.quips.destroy', $quip) }}" style="display:none;">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No quips yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div style="margin-top:20px;">{{ $quips->links() }}</div>
</x-cms-layout>
