<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::query()
            ->with('user')
            ->latest();

        $query->when(
            $request->filled('file_type'),
            fn ($query) => $query->where(
                'file_type',
                $request->string('file_type')->toString()
            )
        );

        $query->when(
            $request->filled('folder'),
            fn ($query) => $query->where(
                'folder',
                $request->string('folder')->toString()
            )
        );

        $query->when(
            $request->filled('search'),
            function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('file_name', 'like', "%{$search}%")
                        ->orWhere('alt_text', 'like', "%{$search}%")
                        ->orWhere('caption', 'like', "%{$search}%");
                });
            }
        );

        $files = $query
            ->paginate(24)
            ->withQueryString();

        $folders = MediaFile::query()
            ->whereNotNull('folder')
            ->where('folder', '!=', '')
            ->distinct()
            ->orderBy('folder')
            ->pluck('folder');

        $stats = [
            'total' => MediaFile::count(),
            'images' => MediaFile::where('file_type', 'image')->count(),
            'videos' => MediaFile::where('file_type', 'video')->count(),
            'audio' => MediaFile::where('file_type', 'audio')->count(),
            'documents' => MediaFile::where('file_type', 'document')->count(),
            'total_size' => MediaFile::sum('size'),
        ];

        return view(
            'admin.media.index',
            compact('files', 'folders', 'stats')
        );
    }

    /**
     * إرجاع صور المكتبة لنافذة اختيار صورة المقال.
     */
    public function picker(Request $request)
    {
        $query = MediaFile::query()
            ->where('file_type', 'image')
            ->latest();

        $query->when(
            $request->filled('folder'),
            fn ($query) => $query->where(
                'folder',
                $request->string('folder')->toString()
            )
        );

        $query->when(
            $request->filled('search'),
            function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('file_name', 'like', "%{$search}%")
                        ->orWhere('alt_text', 'like', "%{$search}%");
                });
            }
        );

        $files = $query->paginate(24);

        return response()->json([
            'success' => true,

            'media' => collect($files->items())
                ->map(fn (MediaFile $media) => $this->formatMedia($media))
                ->values(),

            'pagination' => [
                'current_page' => $files->currentPage(),
                'last_page' => $files->lastPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
                'has_more_pages' => $files->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,gif,webp,svg,mp4,mov,avi,webm,mp3,wav,ogg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            ],

            'folder' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\pL\pN_-]+$/u',
            ],

            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'caption' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType() ?? 'application/octet-stream';
        $folder = $validated['folder'] ?? 'general';

        $fileType = match (true) {
            str_starts_with($mime, 'image/') => 'image',
            str_starts_with($mime, 'video/') => 'video',
            str_starts_with($mime, 'audio/') => 'audio',
            default => 'document',
        };

        $path = $file->store("media/{$folder}", 'public');

        $media = MediaFile::create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mime,
            'size' => $file->getSize(),
            'folder' => $folder,
            'alt_text' => $validated['alt_text'] ?? null,
            'caption' => $validated['caption'] ?? null,
        ]);

        ActivityLog::log(
            'upload',
            'media',
            "Uploaded file: {$media->file_name}"
        );

       return redirect()
    ->route('admin.media.index')
    ->with('success', 'تم رفع الملف إلى مكتبة الوسائط بنجاح.');
    }

    public function update(
        Request $request,
        MediaFile $mediaFile
    ) {
        $data = $request->validate([
            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'caption' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'folder' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\pL\pN_-]+$/u',
            ],
        ]);

        $mediaFile->update($data);

        ActivityLog::log(
            'update',
            'media',
            "Updated file: {$mediaFile->file_name}"
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات الملف بنجاح.',
                'media' => $this->formatMedia(
                    $mediaFile->fresh()
                ),
            ]);
        }

        return back()->with(
            'success',
            'تم تحديث بيانات الملف بنجاح.'
        );
    }

    public function destroy(MediaFile $mediaFile)
    {
        /*
         * سنضيف فحص استخدام الملف في المقالات بعد إضافة
         * main_image_media_id إلى جدول articles.
         */

        ActivityLog::log(
            'delete',
            'media',
            "Deleted file: {$mediaFile->file_name}"
        );

        // حذف ناعم من قاعدة البيانات مع إبقاء الملف قابلًا للاسترجاع.
        $mediaFile->delete();

        return back()->with(
            'success',
            'تم نقل الملف إلى المحذوفات بنجاح.'
        );
    }

    private function formatMedia(MediaFile $media): array
    {
        return [
            'id' => $media->id,
            'file_name' => $media->file_name,
            'file_path' => $media->file_path,
            'file_type' => $media->file_type,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'folder' => $media->folder,
            'alt_text' => $media->alt_text,
            'caption' => $media->caption,
            'url' => Storage::disk('public')->url(
                $media->file_path
            ),
        ];
    }



    public function editorUpload(Request $request)
{
    $request->validate([
        'upload' => 'required|image|max:5120', // 5MB
    ]);

    $path = $request->file('upload')->store('articles', 'public');

    return response()->json([
        'url' => Storage::url($path),
    ]);
}
}