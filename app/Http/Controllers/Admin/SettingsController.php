<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
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
        $partners     = Partner::orderBy('sort_order')->get();
        $plans        = Plan::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);

        return view('app.admin.settings.index', compact('grouped', 'defaults', 'videos', 'testimonials', 'beforeAfters', 'partners', 'plans'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Ensure the per-request static cache is invalidated so the very next
        // read in this same process gets a fresh value from the DB.
        Setting::flushCache();

        $maintenanceEnabled = ($settings['maintenance_mode_enabled'] ?? '0') === '1';

        $message = $maintenanceEnabled
            ? 'وضع الصيانة مُفعَّل الآن — الموقع غير مرئي للزوار فوراً'
            : 'تم حفظ الإعدادات بنجاح';

        return back()->with('success', $message);
    }
}
