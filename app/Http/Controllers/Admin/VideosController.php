<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:150',
            'video_url'      => 'required|url|max:500',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'thumbnail_url'  => 'nullable|url|max:500',
            'sort_order'     => 'nullable|integer|min:0',
        ], [
            'title.required'       => 'عنوان الفيديو مطلوب',
            'video_url.required'   => 'رابط الفيديو مطلوب',
            'video_url.url'        => 'رابط الفيديو غير صحيح',
            'thumbnail_url.url'    => 'رابط الصورة المصغرة غير صحيح',
            'thumbnail_file.image' => 'الملف المرفوع يجب أن يكون صورة',
            'thumbnail_file.max'   => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        Video::create([
            'title'         => $request->title,
            'video_url'     => $request->video_url,
            'thumbnail_url' => $this->saveThumbnail($request),
            'sort_order'    => $request->input('sort_order', (int) Video::max('sort_order') + 1),
            'is_active'     => true,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'videos'])
            ->with('success', 'تم إضافة الفيديو بنجاح');
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title'          => 'required|string|max:150',
            'video_url'      => 'required|url|max:500',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'thumbnail_url'  => 'nullable|url|max:500',
            'sort_order'     => 'nullable|integer|min:0',
        ], [
            'title.required'       => 'عنوان الفيديو مطلوب',
            'video_url.required'   => 'رابط الفيديو مطلوب',
            'video_url.url'        => 'رابط الفيديو غير صحيح',
            'thumbnail_url.url'    => 'رابط الصورة المصغرة غير صحيح',
            'thumbnail_file.image' => 'الملف المرفوع يجب أن يكون صورة',
            'thumbnail_file.max'   => 'حجم الصورة يجب ألا يتجاوز 5MB',
        ]);

        // Delete old local file if replaced by a new upload
        if ($request->hasFile('thumbnail_file')
            && $video->thumbnail_url
            && !str_starts_with($video->thumbnail_url, 'http')) {
            $oldPath = public_path($video->thumbnail_url);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $video->update([
            'title'         => $request->title,
            'video_url'     => $request->video_url,
            'thumbnail_url' => $this->saveThumbnail($request, $video->thumbnail_url),
            'sort_order'    => $request->input('sort_order', $video->sort_order),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'videos'])
            ->with('success', 'تم تحديث الفيديو بنجاح');
    }

    public function toggleActive(Video $video)
    {
        $video->update(['is_active' => !$video->is_active]);

        return back()->with('success', $video->is_active ? 'تم تفعيل الفيديو' : 'تم إخفاء الفيديو');
    }

    public function destroy(Video $video)
    {
        // Delete local thumbnail file if exists
        if ($video->thumbnail_url && !str_starts_with($video->thumbnail_url, 'http')) {
            $path = public_path($video->thumbnail_url);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $video->delete();

        return redirect()->route('admin.settings.index', ['tab' => 'videos'])
            ->with('success', 'تم حذف الفيديو بنجاح');
    }

    private function saveThumbnail(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('thumbnail_file')) {
            $file     = $request->file('thumbnail_file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $dir      = public_path('uploads/videos');

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $file->move($dir, $filename);
            return 'uploads/videos/' . $filename;
        }

        if ($request->filled('thumbnail_url')) {
            return $request->thumbnail_url;
        }

        return $existing;
    }
}
