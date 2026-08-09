<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\Journalist;
use App\Models\Notification;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;



class ArticleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    protected function isSuperAdmin(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    protected function isEditor(): bool
    {
        return auth()->user()->hasRole('editor');
    }

    protected function isJournalist(): bool
    {
        return auth()->user()->hasRole('journalist');
    }

    /**
     * الصحفي يستطيع التعامل فقط مع مقالاته.
     */
    protected function authorizeJournalistArticle(Article $article): void
    {
        if (
            $this->isJournalist() &&
            (int) $article->user_id !== (int) auth()->id()
        ) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المقال.');
        }
    }

    /**
     * قواعد التحقق المشتركة.
     */
    protected function validationRules(bool $isUpdate = false): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'summary' => ['nullable', 'string'],

            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id'),
            ],

            'journalist_id' => [
                'nullable',
                Rule::exists('journalists', 'id'),
            ],
            

            'main_image_media_id' => [
    'nullable',
    'integer',
    Rule::exists('media_files', 'id')
        ->where(fn ($query) => $query
            ->where('file_type', 'image')
            ->whereNull('deleted_at')),
],

            'main_image_file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
            ],

            'main_image_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'under_review',
                    'approved',
                    'scheduled',
                    'published',
                    'archived',
                    'rejected',
                ]),
            ],

            'is_breaking' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_editor_pick' => ['nullable', 'boolean'],

            'scheduled_at' => [
                'nullable',
                'date',
                Rule::requiredIf(
                    fn () => request('status') === 'scheduled'
                ),
            ],

            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],

            'tags' => ['nullable', 'array'],
            'tags.*' => [
                'integer',
                Rule::exists('tags', 'id'),
            ],
        ];

        if ($isUpdate) {
            $rules['remove_main_image'] = ['nullable', 'boolean'];
            $rules['revision_note'] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Article::query()
            ->with(['category', 'journalist', 'tags'])
            ->latest();

        if ($this->isJournalist()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('journalist_id')) {
            $query->where('journalist_id', $request->journalist_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('is_breaking')) {
            $query->where('is_breaking', true);
        }

        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        $articles = $query
            ->paginate(20)
            ->withQueryString();

        $categories = Category::active()
            ->orderBy('name')
            ->get();

        $journalists = $this->journalistsQuery()
            ->orderBy('name')
            ->get();

        return view(
            'admin.articles.index',
            compact('articles', 'categories', 'journalists')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get();

        $journalists = $this->journalistsQuery()
            ->orderBy('name')
            ->get();

        $tags = Tag::active()
            ->orderBy('name')
            ->get();

        return view(
            'admin.articles.create',
            compact('categories', 'journalists', 'tags')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        $this->validateJournalistSelection(
            $data['journalist_id'] ?? null
        );

        $uploadedImage = null;

        try {
           if (! empty($data['main_image_media_id'])) {
    $data['main_image'] = null;
} elseif ($request->hasFile('main_image_file')) {
    $file = $request->file('main_image_file');

    $uploadedImage = $file->store(
        'media/articles',
        'public'
    );

    $media = \App\Models\MediaFile::create([
        'user_id' => auth()->id(),
        'file_name' => $file->getClientOriginalName(),
        'file_path' => $uploadedImage,
        'file_type' => 'image',
        'mime_type' => $file->getMimeType(),
        'size' => $file->getSize(),
        'folder' => 'articles',
        'alt_text' => $data['title'],
    ]);

    $data['main_image_media_id'] = $media->id;
    $data['main_image'] = null;
} else {
    $data['main_image'] =
        $data['main_image_url'] ?? null;

    $data['main_image_media_id'] = null;
}
            unset(
                $data['main_image_file'],
                $data['main_image_url'],
                $data['tags']
            );

            $data['user_id'] = auth()->id();
            $data['slug'] = $this->generateUniqueSlug($data['title']);

            $data['is_breaking'] =
                $request->boolean('is_breaking');

            $data['is_featured'] =
                $request->boolean('is_featured');

            $data['is_editor_pick'] =
                $request->boolean('is_editor_pick');

            if ($this->isJournalist()) {
                $data['status'] = 'under_review';
                $data['is_breaking'] = false;
                $data['is_featured'] = false;
                $data['is_editor_pick'] = false;
                $data['scheduled_at'] = null;
            }

            if ($data['status'] === 'published') {
                $data['published_at'] = now();
                $data['scheduled_at'] = null;
            }

            if ($data['status'] !== 'scheduled') {
                $data['scheduled_at'] = null;
            }

            $article = DB::transaction(function () use ($data, $request) {
                $article = Article::create($data);

                $article->tags()->sync(
                    $request->input('tags', [])
                );

                ActivityLog::log(
                    'create',
                    'articles',
                    "Created article: {$article->title}"
                );

                Notification::create([
                    'title' => 'مقال جديد: ' . $article->title,
                    'message' => 'تمت إضافة مقال جديد بحالة: '
                        . $this->statusValue($article->status),
                    'type' => 'article',
                ]);

                return $article;
            });


            

            return redirect()
                ->route('admin.articles.index')
                ->with('success', 'تم إنشاء المقال بنجاح.');
        } catch (\Throwable $exception) {
            if ($uploadedImage) {
                Storage::disk('public')->delete($uploadedImage);
            }

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

public function show(Request $request, string $slug)
{
    $article = Article::query()
        ->with([
            'category',
            'journalist',
            'tags',
            'approvedComments',
        ])
        ->where('slug', $slug)
        ->where('status', 'published')
        ->where(function ($query) {
            $query->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | تسجيل مشاهدة واحدة لكل IP
    |--------------------------------------------------------------------------
    */
    $ipHash = hash_hmac(
        'sha256',
        (string) $request->ip(),
        (string) config('app.key')
    );

    DB::transaction(function () use ($article, $ipHash) {
        $inserted = ArticleView::query()->insertOrIgnore([
            'article_id' => $article->id,
            'ip_hash'    => $ipHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted === 1) {
            $article->increment('views');
        }
    });

    // تحديث قيمة المشاهدات داخل الكائن بعد increment.
    $article->refresh();

    /*
    |--------------------------------------------------------------------------
    | الأخبار ذات الصلة
    |--------------------------------------------------------------------------
    */
    $related = Article::query()
        ->with(['category', 'journalist'])
        ->where('id', '!=', $article->id)
        ->where('status', 'published')
        ->where(function ($query) {
            $query->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })
        ->when(
            $article->category_id,
            fn ($query) => $query->where(
                'category_id',
                $article->category_id
            )
        )
        ->latest('published_at')
        ->take(4)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | الأكثر قراءة
    |--------------------------------------------------------------------------
    */
    $popular = Article::query()
        ->with(['category', 'journalist'])
        ->where('id', '!=', $article->id)
        ->where('status', 'published')
        ->where(function ($query) {
            $query->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        })
        ->orderByDesc('views')
        ->take(6)
        ->get();

    return view('articles.show', compact(
        'article',
        'related',
        'popular'
    ));
}
    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(Article $article)
    {
        $this->authorizeJournalistArticle($article);

        $article->load('tags');

        $categories = Category::active()
            ->orderBy('name')
            ->get();

        $journalists = $this->journalistsQuery()
            ->orderBy('name')
            ->get();

        $tags = Tag::active()
            ->orderBy('name')
            ->get();

        return view(
            'admin.articles.edit',
            compact('article', 'categories', 'journalists', 'tags')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */




    public function update(Request $request, Article $article)
    {
        $this->authorizeJournalistArticle($article);

        $data = $request->validate(
            $this->validationRules(true)
        );

        $this->validateJournalistSelection(
            $data['journalist_id'] ?? null
        );

        $oldImage = $article->main_image;
        $newUploadedImage = null;
        $deleteOldImageAfterUpdate = false;

        try {
            if ($request->hasFile('main_image_file')) {
                $newUploadedImage = $request
                    ->file('main_image_file')
                    ->store('articles', 'public');

                $data['main_image'] = $newUploadedImage;
                $deleteOldImageAfterUpdate = true;
            } elseif (! empty($data['main_image_url'])) {
                $data['main_image'] = $data['main_image_url'];

                if ($data['main_image'] !== $oldImage) {
                    $deleteOldImageAfterUpdate = true;
                }
            } elseif ($request->boolean('remove_main_image')) {
                $data['main_image'] = null;
                $deleteOldImageAfterUpdate = true;
            } else {
                $data['main_image'] = $oldImage;
            }

            $revisionNote = $data['revision_note'] ?? null;
            $tags = $request->input('tags', []);

            unset(
                $data['main_image_file'],
                $data['main_image_url'],
                $data['remove_main_image'],
                $data['revision_note'],
                $data['tags']
            );

            $data['is_breaking'] =
                $request->boolean('is_breaking');

            $data['is_featured'] =
                $request->boolean('is_featured');

            $data['is_editor_pick'] =
                $request->boolean('is_editor_pick');

            if ($this->isJournalist()) {
                $data['status'] = 'under_review';
                $data['is_breaking'] = false;
                $data['is_featured'] = false;
                $data['is_editor_pick'] = false;
                $data['scheduled_at'] = null;
            }

            if (
                $data['status'] === 'published' &&
                ! $article->published_at
            ) {
                $data['published_at'] = now();
            }

            if ($data['status'] === 'published') {
                $data['scheduled_at'] = null;
            }

            if ($data['status'] !== 'scheduled') {
                $data['scheduled_at'] = null;
            }

            DB::transaction(function () use (
                $article,
                $data,
                $tags,
                $revisionNote
            ) {
                ArticleRevision::create([
                    'article_id' => $article->id,
                    'user_id' => auth()->id(),
                    'old_title' => $article->title,
                    'old_summary' => $article->summary,
                    'old_content' => $article->content,
                    'old_status' => $this->statusValue(
                        $article->status
                    ),
                    'revision_note' => $revisionNote,
                ]);

                $article->update($data);
                $article->tags()->sync($tags);

                ActivityLog::log(
                    'update',
                    'articles',
                    "Updated article: {$article->title}"
                );
            });

            if ($deleteOldImageAfterUpdate) {
                $this->deleteLocalImage($oldImage);
            }

            return redirect()
                ->route('admin.articles.index')
                ->with('success', 'تم تحديث المقال بنجاح.');
        } catch (\Throwable $exception) {
            if ($newUploadedImage) {
                Storage::disk('public')->delete(
                    $newUploadedImage
                );
            }

            throw $exception;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(Article $article)
    {
        $this->authorizeJournalistArticle($article);

        // لمنع الصحفي من حذف مقالاته، أزل التعليق عن الشرط التالي:
        /*
        if ($this->isJournalist()) {
            abort(403, 'غير مصرح لك بحذف المقالات.');
        }
        */

        $image = $article->main_image;
        $title = $article->title;

        DB::transaction(function () use ($article, $title) {
            ActivityLog::log(
                'delete',
                'articles',
                "Deleted article: {$title}"
            );

            $article->delete();
        });

        $this->deleteLocalImage($image);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'تم حذف المقال بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Status
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Article $article
    ) {
        if ($this->isJournalist()) {
            abort(
                403,
                'غير مصرح لك بتغيير حالة المقال.'
            );
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'under_review',
                    'approved',
                    'scheduled',
                    'published',
                    'archived',
                    'rejected',
                ]),
            ],

            'scheduled_at' => [
                'nullable',
                'date',
                Rule::requiredIf(
                    fn () => $request->status === 'scheduled'
                ),
            ],
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'published') {
            $updateData['published_at'] =
                $article->published_at ?: now();

            $updateData['scheduled_at'] = null;
        } elseif ($validated['status'] === 'scheduled') {
            $updateData['scheduled_at'] =
                $validated['scheduled_at'];
        } else {
            $updateData['scheduled_at'] = null;
        }

        $article->update($updateData);

        ActivityLog::log(
            'status_change',
            'articles',
            "Changed article status to {$validated['status']}: {$article->title}"
        );

        return back()->with(
            'success',
            'تم تغيير حالة المقال.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Revisions
    |--------------------------------------------------------------------------
    */

    public function revisions(Article $article)
    {
        $this->authorizeJournalistArticle($article);

        $revisions = $article
            ->revisions()
            ->with('editor')
            ->latest()
            ->paginate(20);

        return view(
            'admin.articles.revisions',
            compact('article', 'revisions')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Content Image (CKEditor)
    |--------------------------------------------------------------------------
    |
    | Handles images inserted directly inside the article content by the
    | CKEditor 5 toolbar (SimpleUploadAdapter). Stored separately from the
    | main article image / media-library uploads, under:
    |
    |     storage/app/public/articles/content
    |
    | The original filename is never trusted — a random, safe filename is
    | always generated server-side using only a whitelisted extension.
    |
    */

   

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    protected function journalistsQuery()
    {
        $query = Journalist::active();

        if ($this->isJournalist()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    /**
     * يمنع الصحفي من ربط المقال بملف صحفي آخر.
     */
    protected function validateJournalistSelection(
        int|string|null $journalistId
    ): void {
        if (! $this->isJournalist() || empty($journalistId)) {
            return;
        }

        $allowed = Journalist::active()
            ->where('id', $journalistId)
            ->where('user_id', auth()->id())
            ->exists();

        abort_unless(
            $allowed,
            403,
            'غير مصرح لك باختيار هذا الصحفي.'
        );
    }

    /**
     * إنشاء slug فريد للمقال.
     */
    protected function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);

        if ($baseSlug === '') {
            $baseSlug = 'article';
        }

        do {
            $slug = $baseSlug . '-' . Str::lower(
                Str::random(6)
            );
        } while (
            Article::where('slug', $slug)->exists()
        );

        return $slug;
    }

    /**
     * حذف الصور المحلية فقط دون حذف الروابط الخارجية.
     */
    protected function deleteLocalImage(?string $image): void
    {
        if (
            blank($image) ||
            str_starts_with($image, 'http://') ||
            str_starts_with($image, 'https://')
        ) {
            return;
        }

        $path = ltrim(
            str_replace('\\', '/', $image),
            '/'
        );

        $path = preg_replace(
            '#^(public/|storage/app/public/|storage/)#',
            '',
            $path
        );

        if (
            $path &&
            Storage::disk('public')->exists($path)
        ) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * يدعم status سواء كان نصًا أو Enum.
     */
    protected function statusValue(mixed $status): string
    {
        if ($status instanceof \BackedEnum) {
            return (string) $status->value;
        }

        return (string) $status;
    }



    public function getMainImageUrlAttribute(): ?string
{
    // الصورة المختارة من مكتبة الوسائط
    if ($this->mainImageMedia?->file_path) {
        return Storage::disk('public')->url(
            $this->mainImageMedia->file_path
        );
    }

    // لا توجد صورة
    if (blank($this->main_image)) {
        return null;
    }

    // رابط خارجي
    if (filter_var($this->main_image, FILTER_VALIDATE_URL)) {
        return $this->main_image;
    }

    // منع تكرار storage داخل الرابط
    $path = ltrim($this->main_image, '/');

    if (str_starts_with($path, 'storage/')) {
        $path = substr($path, strlen('storage/'));
    }

    return Storage::disk('public')->url($path);
}


public function uploadContentImage(Request $request)
{
    /*
     * CKEditor 5 SimpleUploadAdapter uses "upload".
     * Keep "image" as a fallback for older clients.
     */
    $uploadField = $request->hasFile('upload')
        ? 'upload'
        : ($request->hasFile('image') ? 'image' : 'upload');

    $messages = [
        "{$uploadField}.required" => 'يجب اختيار صورة لرفعها.',
        "{$uploadField}.image" => 'الملف المرفوع يجب أن يكون صورة صالحة.',
        "{$uploadField}.mimes" =>
            'صيغ الصور المسموح بها هي: jpg, jpeg, png, webp, gif فقط.',
        "{$uploadField}.max" =>
            'الحد الأقصى لحجم الصورة هو 5 ميجابايت.',
    ];

    try {
        $request->validate([
            $uploadField => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
            ],
        ], $messages);
    } catch (\Illuminate\Validation\ValidationException $exception) {
        $firstError = collect($exception->errors())
            ->flatten()
            ->first();

        return response()->json([
            'error' => [
                'message' => $firstError
                    ?: 'تعذر رفع الصورة، يرجى المحاولة مرة أخرى.',
            ],
        ], 422);
    }

    try {
        $file = $request->file($uploadField);

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif',
        ];

        $extension = strtolower(
            (string) $file->getClientOriginalExtension()
        );

        if (! in_array($extension, $allowedExtensions, true)) {
            $guessedExtension = strtolower(
                (string) $file->guessExtension()
            );

            $extension = in_array(
                $guessedExtension,
                $allowedExtensions,
                true
            ) ? $guessedExtension : 'jpg';
        }

        $filename = Str::random(40) . '.' . $extension;

        $path = $file->storeAs(
            'articles/content',
            $filename,
            'public'
        );

        if (! $path) {
            throw new \RuntimeException(
                'Failed to store uploaded content image.'
            );
        }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    } catch (\Throwable $exception) {
        report($exception);

        return response()->json([
            'error' => [
                'message' =>
                    'حدث خطأ أثناء رفع الصورة، يرجى المحاولة مرة أخرى.',
            ],
        ], 500);
    }
}
}