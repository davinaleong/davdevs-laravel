<x-cms-layout title="YouTube Embed Manager">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h1
            style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0;">
            Video Embeds</h1>
        <a href="{{ route('panel.video-embeds.create') }}"
            style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);padding:7px 14px;border-radius:5px;font-size:13px;font-weight:500;text-decoration:none;">+
            Add Video</a>
    </div>

    <form method="GET" style="margin-bottom:16px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search videos..."
            style="max-width:320px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
    </form>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
        @forelse($embeds as $embed)
            <div
                style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;overflow:hidden;">
                <img src="{{ $embed->thumbnail_url }}" alt=""
                    style="width:100%;aspect-ratio:16/9;object-fit:cover;">
                <div style="padding:12px;">
                    <div
                        style="font-size:13px;font-weight:500;color:var(--cms-text-primary);margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $embed->title }}</div>
                    <div
                        style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);margin-bottom:4px;">
                        {{ $embed->channel_name }}</div>
                    <div
                        style="font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--cms-text-muted);margin-bottom:10px;">
                        ID: {{ $embed->id }}</div>
                    <div style="display:flex;gap:8px;">
                        <a href="{{ route('panel.video-embeds.edit', $embed) }}"
                            style="font-size:12px;color:var(--cms-accent);text-decoration:none;">Edit</a>
                        <form method="POST" action="{{ route('panel.video-embeds.destroy', $embed) }}"
                            onsubmit="return confirm('Remove this video?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="background:none;border:none;color:var(--cms-error);font-size:12px;cursor:pointer;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p style="grid-column:1/-1;text-align:center;color:var(--cms-text-muted);font-size:13px;padding:40px;">No
                videos yet.</p>
        @endforelse
    </div>

    <div style="margin-top:20px;">{{ $embeds->links() }}</div>
</x-cms-layout>
