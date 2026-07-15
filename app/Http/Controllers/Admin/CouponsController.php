<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('subscriptions')->latest()->get();
        return view('app.admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'       => ['required', 'string', 'max:50'],
            'type'       => ['required', 'in:percentage,fixed'],
            'value'      => ['required', 'numeric', 'min:0.01'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'max_uses'   => ['nullable', 'integer', 'min:1'],
        ]);

        $code = strtoupper(trim($data['code']));

        if (Coupon::whereRaw('UPPER(code) = ?', [$code])->exists()) {
            return back()->withErrors(['code' => 'كود الخصم موجود مسبقاً'])->withInput();
        }

        Coupon::create([
            'code'       => $code,
            'type'       => $data['type'],
            'value'      => $data['value'],
            'expires_at' => $data['expires_at'] ?? null,
            'max_uses'   => $data['max_uses'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم إنشاء كود الخصم بنجاح');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'type'       => ['required', 'in:percentage,fixed'],
            'value'      => ['required', 'numeric', 'min:0.01'],
            'expires_at' => ['nullable', 'date'],
            'max_uses'   => ['nullable', 'integer', 'min:1'],
        ]);

        $coupon->update([
            'type'       => $request->type,
            'value'      => $request->value,
            'expires_at' => $request->expires_at ?: null,
            'max_uses'   => $request->max_uses ?: null,
            'is_active'  => $request->boolean('is_active', false),
        ]);

        return back()->with('success', 'تم تحديث كود الخصم');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        $msg = $coupon->is_active ? 'تم تفعيل الكود' : 'تم تعطيل الكود';
        return back()->with('success', $msg);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'تم حذف كود الخصم');
    }
}
