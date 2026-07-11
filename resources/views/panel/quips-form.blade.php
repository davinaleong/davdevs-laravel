<x-cms-layout title="{{ $quip->exists ? 'Edit Quip' : 'New Quip' }}">
    <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 20px;">{{ $quip->exists ? 'Edit Quip' : 'New Quip' }}</h1>
    <form method="POST" action="{{ $quip->exists ? route('panel.quips.update', $quip) : route('panel.quips.store') }}" style="max-width:520px;" x-data="{ variant: '{{ old('variant', $quip->variant ?? 'qa') }}' }">
        @csrf @if($quip->exists) @method('PUT') @endif
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;padding:20px;display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Variant</label>
                <select name="variant" x-model="variant" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">
                    <option value="qa">Q/A (question, then timed reveal)</option>
                    <option value="statement">Statement (shown directly)</option>
                </select>
            </div>
            <div x-show="variant === 'qa'">
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">Question</label>
                <textarea name="question" rows="2" style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('question', $quip->question) }}</textarea>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);" x-text="variant === 'qa' ? 'Answer' : 'Statement'"></label>
                <textarea name="punchline" rows="3" required style="width:100%;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-size:13px;color:var(--cms-input-text);">{{ old('punchline', $quip->punchline) }}</textarea>
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="active" value="1" {{ old('active', $quip->active ?? true) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--cms-accent);">
                <span style="font-size:12px;color:var(--cms-text-muted);">Active</span>
            </label>
        </div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-size:13px;font-weight:500;cursor:pointer;">Save</button>
            <a href="{{ route('panel.quips.index') }}" style="background:var(--cms-btn-secondary-bg);border:1px solid var(--cms-btn-secondary-border);color:var(--cms-btn-secondary-text);padding:10px 20px;border-radius:5px;font-size:13px;text-decoration:none;">Cancel</a>
        </div>
    </form>
</x-cms-layout>
