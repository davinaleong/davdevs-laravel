<x-cms-layout title="Link Manager">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1
            style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">
            Link Manager</h1>
        <a href="{{ route('panel.links.create') }}"
            style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+
            New Link</a>
    </div>

    <form method="GET" style="margin-bottom:16px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search links..."
            style="max-width:320px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
    </form>

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:var(--cms-table-header-bg);">
                    <th
                        style="text-align:left;padding:10px 16px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                        ID</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                        Label</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                        URL</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                        Category</th>
                    <th
                        style="text-align:left;padding:10px 16px;font-family:'Inter',sans-serif;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;letter-spacing:0.06em;">
                        Status</th>
                    <th style="padding:10px 16px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                    <tr style="border-top:0.5px solid var(--cms-table-border);">
                        <td
                            style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">
                            {{ $link->id }}</td>
                        <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">{{ $link->label }}
                        </td>
                        <td
                            style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $link->url }}</td>
                        <td style="padding:10px 16px;">
                            <span
                                style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-bg-surface-2);color:var(--cms-text-muted);">{{ $link->category }}</span>
                        </td>
                        <td style="padding:10px 16px;">
                            @if ($link->active)
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-status-published-bg);color:var(--cms-status-published);">active</span>
                            @else
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-status-draft-bg);color:var(--cms-status-draft);">inactive</span>
                            @endif
                        </td>
                        <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                            <a href="{{ route('panel.links.edit', $link) }}"
                                style="font-size:12px;color:var(--cms-accent);text-decoration:none;margin-right:12px;">Edit</a>
                            <form method="POST" action="{{ route('panel.links.destroy', $link) }}"
                                style="display:inline;" onsubmit="return confirm('Delete this link?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No links
                            yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-cms-layout>
