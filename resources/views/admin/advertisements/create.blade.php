@extends('layouts.admin')

@section('title', __('admin.advertisements_create'))

@section('breadcrumb')
    {{ __('admin.nav_advertisements') }}
    ›
    {{ __('admin.btn_create') }}
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 style="margin:0">
            <i class="fa-solid fa-rectangle-ad"></i>
            {{ __('admin.advertisements_create') }}
        </h3>
    </div>

    <div class="card-body">
       @if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form
            action="{{ route('admin.advertisements.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- العنوان --}}
            <div class="form-group">
                <label class="form-label">
                    {{ __('admin.label_title') }}
                    <span class="required-star">*</span>
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title') }}"
                    required
                >

                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- موضع الإعلان --}}
            <div class="form-group">
                <label class="form-label">
                    {{ __('admin.label_position') }}
                    <span class="required-star">*</span>
                </label>

                <select
                    name="position"
                    class="form-control @error('position') is-invalid @enderror"
                    required
                >
                    <option value="">اختر موضع الإعلان</option>

                    <option
                        value="header"
                        @selected(old('position') === 'header')
                    >
                        {{ __('admin.ad_pos_header') }}
                    </option>

                    <option
                        value="homepage"
                        @selected(old('position') === 'homepage')
                    >
                        {{ __('admin.ad_pos_homepage') }}
                    </option>

                    <option
                        value="sidebar"
                        @selected(old('position') === 'sidebar')
                    >
                        {{ __('admin.ad_pos_sidebar') }}
                    </option>

                    <option
                        value="inside_article"
                        @selected(old('position') === 'inside_article')
                    >
                        {{ __('admin.ad_pos_inside_article') }}
                    </option>

                    <option
                        value="footer"
                        @selected(old('position') === 'footer')
                    >
                        {{ __('admin.ad_pos_footer') }}
                    </option>

                    <option
                        value="popup"
                        @selected(old('position') === 'popup')
                    >
                        {{ __('admin.ad_pos_popup') }}
                    </option>

                    <option
                        value="video"
                        @selected(old('position') === 'video')
                    >
                        {{ __('admin.ad_pos_video') }}
                    </option>
                </select>

                @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- نوع الإعلان --}}
            <div class="form-group">
                <label class="form-label">
                    نوع الإعلان
                    <span class="required-star">*</span>
                </label>

                <div class="ad-type-grid">
                    <label class="ad-type-card">
                        <input
                            type="radio"
                            name="type"
                            value="image"
                            @checked(old('type', 'image') === 'image')
                            onchange="changeMediaType()"
                        >

                        <span class="ad-type-content">
                            <i class="fa-solid fa-image"></i>
                            <strong>إعلان صورة</strong>
                            <small>صورة من الجهاز أو رابط صورة</small>
                        </span>
                    </label>

                    <label class="ad-type-card">
                        <input
                            type="radio"
                            name="type"
                            value="video"
                            @checked(old('type') === 'video')
                            onchange="changeMediaType()"
                        >

                        <span class="ad-type-content">
                            <i class="fa-solid fa-video"></i>
                            <strong>إعلان فيديو</strong>
                            <small>فيديو من الجهاز أو رابط فيديو</small>
                        </span>
                    </label>
                </div>

                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- مصدر الإعلان --}}
            <div class="form-group">
                <label class="form-label" id="media-source-title">
                    مصدر الصورة
                </label>

                <div class="media-source-grid">
                    <label class="media-source-card">
                        <input
                            type="radio"
                            name="image_source"
                            value="url"
                            @checked(old('image_source', 'url') === 'url')
                            onchange="toggleMediaSource('url')"
                        >

                        <span>
                            <i class="fa-solid fa-link"></i>
                            رابط خارجي
                        </span>
                    </label>

                    <label class="media-source-card">
                        <input
                            type="radio"
                            name="image_source"
                            value="file"
                            @checked(old('image_source') === 'file')
                            onchange="toggleMediaSource('file')"
                        >

                        <span>
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            رفع من الجهاز
                        </span>
                    </label>
                </div>

                {{-- رابط الصورة أو الفيديو --}}
                <div id="media-url-wrapper">
                    <input
                        type="url"
                        name="image_url"
                        id="image-url"
                        class="form-control @error('image_url') is-invalid @enderror"
                        value="{{ old('image_url') }}"
                        placeholder="https://example.com/banner.jpg"
                        oninput="previewUrl(this.value)"
                    >

                    <p id="url-help" class="field-help">
                        أدخل رابطًا مباشرًا للصورة.
                    </p>

                    @error('image_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- رفع الصورة أو الفيديو --}}
                <div id="media-file-wrapper" style="display:none">
                    <input
                        type="file"
                        name="image_file"
                        id="image-file"
                        class="form-control @error('image_file') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        onchange="previewFile(this)"
                    >

                    <p id="file-help" class="field-help">
                        JPG، JPEG، PNG، GIF، WEBP — الحد الأقصى 10 MB
                    </p>

                    @error('image_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- المعاينة --}}
                <div id="media-preview" class="media-preview">
                    <div class="preview-header">
                        <span>
                            <i class="fa-solid fa-eye"></i>
                            معاينة الإعلان
                        </span>

                        <button
                            type="button"
                            onclick="clearPreview()"
                            aria-label="إغلاق المعاينة"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div id="media-preview-content"></div>
                </div>
            </div>

            {{-- رابط الانتقال --}}
            <div class="form-group">
                <label class="form-label">
                    {{ __('admin.label_target_url') }}
                </label>

                <input
                    type="url"
                    name="link"
                    class="form-control @error('link') is-invalid @enderror"
                    value="{{ old('link') }}"
                    placeholder="https://example.com"
                >

                <p class="field-help">
                    هذا الرابط يُفتح عند الضغط على الإعلان.
                </p>

                @error('link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- تاريخ البداية والنهاية --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_starts_at') }}
                    </label>

                    <input
                        type="datetime-local"
                        name="starts_at"
                        class="form-control @error('starts_at') is-invalid @enderror"
                        value="{{ old('starts_at') }}"
                    >

                    @error('starts_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_ends_at') }}
                    </label>

                    <input
                        type="datetime-local"
                        name="ends_at"
                        class="form-control @error('ends_at') is-invalid @enderror"
                        value="{{ old('ends_at') }}"
                    >

                    @error('ends_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- الحالة --}}
            <div class="form-group">
                <input type="hidden" name="status" value="0">

                <label class="form-check">
                    <input
                        type="checkbox"
                        name="status"
                        value="1"
                        @checked((string) old('status', '1') === '1')
                    >

                    {{ __('admin.label_active_ad') }}
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    {{ __('admin.btn_save') }}
                </button>

                <a
                    href="{{ route('admin.advertisements.index') }}"
                    class="btn btn-outline"
                >
                    {{ __('admin.btn_cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .required-star {
        color: #ef4444;
    }

    .ad-type-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .ad-type-card {
        position: relative;
        cursor: pointer;
    }

    .ad-type-card > input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .ad-type-content {
        min-height: 115px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 12px;
        background: rgba(255, 255, 255, .025);
        transition: .2s ease;
    }

    .ad-type-content i {
        color: #c89a2b;
        font-size: 26px;
    }

    .ad-type-content strong {
        color: #fff;
        font-size: 14px;
    }

    .ad-type-content small {
        color: rgba(255, 255, 255, .45);
        font-size: 11px;
    }

    .ad-type-card:hover .ad-type-content {
        border-color: rgba(200, 154, 43, .55);
        transform: translateY(-2px);
    }

    .ad-type-card > input:checked + .ad-type-content {
        border-color: #c89a2b;
        background: rgba(200, 154, 43, .1);
        box-shadow: 0 0 0 2px rgba(200, 154, 43, .1);
    }

    .media-source-grid {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .media-source-card {
        cursor: pointer;
        font-size: 13px;
    }

    .media-source-card span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .field-help {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, .42);
        font-size: 11px;
    }

    .media-preview {
        display: none;
        max-width: 720px;
        margin-top: 15px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 13px;
        background: #080808;
    }

    .preview-header {
        padding: 10px 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: rgba(255, 255, 255, .7);
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
    }

    .preview-header span {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .preview-header button {
        padding: 3px;
        color: rgba(255, 255, 255, .55);
        border: 0;
        background: transparent;
        cursor: pointer;
    }

    #media-preview-content img,
    #media-preview-content video {
        display: block;
        width: 100%;
        max-height: 380px;
        object-fit: contain;
        background: #000;
    }

    .preview-error {
        padding: 18px;
        color: #ef4444;
        font-size: 13px;
        text-align: center;
    }

    .invalid-feedback {
        display: block;
        margin-top: 5px;
        color: #ef4444;
        font-size: 12px;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .form-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media (max-width: 650px) {
        .ad-type-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    let activeObjectUrl = null;

    document.addEventListener('DOMContentLoaded', function () {
        changeMediaType(false);

        const selectedSource = getSelectedSource();
        toggleMediaSource(selectedSource, false);

        const existingUrl = document
            .getElementById('image-url')
            .value
            .trim();

        if (existingUrl) {
            previewUrl(existingUrl);
        }
    });

    function getSelectedType() {
        return document.querySelector(
            'input[name="type"]:checked'
        )?.value || 'image';
    }

    function getSelectedSource() {
        return document.querySelector(
            'input[name="image_source"]:checked'
        )?.value || 'url';
    }

    function changeMediaType(clearInputs = true) {
        const type = getSelectedType();
        const fileInput = document.getElementById('image-file');
        const urlInput = document.getElementById('image-url');
        const title = document.getElementById('media-source-title');
        const fileHelp = document.getElementById('file-help');
        const urlHelp = document.getElementById('url-help');

        if (type === 'video') {
            title.textContent = 'مصدر الفيديو';

            fileInput.accept =
                'video/mp4,video/webm,video/ogg';

            fileHelp.textContent =
                'MP4، WEBM، OGG — الحد الأقصى 50 MB';

            urlHelp.textContent =
                'أدخل رابطًا مباشرًا للفيديو مثل MP4 أو WEBM.';

            urlInput.placeholder =
                'https://example.com/advertisement.mp4';
        } else {
            title.textContent = 'مصدر الصورة';

            fileInput.accept =
                'image/jpeg,image/png,image/gif,image/webp';

            fileHelp.textContent =
                'JPG، JPEG، PNG، GIF، WEBP — الحد الأقصى 10 MB';

            urlHelp.textContent =
                'أدخل رابطًا مباشرًا للصورة مثل JPG أو PNG أو WEBP.';

            urlInput.placeholder =
                'https://example.com/banner.jpg';
        }

        if (clearInputs) {
            fileInput.value = '';
            urlInput.value = '';
            clearPreview();
        }

        toggleMediaSource(getSelectedSource(), false);
    }

    function toggleMediaSource(source, clear = true) {
        const urlWrapper =
            document.getElementById('media-url-wrapper');

        const fileWrapper =
            document.getElementById('media-file-wrapper');

        const urlInput =
            document.getElementById('image-url');

        const fileInput =
            document.getElementById('image-file');

        if (source === 'file') {
            urlWrapper.style.display = 'none';
            fileWrapper.style.display = 'block';

            urlInput.disabled = true;
            fileInput.disabled = false;
        } else {
            urlWrapper.style.display = 'block';
            fileWrapper.style.display = 'none';

            urlInput.disabled = false;
            fileInput.disabled = true;
        }

        if (!clear) {
            return;
        }

        clearPreview();

        if (source === 'url' && urlInput.value.trim()) {
            previewUrl(urlInput.value);
        }

        if (
            source === 'file' &&
            fileInput.files &&
            fileInput.files[0]
        ) {
            previewFile(fileInput);
        }
    }

    function previewFile(input) {
        const file = input.files?.[0];

        if (!file) {
            clearPreview();
            return;
        }

        const type = getSelectedType();
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        if (type === 'image' && !isImage) {
            alert('يرجى اختيار ملف صورة صالح.');
            input.value = '';
            clearPreview();
            return;
        }

        if (type === 'video' && !isVideo) {
            alert('يرجى اختيار ملف فيديو صالح.');
            input.value = '';
            clearPreview();
            return;
        }

        const maximumSize = type === 'video'
            ? 50 * 1024 * 1024
            : 10 * 1024 * 1024;

        if (file.size > maximumSize) {
            alert(
                type === 'video'
                    ? 'حجم الفيديو يجب ألا يتجاوز 50 MB.'
                    : 'حجم الصورة يجب ألا يتجاوز 10 MB.'
            );

            input.value = '';
            clearPreview();
            return;
        }

        revokeActiveObjectUrl();

        activeObjectUrl = URL.createObjectURL(file);

        renderPreview(activeObjectUrl, type);
    }

    function previewUrl(value) {
        const url = value.trim();

        if (!url) {
            clearPreview();
            return;
        }

        revokeActiveObjectUrl();
        renderPreview(url, getSelectedType());
    }

    function renderPreview(source, type) {
        const wrapper =
            document.getElementById('media-preview');

        const content =
            document.getElementById('media-preview-content');

        wrapper.style.display = 'block';
        content.innerHTML = '';

        if (type === 'video') {
            const video = document.createElement('video');

            video.src = source;
            video.controls = true;
            video.autoplay = true;
            video.muted = true;
            video.loop = true;
            video.playsInline = true;
            video.preload = 'metadata';

            video.addEventListener('error', function () {
                showPreviewError(
                    'تعذر تحميل الفيديو. تأكد أن الرابط مباشر أو أن الملف صالح.'
                );
            });

            content.appendChild(video);

            video.play().catch(function () {
                // يمكن تشغيل الفيديو يدويًا.
            });

            return;
        }

        const image = document.createElement('img');

        image.src = source;
        image.alt = 'معاينة الإعلان';

        image.addEventListener('error', function () {
            showPreviewError(
                'تعذر تحميل الصورة. تأكد أن الرابط مباشر أو أن الملف صالح.'
            );
        });

        content.appendChild(image);
    }

    function showPreviewError(message) {
        document.getElementById(
            'media-preview-content'
        ).innerHTML = `
            <div class="preview-error">${message}</div>
        `;
    }

    function revokeActiveObjectUrl() {
        if (activeObjectUrl) {
            URL.revokeObjectURL(activeObjectUrl);
            activeObjectUrl = null;
        }
    }

    function clearPreview() {
        revokeActiveObjectUrl();

        document.getElementById(
            'media-preview-content'
        ).innerHTML = '';

        document.getElementById(
            'media-preview'
        ).style.display = 'none';
    }
</script>

@endsection