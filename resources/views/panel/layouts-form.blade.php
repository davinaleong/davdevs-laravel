<x-cms-layout title="{{ $layout->exists ? 'Edit Layout' : 'New Layout' }}">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">{{ $layout->exists ? 'Edit Layout' : 'New Layout' }}</h1>
    <form method="POST" action="{{ $layout->exists ? route('panel.layouts.update', $layout) : route('panel.layouts.store') }}" style="max-width:520px;">
        @csrf @if($layout->exists) @method('PUT') @endif
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Name</label>
                <input type="text" name="name" value="{{ old('name', $layout->name) }}" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Blade Component</label>
                <input type="text" name="blade_component" value="{{ old('blade_component', $layout->blade_component) }}" placeholder="layouts.standard" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
                @error('blade_component')<p style="color:var(--cms-error);font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Description</label>
                <textarea name="description" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('description', $layout->description) }}</textarea>
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="active" value="1" {{ old('active', $layout->active ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Active</span>
            </label>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:8px 16px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
            <a href="{{ route('panel.layouts.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:8px 16px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
