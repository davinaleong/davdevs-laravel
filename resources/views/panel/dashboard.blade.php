<x-cms-layout title="Dashboard">
    <div style="margin-bottom:24px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 4px;">Dashboard</h1>
        <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">~/dav/devs cms — overview</p>
    </div>

    {{-- Stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:32px;">
        @foreach([
            ['label' => 'Total Entries',   'value' => $stats['entries'],      'color' => 'var(--cms-accent)'],
            ['label' => 'Publications',     'value' => $stats['publications'], 'color' => '#A07ADA'],
            ['label' => 'Published',        'value' => $stats['published'],    'color' => 'var(--cms-status-published)'],
            ['label' => 'Drafts',           'value' => $stats['drafts'],       'color' => 'var(--cms-status-draft)'],
        ] as $card)
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;">
            <div style="font-family:'Inter',sans-serif;font-size:28px;font-weight:600;color:{{ $card['color'] }};line-height:1;">{{ $card['value'] }}</div>
            <div style="font-family:'Inter',sans-serif;font-size:12px;color:var(--cms-text-muted);margin-top:4px;">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:32px;">
        <a href="{{ route('panel.entries.create') }}" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;text-decoration:none;">+ New Entry</a>
        <a href="{{ route('panel.publications.create') }}" style="background:var(--cms-btn-secondary-bg);color:var(--cms-btn-secondary-text);padding:7px 14px;border-radius:5px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;text-decoration:none;border:1px solid var(--cms-btn-secondary-border);">+ New Publication</a>
        <a href="{{ route('panel.images.index') }}" style="background:var(--cms-btn-secondary-bg);color:var(--cms-btn-secondary-text);padding:7px 14px;border-radius:5px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;text-decoration:none;border:1px solid var(--cms-btn-secondary-border);">Image Manager</a>
    </div>

    {{-- Recent logs --}}
    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        <div style="padding:14px 20px;border-bottom:1px solid var(--cms-border);font-family:'Inter',sans-serif;font-size:13px;font-weight:600;color:var(--cms-text-primary);">Recent Activity</div>
        @if($recentLogs->isEmpty())
            <div style="padding:24px 20px;font-family:'Inter',sans-serif;font-size:13px;color:var(--cms-text-muted);text-align:center;">No activity yet.</div>
        @else
            @foreach($recentLogs as $log)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 20px;border-bottom:0.5px solid var(--cms-table-border);">
                <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);white-space:nowrap;">[{{ $log->channel }}]</span>
                <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-primary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->message }}</span>
                <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-faint);white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        @endif
    </div>
</x-cms-layout>
