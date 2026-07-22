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
            // Region-scoped contact data (contact_eg_*/contact_intl_*) is
            // shown across the whole site — an empty submission never blanks
            // it out. Every other setting keeps its existing "clear to reset
            // to default" behavior (e.g. maintenance_message).
            if (str_starts_with($key, 'contact_eg_') || str_starts_with($key, 'contact_intl_')) {
                $existing = Setting::get($key, '');
                if ($value === '' && $existing !== '') {
                    $value = $existing;
                }
            }

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
