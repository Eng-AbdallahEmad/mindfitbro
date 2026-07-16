<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnersController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'logo_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'sort_order'=> 'nullable|integer|min:0',
        ], [
            'name.required'      => 'اسم الشريك مطلوب',
            'logo_file.required' => 'صورة اللوجو مطلوبة',
            'logo_file.image'    => 'الملف يجب أن يكون صورة',
            'logo_file.max'      => 'حجم الصورة يجب ألا يتجاوز 2MB',
        ]);

        Partner::create([
            'name'       => $request->name,
            'logo_path'  => $this->saveLogo($request),
            'sort_order' => $request->input('sort_order', (int) Partner::max('sort_order') + 1),
            'is_active'  => true,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'sections'])
            ->with('success', 'تم إضافة الشريك بنجاح');
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'sort_order'=> 'nullable|integer|min:0',
        ], [
            'name.required'   => 'اسم الشريك مطلوب',
            'logo_file.image' => 'الملف يجب أن يكون صورة',
            'logo_file.max'   => 'حجم الصورة يجب ألا يتجاوز 2MB',
        ]);

        if ($request->hasFile('logo_file')) {
            $this->deleteLogo($partner->logo_path);
        }

        $partner->update([
            'name'       => $request->name,
            'logo_path'  => $this->saveLogo($request, $partner->logo_path),
            'sort_order' => $request->input('sort_order', $partner->sort_order),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'sections'])
            ->with('success', 'تم تحديث بيانات الشريك بنجاح');
    }

    public function toggleActive(Partner $partner)
    {
        $partner->update(['is_active' => !$partner->is_active]);

        return back()->with('success', $partner->is_active ? 'تم إظهار الشريك' : 'تم إخفاء الشريك');
    }

    public function destroy(Partner $partner)
    {
        $this->deleteLogo($partner->logo_path);
        $partner->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'sections'])
            ->with('success', 'تم حذف الشريك بنجاح');
    }

    private function saveLogo(Request $request, ?string $existing = null): string
    {
        if ($request->hasFile('logo_file')) {
            $file     = $request->file('logo_file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $dir      = public_path('uploads/partners');

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $file->move($dir, $filename);
            return 'uploads/partners/' . $filename;
        }

        return $existing ?? '';
    }

    private function deleteLogo(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
