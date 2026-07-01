<x-cms-layout title="Add Video">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">Add YouTube Video</h1>

    <form method="POST" action="{{ route('panel.video-embeds.store') }}" style="max-width:520px;">
        @csrf
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;">
            <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Video ID or URL</label>
            <input type="text" name="input" value="{{ old('input') }}" placeholder="https://youtube.com/watch?v=..." required
                   style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
            <p style="font-size:11px;color:var(--cms-text-muted);margin-top:8px;">Title, channel, and thumbnail are auto-fetched via YouTube's oEmbed API.</p>
            @error('input')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:8px 16px;font-size:13px;font-weight:500;cursor:pointer;">Add Video</button>
            <a href="{{ route('panel.video-embeds.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:8px 16px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
