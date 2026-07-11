<x-cms-layout title="{{ $tag->exists ? 'Edit Tag' : 'New Tag' }}">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">{{ $tag->exists ? 'Edit Tag' : 'New Tag' }}</h1>
    <form method="POST" action="{{ $tag->exists ? route('panel.tags.update', $tag) : route('panel.tags.store') }}" style="max-width:520px;">
        @csrf @if($tag->exists) @method('PUT') @endif
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Name</label>
                <input type="text" name="name" value="{{ old('name', $tag->name) }}" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Content Type (optional)</label>
                <select name="content_type_id" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                    <option value="">— cross-type —</option>
                    @foreach($contentTypes as $ct)
                    <option value="{{ $ct->id }}" {{ old('content_type_id', $tag->content_type_id) == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Scope</label>
                <select name="scope" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                    <option value="entries" {{ old('scope', $tag->scope) === 'entries' ? 'selected' : '' }}>Entries</option>
                    <option value="publications" {{ old('scope', $tag->scope) === 'publications' ? 'selected' : '' }}>Publications</option>
                    <option value="all" {{ old('scope', $tag->scope) === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
            <a href="{{ route('panel.tags.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
