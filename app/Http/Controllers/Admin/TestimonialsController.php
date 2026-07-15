<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image_file' => 'required_without:image_url|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url'  => 'required_without:image_file|nullable|url|max:500',
            'alt_text'   => 'nullable|string|max:150',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image_file.required_without' => 'يجب رفع صورة أو إدخال رابط',
            'image_url.required_without'  => 'يجب رفع صورة أو إدخال رابط',
            'image_file.image'            => 'الملف يجب أن يكون صورة',
            'image_file.max'              => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        Testimonial::create([
            'image_path' => $this->saveImage($request),
            'alt_text'   => $request->alt_text,
            'sort_order' => $request->input('sort_order', (int) Testimonial::max('sort_order') + 1),
            'is_active'  => true,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'testimonials'])
            ->with('success', 'تم إضافة الصورة بنجاح');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url'  => 'nullable|url|max:500',
            'alt_text'   => 'nullable|string|max:150',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image_file.image' => 'الملف يجب أن يكون صورة',
            'image_file.max'   => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        // Delete old local file if replaced
        if ($request->hasFile('image_file')
            && $testimonial->image_path
            && !str_starts_with($testimonial->image_path, 'http')) {
            $old = public_path($testimonial->image_path);
            if (file_exists($old)) {
                unlink($old);
            }
        }

        $testimonial->update([
            'image_path' => $this->saveImage($request, $testimonial->image_path),
            'alt_text'   => $request->alt_text,
            'sort_order' => $request->input('sort_order', $testimonial->sort_order),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'testimonials'])
            ->with('success', 'تم تحديث الصورة بنجاح');
    }

    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return back()->with('success', $testimonial->is_active ? 'تم إظهار الصورة' : 'تم إخفاء الصورة');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->image_path && !str_starts_with($testimonial->image_path, 'http')) {
            $path = public_path($testimonial->image_path);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $testimonial->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'testimonials'])
            ->with('success', 'تم حذف الصورة بنجاح');
    }

    private function saveImage(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image_file')) {
            $file     = $request->file('image_file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $dir      = public_path('uploads/testimonials');

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $file->move($dir, $filename);
            return 'uploads/testimonials/' . $filename;
        }

        if ($request->filled('image_url')) {
            return $request->image_url;
        }

        return $existing;
    }
}
