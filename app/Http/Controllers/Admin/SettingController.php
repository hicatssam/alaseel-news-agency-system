<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
        // Bust the layout cache so the new values appear immediately
        cache()->forget('site_settings');
        ActivityLog::log('update','settings','Updated site settings');
        return back()->with('success','تم حفظ الإعدادات بنجاح.');
    }
}
