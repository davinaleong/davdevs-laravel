<?php

namespace App\View\Components;

use App\Models\Image;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;

class SiteLayout extends Component
{
    public array $settings;
    public ?string $faviconUrl;

    public function __construct(
        public string $title = 'Dav/Devs',
        public ?string $description = null,
        public ?string $ogImage = null,
        public ?string $jsonLd = null,
    ) {
        $this->settings = Cache::remember('settings', 3600, function () {
            return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        });

        $faviconImageId = $this->settings['favicon_image_id'] ?? null;
        $this->faviconUrl = $faviconImageId
            ? Cache::remember("favicon_url_{$faviconImageId}", 3600, fn () => Image::find($faviconImageId)?->url)
            : null;
    }

    public function render(): View
    {
        return view('layouts.site');
    }
}
