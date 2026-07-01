<x-cms-layout title="Edit Video">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">Edit Video</h1>

    <div style="display:flex;gap:24px;max-width:900px;">
        <img src="{{ $embed->thumbnail_url }}" alt="" style="flex:0 0 240px;border-radius:8px;">
        <form method="POST" action="{{ route('panel.video-embeds.update', $embed) }}" style="flex:1;">
            @csrf @method('PUT')
            <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Title</label>
                    <input type="text" name="title" value="{{ $embed->title }}" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Channel</label>
                    <input type="text" name="channel_name" value="{{ $embed->channel_name }}" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Description</label>
                    <textarea name="description" rows="3" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ $embed->description }}</textarea>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:8px 16px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
                <a href="{{ route('panel.video-embeds.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:8px 16px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</x-cms-layout>
