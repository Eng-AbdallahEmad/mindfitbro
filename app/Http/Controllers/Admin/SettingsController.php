<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\BeforeAfter;
use App\Models\Testimonial;
use App\Models\Video;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $grouped      = Setting::allGrouped();
        $defaults     = Setting::defaultValues();
        $videos       = Video::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();
        $beforeAfters = BeforeAfter::orderBy('sort_order')->get();
        $plans        = Plan::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);

        return view('app.admin.settings.index', compact('grouped', 'defaults', 'videos', 'testimonials', 'beforeAfters', 'plans'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
