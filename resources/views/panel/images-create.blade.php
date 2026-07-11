<x-cms-layout title="Upload Image">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">Upload Image</h1>

    <form method="POST" action="{{ route('panel.images.store') }}" enctype="multipart/form-data" style="max-width:520px;">
        @csrf
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">File</label>
                <input type="file" name="file" accept="image/*,.svg" required
                       style="width:100%;font-family:'Inter',sans-serif;font-size:13px;color:var(--cms-input-text);">
                @error('file')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Title</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Alt text</label>
                <input type="text" name="alt" value="{{ old('alt') }}"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Caption</label>
                <textarea name="caption" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('caption') }}</textarea>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Credit</label>
                <input type="text" name="credit" value="{{ old('credit') }}"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="qr_code" value="1" style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">This is a QR code</span>
            </label>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Upload</button>
            <a href="{{ route('panel.images.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
