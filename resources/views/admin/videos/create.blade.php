@extends('layouts.admin')

@section('title', __('admin.videos_create'))

@section('breadcrumb')
    <a href="{{ route('admin.videos.index') }}">
        {{ __('admin.nav_videos') }}
    </a>

    <span class="sep">›</span>

    {{ __('admin.btn_add') }}
@endsection

@section('content')
    <div style="max-width:750px">
        <form
            method="POST"
            action="{{ route('admin.videos.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="card">
                <div class="card-body">

                    {{-- عرض أخطاء التحقق --}}
                    @if($errors->any())
                        <div
                            style="
                                padding:14px;
                                margin-bottom:18px;
                                border:1px solid rgba(231,76,60,.4);
                                border-radius:8px;
                                background:rgba(231,76,60,.08);
                                color:#e74c3c;
                            "
                        >
                            <ul style="margin:0;padding-right:20px">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- العنوان --}}
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_title') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title') }}"
                            required
                        >
                    </div>

                    {{-- التصنيف --}}
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_category') }}
                        </label>

                        <select name="category_id" class="form-control">
                            <option value="">
                                {{ __('admin.opt_no_category') }}
                            </option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        (string) old('category_id') ===
                                        (string) $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الوصف --}}
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_description') }}
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- الصورة المصغرة --}}
                    <div
                        class="form-group"
                        style="
                            padding:18px;
                            border:1px solid rgba(255,255,255,.1);
                            border-radius:10px;
                        "
                    >
                        <label
                            class="form-label"
                            style="font-weight:700;margin-bottom:12px"
                        >
                            الصورة المصغرة
                        </label>

                        <div style="margin-bottom:14px">
                            <label class="form-label">
                                رفع صورة من الجهاز أو المكتبة
                            </label>

                            <input
                                type="file"
                                name="thumbnail_file"
                                id="thumbnail_file"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <small
                                style="
                                    display:block;
                                    margin-top:6px;
                                    color:rgba(255,255,255,.5);
                                "
                            >
                                الصيغ المسموحة: JPG، PNG، WEBP، GIF.
                                الحد الأقصى 5MB.
                            </small>
                        </div>

                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                                margin:14px 0;
                            "
                        >
                            <span
                                style="
                                    flex:1;
                                    height:1px;
                                    background:rgba(255,255,255,.1);
                                "
                            ></span>

                            <span style="color:rgba(255,255,255,.45)">
                                أو
                            </span>

                            <span
                                style="
                                    flex:1;
                                    height:1px;
                                    background:rgba(255,255,255,.1);
                                "
                            ></span>
                        </div>

                        <label class="form-label">
                            رابط صورة خارجية
                        </label>

                        <input
                            type="url"
                            name="thumbnail"
                            id="thumbnail_url"
                            class="form-control"
                            value="{{ old('thumbnail') }}"
                            placeholder="https://example.com/thumbnail.jpg"
                        >

                        <div
                            id="thumbnail_preview_container"
                            style="
                                display:none;
                                margin-top:15px;
                            "
                        >
                            <img
                                id="thumbnail_preview"
                                src=""
                                alt="معاينة الصورة"
                                style="
                                    width:100%;
                                    max-width:420px;
                                    height:230px;
                                    object-fit:cover;
                                    border-radius:10px;
                                    border:1px solid rgba(255,255,255,.12);
                                "
                            >
                        </div>
                    </div>

                    {{-- مصدر الفيديو --}}
                    <div
                        class="form-group"
                        style="
                            padding:18px;
                            border:1px solid rgba(255,255,255,.1);
                            border-radius:10px;
                        "
                    >
                        <label
                            class="form-label"
                            style="font-weight:700;margin-bottom:12px"
                        >
                            مصدر الفيديو
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <div
                            style="
                                display:grid;
                                grid-template-columns:repeat(2,minmax(0,1fr));
                                gap:10px;
                                margin-bottom:18px;
                            "
                        >
                            <label
                                style="
                                    padding:14px;
                                    border:1px solid rgba(255,255,255,.12);
                                    border-radius:8px;
                                    cursor:pointer;
                                "
                            >
                                <input
                                    type="radio"
                                    name="video_source"
                                    value="upload"
                                    @checked(old('video_source', 'upload') === 'upload')
                                >

                                <i class="fa-solid fa-upload"></i>
                                رفع من الجهاز أو المكتبة
                            </label>

                            <label
                                style="
                                    padding:14px;
                                    border:1px solid rgba(255,255,255,.12);
                                    border-radius:8px;
                                    cursor:pointer;
                                "
                            >
                                <input
                                    type="radio"
                                    name="video_source"
                                    value="url"
                                    @checked(old('video_source') === 'url')
                                >

                                <i class="fa-brands fa-youtube"></i>
                                رابط فيديو
                            </label>
                        </div>

                        {{-- رفع الفيديو --}}
                        <div id="video_upload_section">
                            <label class="form-label">
                                ملف الفيديو
                            </label>

                            <input
                                type="file"
                                name="video_file"
                                id="video_file"
                                class="form-control"
                                accept="video/mp4,video/webm,video/quicktime"
                            >

                            <small
                                style="
                                    display:block;
                                    margin-top:6px;
                                    color:rgba(255,255,255,.5);
                                "
                            >
                                الصيغ المسموحة: MP4، WEBM، MOV.
                                الحد الأقصى حسب إعدادات الخادم.
                            </small>

                            <video
                                id="video_preview"
                                controls
                                style="
                                    display:none;
                                    width:100%;
                                    max-height:380px;
                                    margin-top:15px;
                                    border-radius:10px;
                                    background:#000;
                                "
                            ></video>
                        </div>

                        {{-- رابط الفيديو --}}
                        <div id="video_url_section" style="display:none">
                            <div class="form-group">
                                <label class="form-label">
                                    رابط YouTube أو رابط الفيديو
                                </label>

                                <input
                                    type="url"
                                    name="video_url"
                                    class="form-control"
                                    value="{{ old('video_url') }}"
                                    placeholder="https://www.youtube.com/watch?v=VIDEO_ID"
                                >
                            </div>

                            <div class="form-group" style="margin-bottom:0">
                                <label class="form-label">
                                    رابط التضمين
                                </label>

                                <input
                                    type="url"
                                    name="embed_url"
                                    class="form-control"
                                    value="{{ old('embed_url') }}"
                                    placeholder="https://www.youtube.com/embed/VIDEO_ID"
                                >

                                <small
                                    style="
                                        display:block;
                                        margin-top:6px;
                                        color:rgba(255,255,255,.5);
                                    "
                                >
                                    يمكنك استخدام رابط الفيديو العادي أو رابط
                                    التضمين، ولا يلزم تعبئة الحقلين.
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- الحالة --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                {{ __('admin.label_status') }}
                            </label>

                            <select name="status" class="form-control">
                                <option
                                    value="draft"
                                    @selected(old('status', 'draft') === 'draft')
                                >
                                    {{ __('admin.status_draft') }}
                                </option>

                                <option
                                    value="published"
                                    @selected(old('status') === 'published')
                                >
                                    {{ __('admin.status_published') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- فيديو مميز --}}
                    <div class="form-group">
                        <label class="form-check">
                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                @checked(old('is_featured'))
                            >

                            {{ __('admin.label_featured_video') }}
                        </label>
                    </div>

                    {{-- الأزرار --}}
                    <div style="display:flex;gap:8px">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i>
                            {{ __('admin.btn_save') }}
                        </button>

                        <a
                            href="{{ route('admin.videos.index') }}"
                            class="btn btn-outline"
                        >
                            {{ __('admin.btn_cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sourceInputs = document.querySelectorAll(
                'input[name="video_source"]'
            );

            const uploadSection = document.getElementById(
                'video_upload_section'
            );

            const urlSection = document.getElementById(
                'video_url_section'
            );

            const videoFile = document.getElementById('video_file');
            const videoPreview = document.getElementById('video_preview');

            const thumbnailFile = document.getElementById(
                'thumbnail_file'
            );

            const thumbnailUrl = document.getElementById(
                'thumbnail_url'
            );

            const thumbnailPreviewContainer = document.getElementById(
                'thumbnail_preview_container'
            );

            const thumbnailPreview = document.getElementById(
                'thumbnail_preview'
            );

            function toggleVideoSource() {
                const selected = document.querySelector(
                    'input[name="video_source"]:checked'
                );

                const source = selected ? selected.value : 'upload';

                uploadSection.style.display =
                    source === 'upload' ? 'block' : 'none';

                urlSection.style.display =
                    source === 'url' ? 'block' : 'none';
            }

            sourceInputs.forEach(function (input) {
                input.addEventListener('change', toggleVideoSource);
            });

            toggleVideoSource();

            videoFile.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    videoPreview.pause();
                    videoPreview.removeAttribute('src');
                    videoPreview.style.display = 'none';
                    return;
                }

                videoPreview.src = URL.createObjectURL(file);
                videoPreview.style.display = 'block';
            });

            thumbnailFile.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    return;
                }

                thumbnailPreview.src = URL.createObjectURL(file);
                thumbnailPreviewContainer.style.display = 'block';

                // الملف المرفوع له الأولوية على الرابط
                thumbnailUrl.value = '';
            });

            thumbnailUrl.addEventListener('input', function () {
                const url = this.value.trim();

                if (!url) {
                    thumbnailPreviewContainer.style.display = 'none';
                    thumbnailPreview.removeAttribute('src');
                    return;
                }

                thumbnailPreview.src = url;
                thumbnailPreviewContainer.style.display = 'block';

                // الرابط له الأولوية عند إدخاله
                thumbnailFile.value = '';
            });
        });
    </script>
@endsection