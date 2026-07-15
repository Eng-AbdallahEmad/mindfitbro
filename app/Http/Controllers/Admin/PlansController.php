<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
    {
        $plans    = Plan::with('features', 'prices')->withCount('subscriptions')->orderBy('sort_order')->get();
        $features = Feature::orderBy('name')->get();

        $plansJson = $plans->keyBy('id')->map(fn ($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'key'         => $p->key,
            'desc'        => $p->desc,
            'price'       => $p->price,
            'icon'        => $p->icon,
            'icon_bg'     => $p->icon_bg,
            'icon_color'  => $p->icon_color,
            'sort_order'  => $p->sort_order,
            'popular'     => $p->popular,
            'is_active'   => $p->is_active,
            'feature_ids' => $p->features->pluck('id')->toArray(),
            // prices keyed as "CURRENCY_DURATION" e.g. "SAR_3", "EGP_6"
            'prices'      => $p->prices->mapWithKeys(fn ($pp) => [
                "{$pp->currency}_{$pp->duration_months}" => (float) $pp->price,
            ]),
        ]);

        return view('app.admin.plans.index', compact('plans', 'features', 'plansJson'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key'         => 'required|string|unique:plans,key|max:50',
            'name'        => 'required|string|max:100',
            'desc'        => 'nullable|string|max:500',
            'sort_order'  => 'required|integer|min:0',
            'popular'     => 'boolean',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:100',
            'icon_bg'     => 'nullable|string|max:50',
            'icon_color'  => 'nullable|string|max:50',
            'btn_class'   => 'nullable|string|max:200',
            // SAR prices are required; others optional
            'prices.SAR.3' => 'required|numeric|min:0',
            'prices.SAR.6' => 'required|numeric|min:0',
            'prices.EGP.3' => 'nullable|numeric|min:0',
            'prices.EGP.6' => 'nullable|numeric|min:0',
            'prices.TND.3' => 'nullable|numeric|min:0',
            'prices.TND.6' => 'nullable|numeric|min:0',
            'prices.USD.3' => 'nullable|numeric|min:0',
            'prices.USD.6' => 'nullable|numeric|min:0',
        ], [
            'key.required'       => 'المعرف الفريد مطلوب',
            'key.unique'         => 'هذا المعرف موجود مسبقاً',
            'name.required'      => 'اسم الباقة مطلوب',
            'prices.SAR.3.required' => 'سعر الريال (٣ شهور) مطلوب',
            'prices.SAR.6.required' => 'سعر الريال (٦ شهور) مطلوب',
        ]);

        $data['popular']   = $request->boolean('popular');
        $data['is_active'] = $request->boolean('is_active', true);
        // Set plan.price = SAR 3m as convenience field
        $data['price'] = (float) ($request->input('prices.SAR.3') ?? 0);

        $plan = Plan::create($data);

        if ($request->has('features')) {
            $sync = [];
            foreach (array_keys($request->input('features', [])) as $fid) {
                $sync[$fid] = ['is_included' => true, 'sort_order' => 0];
            }
            $plan->features()->sync($sync);
        }

        $this->syncPlanPrices($plan, $request->input('prices', []));

        return redirect()->route('admin.plans.index')->with('success', 'تم إنشاء الباقة بنجاح');
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'desc'       => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
            'popular'    => 'boolean',
            'is_active'  => 'boolean',
            'icon'       => 'nullable|string|max:100',
            'icon_bg'    => 'nullable|string|max:50',
            'icon_color' => 'nullable|string|max:50',
            'btn_class'  => 'nullable|string|max:200',
            'prices.SAR.3' => 'required|numeric|min:0',
            'prices.SAR.6' => 'required|numeric|min:0',
            'prices.EGP.3' => 'nullable|numeric|min:0',
            'prices.EGP.6' => 'nullable|numeric|min:0',
            'prices.TND.3' => 'nullable|numeric|min:0',
            'prices.TND.6' => 'nullable|numeric|min:0',
            'prices.USD.3' => 'nullable|numeric|min:0',
            'prices.USD.6' => 'nullable|numeric|min:0',
        ], [
            'name.required'         => 'اسم الباقة مطلوب',
            'prices.SAR.3.required' => 'سعر الريال (٣ شهور) مطلوب',
            'prices.SAR.6.required' => 'سعر الريال (٦ شهور) مطلوب',
        ]);

        $data['popular']   = $request->boolean('popular');
        $data['is_active'] = $request->boolean('is_active');
        $data['price']     = (float) ($request->input('prices.SAR.3') ?? $plan->price);

        $plan->update($data);

        $sync = [];
        foreach (array_keys($request->input('features', [])) as $fid) {
            $sync[$fid] = ['is_included' => true, 'sort_order' => 0];
        }
        $plan->features()->sync($sync);

        $this->syncPlanPrices($plan, $request->input('prices', []));

        return redirect()->route('admin.plans.index')->with('success', 'تم تحديث الباقة بنجاح');
    }

    public function toggleActive(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return back()->with('success', $plan->is_active ? 'تم تفعيل الباقة' : 'تم تعطيل الباقة');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'لا يمكن حذف باقة لها اشتراكات مرتبطة بها');
        }
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'تم حذف الباقة بنجاح');
    }

    // ── Price Matrix ─────────────────────────────────────────────────
    private function syncPlanPrices(Plan $plan, array $prices): void
    {
        foreach (PlanPrice::CURRENCIES as $currency) {
            foreach (PlanPrice::DURATIONS as $duration) {
                $value = $prices[$currency][$duration] ?? '';

                // SAR always saved; others only if provided
                if ($currency === 'SAR' || ($value !== '' && $value !== null)) {
                    $plan->prices()->updateOrCreate(
                        ['currency' => $currency, 'duration_months' => $duration],
                        ['price' => (float) $value]
                    );
                } else {
                    $plan->prices()
                        ->where('currency', $currency)
                        ->where('duration_months', $duration)
                        ->delete();
                }
            }
        }
    }

    // ── Features CRUD ────────────────────────────────────────────────
    public function storeFeature(Request $request)
    {
        $request->validate([
            'key'  => 'required|string|unique:features,key|max:80',
            'name' => 'required|string|max:150',
        ], [
            'key.required'  => 'المعرف الفريد مطلوب',
            'key.unique'    => 'هذا المعرف موجود مسبقاً',
            'name.required' => 'اسم الميزة مطلوب',
        ]);

        Feature::create(['key' => $request->key, 'name' => $request->name, 'is_active' => true]);

        return back()->with('success', 'تم إضافة الميزة بنجاح');
    }

    public function updateFeature(Request $request, Feature $feature)
    {
        $request->validate(['name' => 'required|string|max:150'], ['name.required' => 'اسم الميزة مطلوب']);
        $feature->update(['name' => $request->name]);
        return back()->with('success', 'تم تحديث الميزة بنجاح');
    }

    public function destroyFeature(Feature $feature)
    {
        $feature->delete();
        return back()->with('success', 'تم حذف الميزة بنجاح');
    }
}
