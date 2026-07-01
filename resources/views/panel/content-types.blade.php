<x-cms-layout title="Content Type Manager">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">Content Types</h1>
        <a href="{{ route('panel.content-types.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+ New Content Type</a>
    </div>
    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="background:var(--cms-table-header-bg);">
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Name</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Table</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">React Island</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Listed</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($contentTypes as $ct)
                <tr style="border-top:0.5px solid var(--cms-table-border);">
                    <td style="padding:10px 16px;font-size:13px;color:var(--cms-text-primary);">{{ $ct->name }}</td>
                    <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">{{ $ct->table_target }}</td>
                    <td style="padding:10px 16px;font-size:12px;">{{ $ct->react_island ? '✓' : '—' }}</td>
                    <td style="padding:10px 16px;font-size:12px;">{{ $ct->listed ? '✓' : '—' }}</td>
                    <td style="padding:10px 16px;text-align:right;white-space:nowrap;">
                        <a href="{{ route('panel.content-types.edit', $ct) }}" style="font-size:12px;color:var(--cms-accent);text-decoration:none;margin-right:12px;">Edit</a>
                        <form method="POST" action="{{ route('panel.content-types.destroy', $ct) }}" style="display:inline;" onsubmit="return confirm('Delete this content type?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No content types yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-cms-layout>
