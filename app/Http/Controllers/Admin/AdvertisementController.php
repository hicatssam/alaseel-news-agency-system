<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdvertisementController extends Controller
{
    /**
     * عرض قائمة الإعلانات.
     */
    public function index(Request $request)
    {
        $query = Advertisement::query()->latest();

        if ($request->filled('position')) {
            $query->where(
                'position',
                $request->string('position')->toString()
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->boolean('status')
            );
        }

        $ads = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.advertisements.index',
            compact('ads')
        );
    }

    /**
     * عرض صفحة إنشاء إعلان.
     */
    public function create()
    {
        return view('admin.advertisements.create');
    }

    /**
     * حفظ إعلان جديد.
     */
    public function store(Request $request)
    {
        $validated = $this->validateAdvertisement($request);

        $validated['image'] = $this->resolveMedia($request);
        $validated['status'] = $request->boolean('status');
        $validated['user_id'] = auth()->id();

        unset(
            $validated['image_source'],
            $validated['image_file'],
            $validated['image_url']
        );

        $advertisement = Advertisement::create($validated);

        ActivityLog::log(
            'create',
            'advertisements',
            "Created ad: {$advertisement->title}"
        );

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', __('admin.ad_created'));
    }

    /**
     * عرض تفاصيل الإعلان.
     */
    public function show(Advertisement $advertisement)
    {
        $advertisement->load('user');

        return view(
            'admin.advertisements.show',
            compact('advertisement')
        );
    }

    /**
     * عرض صفحة تعديل الإعلان.
     */
    public function edit(Advertisement $advertisement)
    {
        return view(
            'admin.advertisements.edit',
            compact('advertisement')
        );
    }

    /**
     * تحديث الإعلان.
     */
    public function update(
        Request $request,
        Advertisement $advertisement
    ) {
        $validated = $this->validateAdvertisement(
            $request,
            $advertisement
        );

        $oldMedia = $advertisement->image;

        $newMedia = $this->resolveMedia(
            $request,
            $oldMedia
        );

        if ($newMedia !== $oldMedia) {
            $this->deleteLocalMedia($oldMedia);
        }

        $validated['image'] = $newMedia;
        $validated['status'] = $request->boolean('status');

        unset(
            $validated['image_source'],
            $validated['image_file'],
            $validated['image_url']
        );

        $advertisement->update($validated);

        ActivityLog::log(
            'update',
            'advertisements',
            "Updated ad: {$advertisement->title}"
        );

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', __('admin.ad_updated'));
    }

    /**
     * حذف الإعلان.
     */
    public function destroy(Advertisement $advertisement)
    {
        $title = $advertisement->title;

        $this->deleteLocalMedia($advertisement->image);

        $advertisement->delete();

        ActivityLog::log(
            'delete',
            'advertisements',
            "Deleted ad: {$title}"
        );

        return redirect()
            ->route('admin.advertisements.index')
            ->with('success', __('admin.ad_deleted'));
    }

    /**
     * التحقق من بيانات الإعلان.
     */
    private function validateAdvertisement(
        Request $request,
        ?Advertisement $advertisement = null
    ): array {
        $isCreating = $advertisement === null;
        $source = $request->input('image_source');
        $type = $request->input('type');

        $allowedSources = $isCreating
            ? ['file', 'url']
            : ['file', 'url', 'current'];

        $fileRules = [
            Rule::requiredIf(
                $source === 'file' && $isCreating
            ),
            'nullable',
            'file',
        ];

        if ($type === 'video') {
            $fileRules[] = 'mimetypes:video/mp4,video/webm,video/ogg';
            $fileRules[] = 'max:51200';
        } else {
            $fileRules[] = 'image';
            $fileRules[] = 'mimes:jpg,jpeg,png,gif,webp';
            $fileRules[] = 'max:10240';
        }

        return $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'type' => [
                    'required',
                    Rule::in(['image', 'video']),
                ],

                'position' => [
                    'required',
                    Rule::in([
                        'header',
                        'homepage',
                        'sidebar',
                        'inside_article',
                        'footer',
                        'popup',
                        'video',
                    ]),
                ],

                'image_source' => [
                    'required',
                    Rule::in($allowedSources),
                ],

                'image_url' => [
                    Rule::requiredIf($source === 'url'),
                    'nullable',
                    'url:http,https',
                    'max:255',
                ],

                'image_file' => $fileRules,

                'link' => [
                    'nullable',
                    'url:http,https',
                    'max:255',
                ],

                'starts_at' => [
                    'nullable',
                    'date',
                ],

                'ends_at' => [
                    'nullable',
                    'date',
                    'after_or_equal:starts_at',
                ],

                'status' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'title.required' => 'عنوان الإعلان مطلوب.',
                'title.string' => 'عنوان الإعلان يجب أن يكون نصًا.',
                'title.max' => 'عنوان الإعلان يجب ألا يتجاوز 255 حرفًا.',

                'type.required' => 'نوع الإعلان مطلوب.',
                'type.in' => 'نوع الإعلان المحدد غير صالح.',

                'position.required' => 'مكان ظهور الإعلان مطلوب.',
                'position.in' => 'مكان ظهور الإعلان المحدد غير صالح.',

                'image_source.required' => 'يجب تحديد مصدر صورة أو فيديو الإعلان.',
                'image_source.in' => 'مصدر الملف المحدد غير صالح.',

                'image_url.required' => 'رابط الصورة أو الفيديو مطلوب.',
                'image_url.url' => 'رابط الصورة غير صالح. أدخل الرابط المباشر فقط، ويجب أن يبدأ بـ http:// أو https://.',
                'image_url.max' => 'رابط الصورة طويل جدًا؛ الحد الأقصى المسموح به هو 255 حرفًا.',

                'image_file.required' => 'يجب اختيار ملف من جهازك.',
                'image_file.file' => 'الملف المرفوع غير صالح.',
                'image_file.image' => 'الملف المرفوع يجب أن يكون صورة.',
                'image_file.mimes' => 'صيغة الصورة يجب أن تكون JPG أو JPEG أو PNG أو GIF أو WEBP.',
                'image_file.mimetypes' => 'صيغة الفيديو يجب أن تكون MP4 أو WEBM أو OGG.',
                'image_file.max' => $type === 'video'
                    ? 'حجم الفيديو يجب ألا يتجاوز 50 ميجابايت.'
                    : 'حجم الصورة يجب ألا يتجاوز 10 ميجابايت.',

                'link.url' => 'رابط الإعلان غير صالح. أدخل رابطًا يبدأ بـ http:// أو https:// دون أقواس.',
                'link.max' => 'رابط الإعلان طويل جدًا؛ الحد الأقصى المسموح به هو 255 حرفًا.',

                'starts_at.date' => 'تاريخ بداية الإعلان غير صالح.',
                'ends_at.date' => 'تاريخ نهاية الإعلان غير صالح.',
                'ends_at.after_or_equal' => 'تاريخ نهاية الإعلان يجب أن يكون بعد تاريخ البداية أو مساويًا له.',

                'status.boolean' => 'حالة الإعلان غير صالحة.',
            ]
        );
    }

    /**
     * تحديد ملف الإعلان أو رابطه.
     */
    private function resolveMedia(
        Request $request,
        ?string $existing = null
    ): ?string {
        $source = $request->input('image_source');

        if (
            $source === 'file' &&
            $request->hasFile('image_file')
        ) {
            return $request
                ->file('image_file')
                ->store('ads', 'public');
        }

        if (
            $source === 'url' &&
            $request->filled('image_url')
        ) {
            return $request
                ->string('image_url')
                ->trim()
                ->toString();
        }

        return $existing;
    }

    /**
     * حذف الملف إذا كان محليًا.
     */
    private function deleteLocalMedia(?string $media): void
    {
        if (blank($media)) {
            return;
        }

        $media = str_replace('\\', '/', trim($media));

        if (
            str_starts_with($media, 'http://') ||
            str_starts_with($media, 'https://') ||
            str_starts_with($media, 'data:')
        ) {
            return;
        }

        $media = preg_replace(
            '#^/?(?:public/|storage/)+#',
            '',
            $media
        );

        if ($media) {
            Storage::disk('public')->delete($media);
        }
    }
}