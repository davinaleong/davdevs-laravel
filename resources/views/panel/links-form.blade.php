<x-cms-layout title="{{ $link->exists ? 'Edit Link' : 'New Link' }}">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">{{ $link->exists ? 'Edit Link' : 'New Link' }}</h1>

    <form method="POST" action="{{ $link->exists ? route('panel.links.update', $link) : route('panel.links.store') }}" style="max-width:520px;">
        @csrf
        @if($link->exists) @method('PUT') @endif
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Label</label>
                <input type="text" name="label" value="{{ old('label', $link->label) }}" required
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">URL</label>
                <input type="text" name="url" value="{{ old('url', $link->url) }}" required
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Target</label>
                    <select name="target" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        <option value="_self" {{ old('target', $link->target) === '_self' ? 'selected' : '' }}>Same tab</option>
                        <option value="_blank" {{ old('target', $link->target) === '_blank' ? 'selected' : '' }}>New tab</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Category</label>
                    <select name="category" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                        <option value="general" {{ old('category', $link->category) === 'general' ? 'selected' : '' }}>General</option>
                        <option value="social" {{ old('category', $link->category) === 'social' ? 'selected' : '' }}>Social</option>
                        <option value="nav" {{ old('category', $link->category) === 'nav' ? 'selected' : '' }}>Nav</option>
                    </select>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Icon class (Tabler)</label>
                <input type="text" name="icon_class" value="{{ old('icon_class', $link->icon_class) }}" placeholder="ti-brand-linkedin"
                       style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Description</label>
                <textarea name="description" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('description', $link->description) }}</textarea>
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="active" value="1" {{ old('active', $link->active ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Active</span>
            </label>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:8px 16px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
            <a href="{{ route('panel.links.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:8px 16px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
