<x-cms-layout title="Activity Logs">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">Activity Logs</h1>

    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search message..."
               style="flex:1;min-width:200px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
        <select name="channel" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All channels</option>
            @foreach($channels as $c)
            <option value="{{ $c }}" {{ request('channel') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
        <select name="level" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
            <option value="">All levels</option>
            @foreach(['debug','info','warning','error','critical'] as $l)
            <option value="{{ $l }}" {{ request('level') === $l ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
        <input type="date" name="to" value="{{ request('to') }}" style="background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:7px 10px;font-size:12px;color:var(--cms-input-text);">
        <button type="submit" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);border-radius:5px;padding:7px 14px;font-size:12px;cursor:pointer;">Filter</button>
    </form>

    @php
    $levelColors = [
        'debug'    => ['c' => 'var(--cms-log-debug)', 'bg' => 'var(--cms-log-debug-bg)'],
        'info'     => ['c' => 'var(--cms-log-info)', 'bg' => 'var(--cms-log-info-bg)'],
        'warning'  => ['c' => 'var(--cms-log-warning)', 'bg' => 'var(--cms-log-warning-bg)'],
        'error'    => ['c' => 'var(--cms-log-error)', 'bg' => 'var(--cms-log-error-bg)'],
        'critical' => ['c' => 'var(--cms-log-critical)', 'bg' => 'var(--cms-log-critical-bg)'],
    ];
    @endphp

    <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
        @forelse($logs as $log)
        @php $lc = $levelColors[$log->level] ?? $levelColors['info']; @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:10px 20px;border-top:0.5px solid var(--cms-table-border);background:{{ in_array($log->level, ['warning','error','critical']) ? $lc['bg'] : 'transparent' }};">
            <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);white-space:nowrap;">[{{ $log->channel }}]</span>
            <span style="font-family:'JetBrains Mono',monospace;font-size:9px;padding:2px 6px;border-radius:3px;color:{{ $lc['c'] }};background:{{ $lc['bg'] }};white-space:nowrap;">{{ $log->level }}</span>
            <span style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-primary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->message }}</span>
            <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-faint);white-space:nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
        </div>
        @empty
        <div style="padding:24px;text-align:center;color:var(--cms-text-muted);font-size:13px;">No logs found.</div>
        @endforelse
    </div>

    <div style="margin-top:20px;">{{ $logs->links() }}</div>
</x-cms-layout>
