@extends('layouts.admin')

@section('title', __('admin.articles_edit'))

@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}">
        {{ __('admin.nav_articles') }}
    </a>
    <span class="sep">›</span>
    {{ __('admin.btn_edit') }}
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:16px">
        <strong>يرجى تصحيح الأخطاء التالية:</strong>

        <ul style="margin:10px 0 0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $currentImageUrl = old('main_image_media_id')
        ? null
        : $article->main_image_url;

    $selectedMediaId = old(
        'main_image_media_id',
        $article->main_image_media_id
    );

    $selectedTags = array_map(
        'strval',
        old('tags', $article->tags->pluck('id')->all())
    );
@endphp

<form
    method="POST"
    action="{{ route('admin.articles.update', $article) }}"
    enctype="multipart/form-data"
    id="article-edit-form"
>
    @csrf
    @method('PUT')

    <div
        class="article-form-grid"
        style="
            display:grid;
            grid-template-columns:minmax(0, 1fr) 300px;
            gap:20px;
            align-items:start;
        "
    >
        <div>
            <div class="card" style="margin-bottom:16px">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_article_content') }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_main_title') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $article->title) }}"
                            required
                            maxlength="500"
                            style="font-size:16px;font-weight:700"
                        >

                        @error('title')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_summary') }}
                        </label>

                        <textarea
                            name="summary"
                            class="form-control @error('summary') is-invalid @enderror"
                            rows="3"
                        >{{ old('summary', $article->summary) }}</textarea>

                        @error('summary')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_full_content') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <textarea
                            name="content"
                            class="form-control @error('content') is-invalid @enderror"
                            rows="18"
                            required
                        >{{ old('content', $article->content) }}</textarea>

                        @error('content')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">
                            {{ __('admin.label_revision_note') }}
                        </label>

                        <input
                            type="text"
                            name="revision_note"
                            class="form-control @error('revision_note') is-invalid @enderror"
                            value="{{ old('revision_note') }}"
                            placeholder="{{ __('admin.placeholder_revision_note') }}"
                            maxlength="1000"
                        >

                        @error('revision_note')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_seo_section') }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_seo_title') }}
                        </label>

                        <input
                            type="text"
                            name="seo_title"
                            class="form-control @error('seo_title') is-invalid @enderror"
                            value="{{ old('seo_title', $article->seo_title) }}"
                            maxlength="255"
                        >

                        @error('seo_title')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_seo_description') }}
                        </label>

                        <textarea
                            name="seo_description"
                            class="form-control @error('seo_description') is-invalid @enderror"
                            rows="3"
                        >{{ old('seo_description', $article->seo_description) }}</textarea>

                        @error('seo_description')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">
                            {{ __('admin.label_meta_keywords') }}
                        </label>

                        <input
                            type="text"
                            name="meta_keywords"
                            class="form-control @error('meta_keywords') is-invalid @enderror"
                            value="{{ old('meta_keywords', $article->meta_keywords) }}"
                        >

                        @error('meta_keywords')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div>
            {{-- النشر --}}
            <div class="card" style="margin-bottom:16px">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_publish_short') }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_status') }}
                        </label>

                        <select
                            name="status"
                            id="article-status"
                            class="form-control @error('status') is-invalid @enderror"
                            required
                        >
                            @foreach ([
                                'draft' => __('admin.status_draft'),
                                'under_review' => __('admin.status_review'),
                                'approved' => __('admin.status_approved'),
                                'published' => __('admin.status_published'),
                                'scheduled' => __('admin.status_scheduled'),
                                'archived' => __('admin.status_archived'),
                                'rejected' => __('admin.status_rejected'),
                            ] as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('status', $article->status) === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div
                        class="form-group"
                        id="scheduled-at-wrapper"
                    >
                        <label class="form-label">
                            {{ __('admin.label_schedule_date') }}
                        </label>

                        <input
                            type="datetime-local"
                            name="scheduled_at"
                            class="form-control @error('scheduled_at') is-invalid @enderror"
                            value="{{ old(
                                'scheduled_at',
                                $article->scheduled_at?->format('Y-m-d\TH:i')
                            ) }}"
                        >

                        @error('scheduled_at')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div style="display:flex;flex-direction:column;gap:10px">
                        <label class="form-check">
                            <input
                                type="checkbox"
                                name="is_breaking"
                                value="1"
                                @checked(old('is_breaking', $article->is_breaking))
                            >
                            {{ __('admin.label_breaking_news') }}
                        </label>

                        <label class="form-check">
                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                @checked(old('is_featured', $article->is_featured))
                            >
                            {{ __('admin.label_featured_item') }}
                        </label>

                        <label class="form-check">
                            <input
                                type="checkbox"
                                name="is_editor_pick"
                                value="1"
                                @checked(old('is_editor_pick', $article->is_editor_pick))
                            >
                            {{ __('admin.label_editor_pick_item') }}
                        </label>
                    </div>
                </div>
            </div>

            {{-- القسم والصحفي --}}
            <div class="card" style="margin-bottom:16px">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_category_journalist') }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_category') }}
                        </label>

                        <select
                            name="category_id"
                            class="form-control @error('category_id') is-invalid @enderror"
                        >
                            <option value="">
                                {{ __('admin.opt_no_category') }}
                            </option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        (string) old(
                                            'category_id',
                                            $article->category_id
                                        ) === (string) $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">
                            {{ __('admin.label_journalist') }}
                        </label>

                        <select
                            name="journalist_id"
                            class="form-control @error('journalist_id') is-invalid @enderror"
                        >
                            <option value="">
                                {{ __('admin.opt_no_journalist') }}
                            </option>

                            @foreach ($journalists as $journalist)
                                <option
                                    value="{{ $journalist->id }}"
                                    @selected(
                                        (string) old(
                                            'journalist_id',
                                            $article->journalist_id
                                        ) === (string) $journalist->id
                                    )
                                >
                                    {{ $journalist->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('journalist_id')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- الصورة الرئيسية --}}
            <div class="card" style="margin-bottom:16px">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_main_image_section') }}
                    </span>
                </div>

                <div class="card-body">
                    <input
                        type="hidden"
                        name="main_image_media_id"
                        id="main-image-media-id"
                        value="{{ $selectedMediaId }}"
                    >

                    <input
                        type="hidden"
                        name="remove_main_image"
                        id="remove-main-image"
                        value="0"
                    >

                    <div
                        id="main-image-preview-wrapper"
                        style="{{ $article->main_image_url ? '' : 'display:none;' }}margin-bottom:12px"
                    >
                        <img
                            id="main-image-preview"
                            src="{{ $article->main_image_url }}"
                            alt="معاينة الصورة الرئيسية"
                            style="
                                width:100%;
                                height:170px;
                                object-fit:cover;
                                border-radius:8px;
                                border:1px solid #dee2e6;
                            "
                        >

                        <div
                            id="selected-image-name"
                            style="font-size:12px;color:#777;margin-top:6px"
                        >
                            @if ($article->mainImageMedia)
                                {{ $article->mainImageMedia->file_name }}
                            @elseif ($article->main_image)
                                الصورة الحالية
                            @endif
                        </div>
                    </div>

                    <button
                        type="button"
                        id="open-media-picker"
                        class="btn btn-outline"
                        style="width:100%;margin-bottom:10px"
                    >
                        <i class="fa-solid fa-images"></i>
                        اختيار من مكتبة الوسائط
                    </button>

                    <div class="form-group">
                        <label class="form-label">
                            رفع صورة جديدة
                        </label>

                        <input
                            type="file"
                            name="main_image_file"
                            id="main-image-file"
                            class="form-control @error('main_image_file') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                        >

                        <small style="display:block;color:#888;margin-top:5px">
                            JPG، PNG، WEBP أو GIF — الحد الأقصى 5MB.
                        </small>

                        @error('main_image_file')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="image-divider">
                        <span></span>
                        <small>أو</small>
                        <span></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            رابط صورة خارجي
                        </label>

                        <input
                            type="url"
                            name="main_image_url"
                            id="main-image-url"
                            class="form-control @error('main_image_url') is-invalid @enderror"
                            value="{{ old(
                                'main_image_url',
                                $article->main_image_media_id
                                    ? ''
                                    : (
                                        filter_var(
                                            $article->main_image,
                                            FILTER_VALIDATE_URL
                                        )
                                            ? $article->main_image
                                            : ''
                                    )
                            ) }}"
                            placeholder="https://example.com/image.jpg"
                        >

                        @error('main_image_url')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button
                        type="button"
                        id="remove-main-image-button"
                        class="btn btn-outline"
                        style="width:100%;color:#dc3545"
                    >
                        <i class="fa-solid fa-trash"></i>
                        إزالة الصورة الرئيسية
                    </button>

                    @error('main_image_media_id')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- الوسوم --}}
            <div class="card" style="margin-bottom:16px">
                <div class="card-header">
                    <span class="card-title">
                        {{ __('admin.label_tags') }}
                    </span>
                </div>

                <div class="card-body">
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        @forelse ($tags as $tag)
                            <label class="tag-option">
                                <input
                                    type="checkbox"
                                    name="tags[]"
                                    value="{{ $tag->id }}"
                                    @checked(
                                        in_array(
                                            (string) $tag->id,
                                            $selectedTags,
                                            true
                                        )
                                    )
                                >
                                {{ $tag->name }}
                            </label>
                        @empty
                            <span style="font-size:13px;color:#888">
                                لا توجد وسوم متاحة.
                            </span>
                        @endforelse
                    </div>

                    @error('tags')
                        <small class="field-error">{{ $message }}</small>
                    @enderror

                    @error('tags.*')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    {{ __('admin.btn_save_changes') }}
                </button>

                <a
                    href="{{ route('admin.articles.revisions', $article) }}"
                    class="btn btn-outline"
                >
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    {{ __('admin.btn_revision_log') }}
                    ({{ $article->revisions->count() }})
                </a>

                <a
                    href="{{ route('admin.articles.index') }}"
                    class="btn btn-outline"
                >
                    {{ __('admin.btn_cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>

{{-- نافذة مكتبة الوسائط --}}
<div id="media-picker-modal" class="media-modal" aria-hidden="true">
    <div class="media-modal-dialog">
        <div class="media-modal-header">
            <div>
                <h3>اختيار صورة من مكتبة الوسائط</h3>
                <small>اختر صورة محفوظة لاستخدامها في المقال.</small>
            </div>

            <button
                type="button"
                id="close-media-picker"
                class="media-modal-close"
                aria-label="إغلاق"
            >
                ×
            </button>
        </div>

        <div class="media-modal-toolbar">
            <input
                type="search"
                id="media-search"
                class="form-control"
                placeholder="البحث باسم الصورة..."
            >

            <button
                type="button"
                id="media-search-button"
                class="btn btn-primary"
            >
                <i class="fa-solid fa-search"></i>
                بحث
            </button>
        </div>

        <div id="media-picker-loading" class="media-message">
            جاري تحميل الصور...
        </div>

        <div id="media-picker-empty" class="media-message" style="display:none">
            لا توجد صور مطابقة.
        </div>

        <div id="media-picker-error" class="media-error" style="display:none">
            تعذر تحميل مكتبة الوسائط.
        </div>

        <div id="media-picker-grid" class="media-picker-grid"></div>

        <div class="media-modal-footer">
            <button
                type="button"
                id="media-previous-page"
                class="btn btn-outline"
                disabled
            >
                السابق
            </button>

            <span id="media-page-status">صفحة 1</span>

            <button
                type="button"
                id="media-next-page"
                class="btn btn-outline"
                disabled
            >
                التالي
            </button>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) {
        .article-form-grid {
            grid-template-columns:1fr !important;
        }
    }

    .is-invalid {
        border-color:#dc3545 !important;
    }

    .field-error {
        display:block;
        color:#dc3545;
        margin-top:5px;
    }

    .tag-option {
        cursor:pointer;
        display:flex;
        align-items:center;
        gap:5px;
        background:#f8f9fa;
        border:1px solid #dee2e6;
        padding:5px 8px;
        border-radius:6px;
        font-size:12px;
    }

    .tag-option input {
        accent-color:#c9a84c;
    }

    .image-divider {
        display:flex;
        align-items:center;
        gap:8px;
        margin:12px 0;
    }

    .image-divider span {
        height:1px;
        background:#dee2e6;
        flex:1;
    }

    .image-divider small {
        color:#999;
    }

    .media-modal {
        display:none;
        position:fixed;
        inset:0;
        z-index:9999;
        padding:24px;
        background:rgba(0, 0, 0, .65);
        overflow:auto;
    }

    .media-modal.is-open {
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .media-modal-dialog {
        width:min(100%, 1000px);
        max-height:90vh;
        display:flex;
        flex-direction:column;
        overflow:hidden;
        background:#fff;
        color:#212529;
        border-radius:12px;
        box-shadow:0 20px 60px rgba(0, 0, 0, .35);
    }

    .media-modal-header,
    .media-modal-toolbar,
    .media-modal-footer {
        padding:16px;
        border-bottom:1px solid #e9ecef;
    }

    .media-modal-header {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
    }

    .media-modal-header h3 {
        margin:0 0 5px;
        font-size:18px;
    }

    .media-modal-header small {
        color:#777;
    }

    .media-modal-close {
        border:0;
        background:transparent;
        cursor:pointer;
        color:#555;
        font-size:30px;
        line-height:1;
    }

    .media-modal-toolbar {
        display:flex;
        gap:10px;
    }

    .media-modal-toolbar input {
        flex:1;
    }

    .media-picker-grid {
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:12px;
        padding:16px;
        overflow:auto;
    }

    .media-item {
        padding:0;
        overflow:hidden;
        text-align:start;
        cursor:pointer;
        background:#fff;
        border:2px solid transparent;
        border-radius:8px;
        box-shadow:0 0 0 1px #dee2e6;
        transition:.15s ease;
    }

    .media-item:hover {
        border-color:#c9a84c;
        transform:translateY(-2px);
    }

    .media-item img {
        display:block;
        width:100%;
        height:135px;
        object-fit:cover;
        background:#f1f3f5;
    }

    .media-item-name {
        display:block;
        padding:8px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
        font-size:12px;
    }

    .media-message,
    .media-error {
        padding:30px;
        color:#777;
        text-align:center;
    }

    .media-error {
        color:#dc3545;
    }

    .media-modal-footer {
        display:flex;
        align-items:center;
        justify-content:center;
        gap:12px;
        border-top:1px solid #e9ecef;
        border-bottom:0;
    }

    @media (max-width: 700px) {
        .media-picker-grid {
            grid-template-columns:repeat(2, minmax(0, 1fr));
        }

        .media-modal-toolbar {
            flex-direction:column;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pickerUrl = @json(route('admin.media.picker'));

    const statusInput = document.getElementById('article-status');
    const scheduleWrapper = document.getElementById(
        'scheduled-at-wrapper'
    );

    const modal = document.getElementById('media-picker-modal');
    const openPickerButton = document.getElementById(
        'open-media-picker'
    );
    const closePickerButton = document.getElementById(
        'close-media-picker'
    );

    const mediaIdInput = document.getElementById(
        'main-image-media-id'
    );
    const removeImageInput = document.getElementById(
        'remove-main-image'
    );
    const fileInput = document.getElementById('main-image-file');
    const urlInput = document.getElementById('main-image-url');

    const previewWrapper = document.getElementById(
        'main-image-preview-wrapper'
    );
    const previewImage = document.getElementById(
        'main-image-preview'
    );
    const selectedImageName = document.getElementById(
        'selected-image-name'
    );
    const removeImageButton = document.getElementById(
        'remove-main-image-button'
    );

    const searchInput = document.getElementById('media-search');
    const searchButton = document.getElementById(
        'media-search-button'
    );
    const mediaGrid = document.getElementById('media-picker-grid');
    const loadingMessage = document.getElementById(
        'media-picker-loading'
    );
    const emptyMessage = document.getElementById(
        'media-picker-empty'
    );
    const errorMessage = document.getElementById(
        'media-picker-error'
    );

    const previousButton = document.getElementById(
        'media-previous-page'
    );
    const nextButton = document.getElementById(
        'media-next-page'
    );
    const pageStatus = document.getElementById(
        'media-page-status'
    );

    let currentPage = 1;
    let lastPage = 1;
    let objectUrl = null;

    function updateScheduleVisibility() {
        scheduleWrapper.style.display =
            statusInput.value === 'scheduled'
                ? 'block'
                : 'none';
    }

    function showPreview(source, name = '') {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        if (!source) {
            previewImage.removeAttribute('src');
            previewWrapper.style.display = 'none';
            selectedImageName.textContent = '';
            return;
        }

        previewImage.src = source;
        previewWrapper.style.display = 'block';
        selectedImageName.textContent = name;
    }

    function clearAlternativeSources() {
        fileInput.value = '';
        urlInput.value = '';
        removeImageInput.value = '0';
    }

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        currentPage = 1;
        loadMedia();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    async function loadMedia() {
        loadingMessage.style.display = 'block';
        emptyMessage.style.display = 'none';
        errorMessage.style.display = 'none';
        mediaGrid.innerHTML = '';

        const params = new URLSearchParams({
            page: currentPage,
            search: searchInput.value.trim()
        });

        try {
            const response = await fetch(
                pickerUrl + '?' + params.toString(),
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Media request failed.');
            }

            const result = await response.json();
            const media = Array.isArray(result.media)
                ? result.media
                : [];

            lastPage = Number(
                result.pagination?.last_page || 1
            );

            currentPage = Number(
                result.pagination?.current_page || 1
            );

            loadingMessage.style.display = 'none';
            emptyMessage.style.display =
                media.length ? 'none' : 'block';

            media.forEach(function (item) {
                const button = document.createElement('button');
                const image = document.createElement('img');
                const name = document.createElement('span');

                button.type = 'button';
                button.className = 'media-item';
                button.title = item.file_name || 'صورة';

                image.src = item.url;
                image.alt = item.alt_text || item.file_name || '';
                image.loading = 'lazy';

                name.className = 'media-item-name';
                name.textContent = item.file_name || 'بدون اسم';

                button.appendChild(image);
                button.appendChild(name);

                button.addEventListener('click', function () {
                    mediaIdInput.value = item.id;
                    clearAlternativeSources();

                    showPreview(
                        item.url,
                        item.file_name || 'صورة من المكتبة'
                    );

                    closeModal();
                });

                mediaGrid.appendChild(button);
            });

            previousButton.disabled = currentPage <= 1;
            nextButton.disabled = currentPage >= lastPage;
            pageStatus.textContent =
                'صفحة ' + currentPage + ' من ' + lastPage;
        } catch (error) {
            loadingMessage.style.display = 'none';
            errorMessage.style.display = 'block';
            previousButton.disabled = true;
            nextButton.disabled = true;
        }
    }

    openPickerButton.addEventListener('click', openModal);
    closePickerButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape' &&
            modal.classList.contains('is-open')
        ) {
            closeModal();
        }
    });

    searchButton.addEventListener('click', function () {
        currentPage = 1;
        loadMedia();
    });

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            loadMedia();
        }
    });

    previousButton.addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--;
            loadMedia();
        }
    });

    nextButton.addEventListener('click', function () {
        if (currentPage < lastPage) {
            currentPage++;
            loadMedia();
        }
    });

    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        mediaIdInput.value = '';
        urlInput.value = '';
        removeImageInput.value = '0';

        objectUrl = URL.createObjectURL(file);

        previewImage.src = objectUrl;
        previewWrapper.style.display = 'block';
        selectedImageName.textContent = file.name;
    });

    urlInput.addEventListener('input', function () {
        const url = this.value.trim();

        if (!url) {
            return;
        }

        mediaIdInput.value = '';
        fileInput.value = '';
        removeImageInput.value = '0';

        showPreview(url, 'رابط خارجي');
    });

    removeImageButton.addEventListener('click', function () {
        mediaIdInput.value = '';
        fileInput.value = '';
        urlInput.value = '';
        removeImageInput.value = '1';

        showPreview('');
    });

    previewImage.addEventListener('error', function () {
        previewWrapper.style.display = 'none';
    });

    statusInput.addEventListener(
        'change',
        updateScheduleVisibility
    );

    updateScheduleVisibility();
});
</script>

@endsection