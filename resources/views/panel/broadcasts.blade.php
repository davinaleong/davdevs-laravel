<x-cms-layout title="Broadcast History">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <h1
            style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">
            Broadcast History</h1>
    </div>

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
