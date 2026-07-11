<x-cms-layout title="{{ $contentType->exists ? 'Edit Content Type' : 'New Content Type' }}">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">{{ $contentType->exists ? 'Edit Content Type' : 'New Content Type' }}</h1>
    <form method="POST" action="{{ $contentType->exists ? route('panel.content-types.update', $contentType) : route('panel.content-types.store') }}" style="max-width:520px;">
        @csrf @if($contentType->exists) @method('PUT') @endif
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Name</label>
                <input type="text" name="name" value="{{ old('name', $contentType->name) }}" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Table Target</label>
                <select name="table_target" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                    <option value="entries" {{ old('table_target', $contentType->table_target) === 'entries' ? 'selected' : '' }}>Entries (free)</option>
                    <option value="publications" {{ old('table_target', $contentType->table_target) === 'publications' ? 'selected' : '' }}>Publications (paid)</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Description</label>
                <textarea name="description" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('description', $contentType->description) }}</textarea>
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="react_island" value="1" {{ old('react_island', $contentType->react_island) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Loads a React island on detail page</span>
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="listed" value="1" {{ old('listed', $contentType->listed ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Listed (shown in homepage/sitemap/listings)</span>
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="show_price" value="1" {{ old('show_price', $contentType->show_price ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Show price on frontend (publications only)</span>
            </label>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
            <a href="{{ route('panel.content-types.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
