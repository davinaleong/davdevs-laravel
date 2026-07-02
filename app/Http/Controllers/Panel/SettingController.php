<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('panel.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                continue;
            }

            if ($setting->cast === 'boolean') {
                $value = $request->has($key) ? '1' : '0';
            }

            $setting->update(['value' => $value]);
        }

        // Handle unchecked booleans (not present in request at all)
        foreach (Setting::where('cast', 'boolean')->get() as $boolSetting) {
            if (!$request->has($boolSetting->key)) {
                $boolSetting->update(['value' => '0']);
            }
        }

        Cache::forget('settings');

        ActivityLog::create(['channel' => 'cms', 'level' => 'info', 'message' => 'Settings saved by '.auth()->user()->email]);

        return redirect()->route('panel.settings.index')->with('success', 'Settings saved.');
    }

    public function flushCache()
    {
        Cache::forget('settings');

        return redirect()->route('panel.settings.index')->with('success', 'Cache flushed.');
    }
}
