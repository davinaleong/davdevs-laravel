<x-cms-layout title="Publications">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">Publications</h1>
        <a href="{{ route('panel.publications.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+ New Publication</a>
    </div>

    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
               style="flex:1;max-width:280px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
        <select name="status" onchange="this.form.submit()" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All statuses</option>
            @foreach(['draft','private','published','archived'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="background:var(--cms-table-header-bg);">
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Title</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Status</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Bundle</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($publications as $pub)
                <tr style="border-top:0.5px solid var(--cms-table-border);">
                    <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">{{ $pub->title }}</td>
                    <td style="padding:10px 16px;">
                        <span style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-bg-surface-2);color:var(--cms-text-muted);">{{ $pub->status }}</span>
                    </td>
                    <td style="padding:10px 16px;font-size:12px;">{{ $pub->bundle ? '✓' : '—' }}</td>
                    <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                        <a href="{{ route('panel.publications.edit', $pub) }}" style="font-size:12px;color:var(--cms-accent);text-decoration:none;margin-right:10px;">Edit</a>
                        <form method="POST" action="{{ route('panel.publications.destroy', $pub) }}" style="display:inline;" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No publications yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px;">{{ $publications->links() }}</div>
</x-cms-layout>
