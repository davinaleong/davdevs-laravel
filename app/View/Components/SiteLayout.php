<?php

namespace App\View\Components;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;

class SiteLayout extends Component
{
    public array $settings;

    public function __construct(public string $title = 'Dav/Devs', public ?string $description = null)
    {
        $this->settings = Cache::remember('settings', 3600, function () {
            return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        });
    }

    public function render(): View
    {
        return view('layouts.site');
    }
}
