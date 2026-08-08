<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query()
            ->with('category')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString()
            );
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->integer('category_id')
            );
        }

        $videos = $query
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.videos.index',
            compact('videos', 'categories')
        );
    }

    public function create()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.videos.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        $this->cleanRequestUrls($request);

        $validated = $request->validate(
            $this->storeRules($request),
            $this->validationMessages()
        );

        $thumbnail = $validated['thumbnail'] ?? null;
        $videoUrl = $validated['video_url'] ?? null;
        $embedUrl = $validated['embed_url'] ?? null;

        if ($request->hasFile('thumbnail_file')) {
            $thumbnailPath = $request
                ->file('thumbnail_file')
                ->store('videos/thumbnails', 'public');

            $thumbnail = Storage::disk('public')
                ->url($thumbnailPath);
        }

        if (
            $validated['video_source'] === 'upload'
            && $request->hasFile('video_file')
        ) {
            $videoPath = $request
                ->file('video_file')
                ->store('videos/files', 'public');

            $videoUrl = Storage::disk('public')->url($videoPath);
            $embedUrl = null;
        }

        if ($validated['video_source'] === 'url') {
            $videoUrl = $validated['video_url'] ?? null;
            $embedUrl = $validated['embed_url'] ?? null;
        }

        Video::create([
            'title' => $validated['title'],
            'slug' => $this->createUniqueSlug(
                $validated['title']
            ),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnail,
            'video_url' => $videoUrl,
            'embed_url' => $embedUrl,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $validated['status'] === 'published'
                ? now()
                : null,
        ]);

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'تمت إضافة الفيديو بنجاح.');
    }

    public function show(Video $video)
    {
        $video->load('category');

        return view(
            'admin.videos.show',
            compact('video')
        );
    }

    public function edit(Video $video)
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.videos.edit',
            compact('video', 'categories')
        );
    }

    public function update(Request $request, Video $video)
    {
        $this->cleanRequestUrls($request);

        $validated = $request->validate(
            $this->updateRules($request),
            $this->validationMessages()
        );

        $thumbnail = $video->thumbnail;
        $videoUrl = $video->video_url;
        $embedUrl = $video->embed_url;

        /*
        |--------------------------------------------------------------------------
        | تحديث الصورة المصغرة
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('thumbnail_file')) {
            $this->deletePublicFile($video->thumbnail);

            $thumbnailPath = $request
                ->file('thumbnail_file')
                ->store('videos/thumbnails', 'public');

            $thumbnail = Storage::disk('public')
                ->url($thumbnailPath);
        } elseif ($request->filled('thumbnail')) {
            $newThumbnail = $validated['thumbnail'] ?? null;

            if ($newThumbnail !== $video->thumbnail) {
                $this->deletePublicFile($video->thumbnail);
            }

            $thumbnail = $newThumbnail;
        }

        /*
        |--------------------------------------------------------------------------
        | تحديد مصدر الفيديو
        |--------------------------------------------------------------------------
        |
        | إذا لم يرسل نموذج التعديل video_source، يتم استنتاجه من الحقول.
        |
        */

        $videoSource = $request->input('video_source');

        if (! in_array($videoSource, ['upload', 'url'], true)) {
            $videoSource = $request->hasFile('video_file')
                ? 'upload'
                : 'url';
        }

        /*
        |--------------------------------------------------------------------------
        | رفع ملف فيديو جديد
        |--------------------------------------------------------------------------
        */

        if (
            $videoSource === 'upload'
            && $request->hasFile('video_file')
        ) {
            $this->deletePublicFile($video->video_url);

            $videoPath = $request
                ->file('video_file')
                ->store('videos/files', 'public');

            $videoUrl = Storage::disk('public')->url($videoPath);
            $embedUrl = null;
        }

        /*
        |--------------------------------------------------------------------------
        | تحديث رابط الفيديو
        |--------------------------------------------------------------------------
        |
        | لا نحذف الفيديو القديم إذا كانت حقول الرابط فارغة؛ وهذا مهم في نموذج
        | التعديل عندما يريد المستخدم تعديل العنوان فقط.
        |
        */

        if (
            $videoSource === 'url'
            && (
                $request->filled('video_url')
                || $request->filled('embed_url')
            )
        ) {
            $newVideoUrl = $validated['video_url'] ?? null;
            $newEmbedUrl = $validated['embed_url'] ?? null;

            if ($newVideoUrl !== $video->video_url) {
                $this->deletePublicFile($video->video_url);
            }

            $videoUrl = $newVideoUrl;
            $embedUrl = $newEmbedUrl;
        }

        /*
        |--------------------------------------------------------------------------
        | تحديث تاريخ النشر
        |--------------------------------------------------------------------------
        */

        $publishedAt = $video->published_at;

        if (
            $validated['status'] === 'published'
            && blank($publishedAt)
        ) {
            $publishedAt = now();
        }

        if ($validated['status'] === 'draft') {
            $publishedAt = null;
        }

        $video->update([
            'title' => $validated['title'],
            'slug' => $this->createUniqueSlug(
                $validated['title'],
                $video->id
            ),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'thumbnail' => $thumbnail,
            'video_url' => $videoUrl,
            'embed_url' => $embedUrl,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'تم تحديث الفيديو بنجاح.');
    }

    public function destroy(Video $video)
    {
        $this->deletePublicFile($video->thumbnail);
        $this->deletePublicFile($video->video_url);

        $video->delete();

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'تم حذف الفيديو بنجاح.');
    }

    private function storeRules(Request $request): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'url:http,https',
                'max:5000',
                'required_without:thumbnail_file',
            ],

            'thumbnail_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
                'required_without:thumbnail',
            ],

            'video_source' => [
                'required',
                Rule::in(['upload', 'url']),
            ],

            'video_file' => [
                Rule::requiredIf(
                    fn () =>
                        $request->input('video_source') === 'upload'
                ),
                'nullable',
                'file',
                'mimes:mp4,webm,mov',
                'max:204800',
            ],

            'video_url' => [
                Rule::requiredIf(
                    fn () =>
                        $request->input('video_source') === 'url'
                        && ! $request->filled('embed_url')
                ),
                'nullable',
                'url:http,https',
                'max:5000',
            ],

            'embed_url' => [
                'nullable',
                'url:http,https',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'thumbnail' => [
                'nullable',
                'url:http,https',
                'max:5000',
            ],

            'thumbnail_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
            ],

            'video_source' => [
                'nullable',
                Rule::in(['upload', 'url']),
            ],

            'video_file' => [
                'nullable',
                'file',
                'mimes:mp4,webm,mov',
                'max:204800',
            ],

            'video_url' => [
                'nullable',
                'url:http,https',
                'max:5000',
            ],

            'embed_url' => [
                'nullable',
                'url:http,https',
                'max:5000',
            ],

            'status' => [
                'required',
                Rule::in(['draft', 'published']),
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'title.required' =>
                'يرجى إدخال عنوان الفيديو.',

            'category_id.exists' =>
                'التصنيف المحدد غير موجود.',

            'thumbnail.required_without' =>
                'يرجى رفع صورة مصغرة أو إدخال رابط الصورة.',

            'thumbnail.url' =>
                'رابط الصورة المصغرة غير صالح.',

            'thumbnail_file.required_without' =>
                'يرجى رفع صورة مصغرة أو إدخال رابط الصورة.',

            'thumbnail_file.image' =>
                'يجب أن يكون الملف المرفوع صورة صالحة.',

            'thumbnail_file.mimes' =>
                'صيغة الصورة غير مدعومة.',

            'thumbnail_file.max' =>
                'يجب ألا يزيد حجم الصورة عن 5 ميجابايت.',

            'video_source.required' =>
                'يرجى تحديد مصدر الفيديو.',

            'video_source.in' =>
                'مصدر الفيديو المحدد غير صالح.',

            'video_file.required' =>
                'يرجى اختيار ملف الفيديو من الجهاز.',

            'video_file.mimes' =>
                'صيغة الفيديو غير مدعومة. الصيغ المسموحة: MP4 وWEBM وMOV.',

            'video_file.max' =>
                'يجب ألا يزيد حجم الفيديو عن 200 ميجابايت.',

            'video_url.required' =>
                'يرجى إدخال رابط الفيديو أو رابط التضمين.',

            'video_url.url' =>
                'رابط الفيديو غير صالح.',

            'embed_url.url' =>
                'رابط تضمين الفيديو غير صالح.',

            'status.required' =>
                'يرجى تحديد حالة الفيديو.',

            'status.in' =>
                'حالة الفيديو المحددة غير صالحة.',
        ];
    }

    private function cleanRequestUrls(Request $request): void
    {
        $request->merge([
            'thumbnail' => $this->extractUrl(
                $request->input('thumbnail')
            ),

            'video_url' => $this->extractUrl(
                $request->input('video_url')
            ),

            'embed_url' => $this->extractUrl(
                $request->input('embed_url')
            ),
        ]);
    }

    private function extractUrl(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim(html_entity_decode($value));

        if ($value === '') {
            return null;
        }

        /*
        | استخراج الرابط من Markdown:
        | [عنوان](https://example.com/file.jpg)
        */
        if (preg_match(
            '/\[[^\]]*]\((https?:\/\/[^)\s]+)\)/i',
            $value,
            $matches
        )) {
            return $matches[1];
        }

        /*
        | استخراج رابط src من iframe.
        */
        if (preg_match(
            '/<iframe[^>]+src=["\']([^"\']+)["\']/i',
            $value,
            $matches
        )) {
            return $matches[1];
        }

        /*
        | إزالة الأقواس إذا تم لصق رابط مثل:
        | (https://example.com/file.jpg)
        */
        if (preg_match(
            '/^\((https?:\/\/[^)\s]+)\)$/i',
            $value,
            $matches
        )) {
            return $matches[1];
        }

        return $value;
    }

    private function createUniqueSlug(
        string $title,
        ?int $ignoreVideoId = null
    ): string {
        $slugBase = Str::slug($title);

        if ($slugBase === '') {
            $slugBase = 'video';
        }

        $slug = $slugBase;
        $counter = 1;

        while (
            Video::query()
                ->when(
                    $ignoreVideoId,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreVideoId)
                )
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function deletePublicFile(?string $url): void
    {
        if (blank($url)) {
            return;
        }

        $urlPath = parse_url($url, PHP_URL_PATH);

        if (! is_string($urlPath)) {
            return;
        }

        $urlPath = str_replace('\\', '/', $urlPath);

        /*
        | لا نحذف الروابط الخارجية.
        | نحذف فقط الملفات الموجودة داخل storage.
        */
        if (! str_contains($urlPath, '/storage/')) {
            return;
        }

        $storagePath = Str::after($urlPath, '/storage/');
        $storagePath = ltrim($storagePath, '/');

        if (
            $storagePath !== ''
            && Storage::disk('public')->exists($storagePath)
        ) {
            Storage::disk('public')->delete($storagePath);
        }
    }
}