<x-cms-layout title="Settings">
    <div style="margin-bottom:24px;">
        <h1 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 4px;">Settings</h1>
        <p style="font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--cms-text-muted);">Site-wide configuration, grouped by area</p>
    </div>

    @php
    $groupLabels = [
        'display'    => 'Display',
        'header'     => 'Header',
        'footer'     => 'Footer',
        'lighthouse' => 'Lighthouse',
        'publications' => 'Publications',
        'api'        => 'Headless API',
        'ai'         => 'AI Provider',
    ];
    $keyLabels = [
        'frontend_date_format' => 'Frontend date format',
        'cms_date_format'      => 'CMS date format',
        'brand_name'           => 'Brand name',
        'brand_image_id'       => 'Brand image ID',
        'copyright_text'       => 'Copyright text (supports {year})',
        'lh_show_performance'   => 'Show Performance badge',
        'lh_show_accessibility' => 'Show Accessibility badge',
        'lh_show_seo'           => 'Show SEO badge',
        'lh_show_best_practices'=> 'Show Best Practices badge',
        'lh_performance'        => 'Performance score',
        'lh_accessibility'      => 'Accessibility score',
        'lh_seo'                => 'SEO score',
        'lh_best_practices'     => 'Best Practices score',
        'publication_ebook_show_price' => 'Show price on E-Book listing (/ebooks)',
        'api_enabled'           => 'Enable public API',
        'ai_api_key'            => 'OpenAI API key',
        'ai_model'              => 'Model',
    ];
    @endphp

    <form method="POST" action="{{ route('panel.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach($settings as $group => $items)
        <div style="background:var(--cms-bg-surface);border:1px solid var(--cms-border);border-radius:8px;margin-bottom:20px;overflow:hidden;">
            <div style="padding:14px 20px;border-bottom:1px solid var(--cms-border);font-family:'Inter',sans-serif;font-size:13px;font-weight:600;color:var(--cms-text-primary);">
                {{ $groupLabels[$group] ?? ucfirst($group) }}
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
                @foreach($items as $setting)
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;margin-bottom:6px;color:var(--cms-text-secondary);">
                        {{ $keyLabels[$setting->key] ?? $setting->key }}
                    </label>

                    @if($setting->key === 'ai_api_key')
                        <input type="password" name="{{ $setting->key }}" placeholder="{{ $setting->value ? '••••••••••••••••  (set — leave blank to keep)' : 'sk-...' }}" autocomplete="off"
                               style="width:100%;max-width:420px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
                        <p style="font-size:11px;color:var(--cms-text-muted);margin-top:4px;">Stored encrypted. Never displayed once set — leave blank on save to keep the current key.</p>
                    @elseif($setting->cast === 'boolean')
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="checkbox" name="{{ $setting->key }}" value="1" {{ $setting->getTypedValue() ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--cms-accent);">
                            <span style="font-size:12px;color:var(--cms-text-muted);">Enabled</span>
                        </label>
                    @elseif($setting->cast === 'integer')
                        <input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}"
                               style="width:140px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--cms-input-text);">
                    @else
                        <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                               style="width:100%;max-width:420px;box-sizing:border-box;background:var(--cms-input-bg);border:1px solid var(--cms-input-border);border-radius:5px;padding:8px 12px;font-family:'Inter',sans-serif;font-size:13px;color:var(--cms-input-text);">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div style="display:flex;gap:10px;">
            <button type="submit" style="background:var(--cms-btn-primary-bg);color:var(--cms-btn-primary-text);border:none;border-radius:5px;padding:10px 20px;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;cursor:pointer;">Save Settings</button>
        </div>
    </form>

    <form method="POST" action="{{ route('panel.settings.flush-cache') }}" style="margin-top:12px;">
        @csrf
        <button type="submit" style="background:var(--cms-btn-secondary-bg);color:var(--cms-btn-secondary-text);border:1px solid var(--cms-btn-secondary-border);border-radius:5px;padding:7px 14px;font-family:'Inter',sans-serif;font-size:12px;cursor:pointer;">Flush Settings Cache</button>
    </form>
</x-cms-layout>
