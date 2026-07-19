<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Services\Web\SeasonService;
use Illuminate\Http\Request;

class SeasonsController extends Controller
{
    public function index()
    {
        $seasons = Season::latest()->get();
        return view('app.admin.seasons.index', compact('seasons'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $season = new Season($data);

        if ($conflict = $season->overlapsWithAny()) {
            return back()
                ->withInput()
                ->withErrors(['overlap' => "يتعارض مع موسم فعال: «{$conflict->name_ar}» ({$conflict->starts_at->format('d/m/Y')} — {$conflict->ends_at->format('d/m/Y')})"]);
        }

        $season->save();
        SeasonService::forgetCache();

        return back()->with('success', 'تم إنشاء الموسم بنجاح');
    }

    public function update(Request $request, Season $season)
    {
        $data = $this->validated($request);

        $updated = new Season(array_merge($season->getAttributes(), $data));
        $updated->id = $season->id;

        if ($conflict = $updated->overlapsWithAny($season->id)) {
            return back()
                ->withInput()
                ->withErrors(['overlap' => "يتعارض مع موسم فعال: «{$conflict->name_ar}» ({$conflict->starts_at->format('d/m/Y')} — {$conflict->ends_at->format('d/m/Y')})"]);
        }

        $season->update($data);
        SeasonService::forgetCache();

        return back()->with('success', 'تم تحديث الموسم');
    }

    public function toggle(Season $season)
    {
        $newState = ! $season->is_active;

        // Activating: check for overlap with other active seasons
        if ($newState) {
            if ($conflict = $season->overlapsWithAny($season->id)) {
                return back()->withErrors(['overlap' =>
                    "لا يمكن تفعيل هذا الموسم — يتعارض مع «{$conflict->name_ar}» ({$conflict->starts_at->format('d/m/Y')} — {$conflict->ends_at->format('d/m/Y')})"
                ]);
            }
        }

        $season->update(['is_active' => $newState]);
        SeasonService::forgetCache();

        $msg = $newState ? 'تم تفعيل الموسم' : 'تم إيقاف الموسم';
        return back()->with('success', $msg);
    }

    public function destroy(Season $season)
    {
        $season->delete();
        SeasonService::forgetCache();
        return back()->with('success', 'تم حذف الموسم');
    }

    // ── Private ────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name_ar'             => 'required|string|max:100',
            'name_en'             => 'required|string|max:100',
            'discount_percentage' => 'required|numeric|min:1|max:90',
            'starts_at'           => 'required|date',
            'ends_at'             => 'required|date|after:starts_at',
            'is_active'           => 'boolean',
        ], [
            'name_ar.required'             => 'الاسم بالعربي مطلوب',
            'name_en.required'             => 'الاسم بالإنجليزي مطلوب',
            'discount_percentage.required' => 'نسبة الخصم مطلوبة',
            'discount_percentage.min'      => 'الحد الأدنى للخصم 1%',
            'discount_percentage.max'      => 'الحد الأقصى للخصم 90%',
            'starts_at.required'           => 'تاريخ البداية مطلوب',
            'ends_at.required'             => 'تاريخ النهاية مطلوب',
            'ends_at.after'                => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        return $data;
    }
}
