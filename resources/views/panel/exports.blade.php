<x-cms-layout title="Data Export">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">Data Export</h1>
        <form method="POST" action="{{ route('panel.exports.store') }}">
            @csrf
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;border:none;cursor:pointer;">+ New Export</button>
        </form>
    </div>

    <p style="font-size:12px;color:var(--cms-text-muted);margin-bottom:20px;">
        Exports a ZIP with posts.json, publications.json, images.json, links.json, youtube_embeds.json, quips.json, settings.json, and logs.json (last 90 days). Runs as a queued job; download links expire after 24 hours.
    </p>

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr style="background:var(--cms-table-header-bg);">
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Created</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Status</th>
                <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:var(--cms-text-muted);text-transform:uppercase;">Expires</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($jobs as $job)
                <tr style="border-top:0.5px solid var(--cms-table-border);">
                    <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-primary);">{{ $job->created_at->format('Y-m-d H:i') }}</td>
                    <td style="padding:10px 16px;">
                        <span style="font-family:'JetBrains Mono',monospace;font-size:10px;padding:2px 7px;border-radius:3px;background:var(--cms-bg-surface-2);color:var(--cms-text-muted);">{{ $job->status }}</span>
                    </td>
                    <td style="padding:10px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-faint);">{{ $job->expires_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td style="padding:10px 16px;text-align:right;">
                        @if($job->status === 'complete' && $job->download_url && (!$job->expires_at || $job->expires_at->isFuture()))
                        <a href="{{ route('panel.exports.download', $job) }}" style="font-size:12px;color:var(--cms-accent);text-decoration:none;">Download</a>
                        @elseif($job->status === 'complete')
                        <span style="font-size:11px;color:var(--cms-text-muted);">Expired</span>
                        @elseif($job->status === 'failed')
                        <span style="font-size:11px;color:var(--cms-error);">{{ $job->error_message }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No exports yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-cms-layout>
