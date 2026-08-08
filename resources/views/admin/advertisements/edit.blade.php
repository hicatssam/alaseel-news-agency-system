@extends('layouts.admin')

@section('title', __('admin.advertisements_edit'))

@section('breadcrumb')
    {{ __('admin.nav_advertisements') }}
    ›
    {{ __('admin.btn_edit') }}
@endsection

@section('content')
    @php
        $currentMedia = $advertisement->image;

        $isExternalMedia = $currentMedia && (
            str_starts_with($currentMedia, 'http://') ||
            str_starts_with($currentMedia, 'https://')
        );

        $currentMediaUrl = $currentMedia
            ? ($isExternalMedia
                ? $currentMedia
                : Storage::disk('public')->url($currentMedia))
            : null;

        $selectedType = old(
            'type',
            $advertisement->type ?: 'image'
        );

        $selectedSource = old(
            'image_source',
            $isExternalMedia ? 'url' : 'file'
        );

        $currentExternalUrl = $isExternalMedia
            ? $currentMedia
            : '';
    @endphp

    <div class="card advertisement-edit-card">
        <div class="card-header advertisement-card-header">
            <div>
                <h3 class="advertisement-card-title">
                    <i class="fa-solid fa-pen-to-square"></i>

                    {{ __('admin.advertisements_edit') }}
                </h3>

                <p class="advertisement-card-description">
                    تعديل بيانات الإعلان ومحتوى الصورة أو الفيديو.
                </p>
            </div>

            <a
                href="{{ route('admin.advertisements.show', $advertisement) }}"
                class="btn btn-outline"
            >
                <i class="fa-solid fa-eye"></i>
                عرض الإعلان
            </a>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger validation-errors">
                    <div class="validation-errors-title">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        يرجى تصحيح الأخطاء التالية:
                    </div>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('admin.advertisements.update', $advertisement) }}"
                method="POST"
                enctype="multipart/form-data"
                id="advertisement-edit-form"
            >
                @csrf
                @method('PUT')

                {{-- Advertisement title --}}
                <div class="form-group">
                    <label for="title" class="form-label">
                        {{ __('admin.label_title') }}
                        <span class="required-star">*</span>
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $advertisement->title) }}"
                        maxlength="255"
                        autocomplete="off"
                        required
                    >

                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Advertisement position --}}
                <div class="form-group">
                    <label for="position" class="form-label">
                        {{ __('admin.label_position') }}
                        <span class="required-star">*</span>
                    </label>

                    <select
                        name="position"
                        id="position"
                        class="form-control @error('position') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            اختر موضع الإعلان
                        </option>

                        <option
                            value="header"
                            @selected(
                                old('position', $advertisement->position) === 'header'
                            )
                        >
                            {{ __('admin.ad_pos_header') }}
                        </option>

                        <option
                            value="homepage"
                            @selected(
                                old('position', $advertisement->position) === 'homepage'
                            )
                        >
                            {{ __('admin.ad_pos_homepage') }}
                        </option>

                        <option
                            value="sidebar"
                            @selected(
                                old('position', $advertisement->position) === 'sidebar'
                            )
                        >
                            {{ __('admin.ad_pos_sidebar') }}
                        </option>

                        <option
                            value="inside_article"
                            @selected(
                                old('position', $advertisement->position) === 'inside_article'
                            )
                        >
                            {{ __('admin.ad_pos_inside_article') }}
                        </option>

                        <option
                            value="footer"
                            @selected(
                                old('position', $advertisement->position) === 'footer'
                            )
                        >
                            {{ __('admin.ad_pos_footer') }}
                        </option>

                        <option
                            value="popup"
                            @selected(
                                old('position', $advertisement->position) === 'popup'
                            )
                        >
                            {{ __('admin.ad_pos_popup') }}
                        </option>

                        <option
                            value="video"
                            @selected(
                                old('position', $advertisement->position) === 'video'
                            )
                        >
                            {{ __('admin.ad_pos_video') }}
                        </option>
                    </select>

                    @error('position')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Advertisement type --}}
                <div class="form-group">
                    <label class="form-label">
                        نوع الإعلان
                        <span class="required-star">*</span>
                    </label>

                    <div class="selection-grid advertisement-type-grid">
                        <label class="selection-card">
                            <input
                                type="radio"
                                name="type"
                                value="image"
                                @checked($selectedType === 'image')
                            >

                            <span class="selection-card-content">
                                <span class="selection-card-icon">
                                    <i class="fa-solid fa-image"></i>
                                </span>

                                <strong>إعلان صورة</strong>

                                <small>
                                    JPG، PNG، GIF أو WEBP
                                </small>
                            </span>
                        </label>

                        <label class="selection-card">
                            <input
                                type="radio"
                                name="type"
                                value="video"
                                @checked($selectedType === 'video')
                            >

                            <span class="selection-card-content">
                                <span class="selection-card-icon">
                                    <i class="fa-solid fa-video"></i>
                                </span>

                                <strong>إعلان فيديو</strong>

                                <small>
                                    MP4، WEBM أو OGG
                                </small>
                            </span>
                        </label>
                    </div>

                    @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Current media --}}
                @if ($currentMediaUrl)
                    <div class="form-group">
                        <label class="form-label">
                            المحتوى الحالي
                        </label>

                        <div class="current-media-card">
                            <div class="media-card-header">
                                <span>
                                    <i class="fa-solid fa-photo-film"></i>
                                    الإعلان المحفوظ حاليًا
                                </span>

                                <span class="media-type-badge">
                                    {{ $advertisement->type === 'video'
                                        ? 'فيديو'
                                        : 'صورة' }}
                                </span>
                            </div>

                            <div class="current-media-content">
                                @if ($advertisement->type === 'video')
                                    <video
                                        src="{{ $currentMediaUrl }}"
                                        controls
                                        muted
                                        playsinline
                                        preload="metadata"
                                    ></video>
                                @else
                                    <img
                                        src="{{ $currentMediaUrl }}"
                                        alt="{{ $advertisement->title }}"
                                    >
                                @endif
                            </div>

                            <p class="field-help current-media-help">
                                يمكنك ترك حقل الملف أو الرابط فارغًا للاحتفاظ
                                بالمحتوى الحالي.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Media source --}}
                <div class="form-group">
                    <label class="form-label" id="media-source-label">
                        مصدر المحتوى الجديد
                    </label>

                    <div class="selection-grid media-source-grid">
                        <label class="selection-card source-selection-card">
                            <input
                                type="radio"
                                name="image_source"
                                value="url"
                                @checked($selectedSource === 'url')
                            >

                            <span class="selection-card-content">
                                <span class="selection-card-icon">
                                    <i class="fa-solid fa-link"></i>
                                </span>

                                <strong>رابط خارجي</strong>

                                <small>
                                    استخدم رابطًا مباشرًا للمحتوى
                                </small>
                            </span>
                        </label>

                        <label class="selection-card source-selection-card">
                            <input
                                type="radio"
                                name="image_source"
                                value="file"
                                @checked($selectedSource === 'file')
                            >

                            <span class="selection-card-content">
                                <span class="selection-card-icon">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </span>

                                <strong>رفع من الجهاز</strong>

                                <small>
                                    اختر صورة أو فيديو من جهازك
                                </small>
                            </span>
                        </label>
                    </div>

                    @error('image_source')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- External URL --}}
                    <div
                        id="media-url-wrapper"
                        class="media-input-wrapper"
                    >
                        <label for="image-url" class="form-label">
                            رابط المحتوى
                        </label>

                        <div class="input-with-icon">
                            <i class="fa-solid fa-link"></i>

                            <input
                                type="url"
                                name="image_url"
                                id="image-url"
                                class="form-control @error('image_url') is-invalid @enderror"
                                value="{{ old('image_url', $currentExternalUrl) }}"
                                maxlength="2048"
                                placeholder="https://example.com/advertisement.jpg"
                            >
                        </div>

                        <p id="url-help" class="field-help">
                            اترك الرابط الحالي كما هو للاحتفاظ به، أو أدخل رابطًا
                            مباشرًا جديدًا.
                        </p>

                        @error('image_url')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- File upload --}}
                    <div
                        id="media-file-wrapper"
                        class="media-input-wrapper"
                    >
                        <label for="image-file" class="form-label">
                            اختيار ملف جديد
                        </label>

                        <label class="file-upload-area" for="image-file">
                            <input
                                type="file"
                                name="image_file"
                                id="image-file"
                                class="@error('image_file') is-invalid @enderror"
                            >

                            <span class="file-upload-icon">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </span>

                            <strong id="file-upload-title">
                                اضغط لاختيار ملف
                            </strong>

                            <span id="file-upload-description">
                                أو اسحب الملف وأفلته هنا
                            </span>

                            <span id="selected-file-name"></span>
                        </label>

                        <p id="file-help" class="field-help">
                            JPG، JPEG، PNG، GIF، WEBP — الحد الأقصى 10 MB.
                        </p>

                        @error('image_file')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- New media preview --}}
                    <div id="media-preview" class="media-preview">
                        <div class="media-card-header">
                            <span>
                                <i class="fa-solid fa-eye"></i>
                                معاينة المحتوى الجديد
                            </span>

                            <button
                                type="button"
                                class="preview-close-button"
                                id="clear-preview-button"
                                aria-label="إغلاق المعاينة"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div id="media-preview-content"></div>
                    </div>
                </div>

                {{-- Target URL --}}
                <div class="form-group">
                    <label for="link" class="form-label">
                        {{ __('admin.label_target_url') }}
                    </label>

                    <div class="input-with-icon">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>

                        <input
                            type="url"
                            name="link"
                            id="link"
                            class="form-control @error('link') is-invalid @enderror"
                            value="{{ old('link', $advertisement->link) }}"
                            maxlength="2048"
                            placeholder="https://example.com"
                        >
                    </div>

                    <p class="field-help">
                        هذا الرابط سيفتح عند ضغط الزائر على الإعلان.
                    </p>

                    @error('link')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Advertisement period --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="starts-at" class="form-label">
                            {{ __('admin.label_starts_at') }}
                        </label>

                        <input
                            type="datetime-local"
                            name="starts_at"
                            id="starts-at"
                            class="form-control @error('starts_at') is-invalid @enderror"
                            value="{{ old(
                                'starts_at',
                                $advertisement->starts_at?->format('Y-m-d\TH:i')
                            ) }}"
                        >

                        @error('starts_at')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ends-at" class="form-label">
                            {{ __('admin.label_ends_at') }}
                        </label>

                        <input
                            type="datetime-local"
                            name="ends_at"
                            id="ends-at"
                            class="form-control @error('ends_at') is-invalid @enderror"
                            value="{{ old(
                                'ends_at',
                                $advertisement->ends_at?->format('Y-m-d\TH:i')
                            ) }}"
                        >

                        @error('ends_at')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <input type="hidden" name="status" value="0">

                    <label class="status-switch">
                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(
                                (string) old(
                                    'status',
                                    $advertisement->status ? '1' : '0'
                                ) === '1'
                            )
                        >

                        <span class="status-switch-slider"></span>

                        <span class="status-switch-content">
                            <strong>
                                {{ __('admin.label_active_ad') }}
                            </strong>

                            <small>
                                عند التفعيل يمكن عرض الإعلان خلال الفترة المحددة.
                            </small>
                        </span>
                    </label>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Form actions --}}
                <div class="form-actions">
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="submit-button"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ __('admin.btn_save') }}
                    </button>

                    <a
                        href="{{ route('admin.advertisements.index') }}"
                        class="btn btn-outline"
                    >
                        <i class="fa-solid fa-xmark"></i>
                        {{ __('admin.btn_cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .advertisement-edit-card {
            max-width: 960px;
            margin-inline: auto;
        }

        .advertisement-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .advertisement-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .advertisement-card-title i {
            color: #c89a2b;
        }

        .advertisement-card-description {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, .45);
            font-size: 12px;
        }

        .required-star {
            color: #ef4444;
        }

        .validation-errors {
            margin-bottom: 22px;
        }

        .validation-errors-title {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .validation-errors ul {
            margin: 0;
            padding-inline-start: 22px;
        }

        .selection-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .selection-card {
            position: relative;
            cursor: pointer;
        }

        .selection-card > input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .selection-card-content {
            min-height: 118px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            background: rgba(255, 255, 255, .025);
            transition:
                border-color .2s ease,
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }

        .selection-card:hover .selection-card-content {
            border-color: rgba(200, 154, 43, .55);
            transform: translateY(-2px);
        }

        .selection-card > input:focus-visible +
        .selection-card-content {
            outline: 2px solid #c89a2b;
            outline-offset: 2px;
        }

        .selection-card > input:checked +
        .selection-card-content {
            border-color: #c89a2b;
            background: rgba(200, 154, 43, .1);
            box-shadow: 0 0 0 2px rgba(200, 154, 43, .08);
        }

        .selection-card-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #c89a2b;
            border-radius: 12px;
            background: rgba(200, 154, 43, .12);
            font-size: 20px;
        }

        .selection-card-content strong {
            color: #fff;
            font-size: 14px;
        }

        .selection-card-content small {
            color: rgba(255, 255, 255, .45);
            font-size: 11px;
        }

        .source-selection-card .selection-card-content {
            min-height: 105px;
        }

        .media-input-wrapper {
            margin-top: 16px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon > i {
            position: absolute;
            inset-inline-start: 14px;
            top: 50%;
            z-index: 2;
            color: rgba(255, 255, 255, .35);
            transform: translateY(-50%);
            pointer-events: none;
        }

        .input-with-icon .form-control {
            padding-inline-start: 41px;
        }

        .field-help {
            margin: 6px 0 0;
            color: rgba(255, 255, 255, .42);
            font-size: 11px;
            line-height: 1.7;
        }

        .current-media-card,
        .media-preview {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            background: #080808;
        }

        .media-card-header {
            min-height: 46px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255, 255, 255, .7);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
            font-size: 12px;
        }

        .media-card-header > span:first-child {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .media-type-badge {
            padding: 4px 10px;
            color: #c89a2b;
            border: 1px solid rgba(200, 154, 43, .28);
            border-radius: 999px;
            background: rgba(200, 154, 43, .08);
            font-size: 10px;
        }

        .current-media-content img,
        .current-media-content video,
        #media-preview-content img,
        #media-preview-content video {
            display: block;
            width: 100%;
            max-height: 430px;
            object-fit: contain;
            background: #000;
        }

        .current-media-help {
            padding: 10px 14px;
            margin: 0;
            border-top: 1px solid rgba(255, 255, 255, .07);
        }

        .file-upload-area {
            min-height: 165px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            color: rgba(255, 255, 255, .7);
            text-align: center;
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 14px;
            background: rgba(255, 255, 255, .02);
            cursor: pointer;
            transition: .2s ease;
        }

        .file-upload-area:hover,
        .file-upload-area.drag-active {
            border-color: #c89a2b;
            background: rgba(200, 154, 43, .07);
        }

        .file-upload-area input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .file-upload-icon {
            color: #c89a2b;
            font-size: 28px;
        }

        .file-upload-area strong {
            color: #fff;
            font-size: 13px;
        }

        .file-upload-area > span {
            font-size: 11px;
        }

        #selected-file-name {
            max-width: 100%;
            color: #c89a2b;
            overflow-wrap: anywhere;
        }

        .media-preview {
            display: none;
            margin-top: 16px;
        }

        .preview-close-button {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: rgba(255, 255, 255, .55);
            border: 0;
            border-radius: 7px;
            background: transparent;
            cursor: pointer;
            transition: .2s ease;
        }

        .preview-close-button:hover {
            color: #fff;
            background: rgba(255, 255, 255, .08);
        }

        .preview-error {
            padding: 30px 18px;
            color: #ef4444;
            text-align: center;
            font-size: 13px;
        }

        .status-switch {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .status-switch > input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .status-switch-slider {
            position: relative;
            width: 48px;
            height: 26px;
            flex-shrink: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            transition: .2s ease;
        }

        .status-switch-slider::after {
            content: '';
            position: absolute;
            top: 4px;
            inset-inline-start: 4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            transition: .2s ease;
        }

        .status-switch > input:checked +
        .status-switch-slider {
            background: #c89a2b;
        }

        .status-switch > input:checked +
        .status-switch-slider::after {
            transform: translateX(22px);
        }

        [dir="rtl"] .status-switch > input:checked +
        .status-switch-slider::after {
            transform: translateX(-22px);
        }

        .status-switch-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .status-switch-content strong {
            color: #fff;
            font-size: 13px;
        }

        .status-switch-content small {
            color: rgba(255, 255, 255, .42);
            font-size: 11px;
        }

        .invalid-feedback {
            display: block;
            margin-top: 6px;
            color: #ef4444;
            font-size: 12px;
        }

        .is-invalid,
        .form-control.is-invalid {
            border-color: #ef4444 !important;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
            padding-top: 20px;
            margin-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        @media (max-width: 680px) {
            .advertisement-type-grid,
            .media-source-grid {
                grid-template-columns: 1fr;
            }

            .advertisement-card-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .advertisement-card-header .btn {
                width: 100%;
                justify-content: center;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        (() => {
            'use strict';

            let activeObjectUrl = null;

            const form = document.getElementById(
                'advertisement-edit-form'
            );

            const typeInputs = document.querySelectorAll(
                'input[name="type"]'
            );

            const sourceInputs = document.querySelectorAll(
                'input[name="image_source"]'
            );

            const urlWrapper = document.getElementById(
                'media-url-wrapper'
            );

            const fileWrapper = document.getElementById(
                'media-file-wrapper'
            );

            const urlInput = document.getElementById('image-url');
            const fileInput = document.getElementById('image-file');

            const urlHelp = document.getElementById('url-help');
            const fileHelp = document.getElementById('file-help');

            const sourceLabel = document.getElementById(
                'media-source-label'
            );

            const uploadArea = document.querySelector(
                '.file-upload-area'
            );

            const selectedFileName = document.getElementById(
                'selected-file-name'
            );

            const previewWrapper = document.getElementById(
                'media-preview'
            );

            const previewContent = document.getElementById(
                'media-preview-content'
            );

            const clearPreviewButton = document.getElementById(
                'clear-preview-button'
            );

            const submitButton = document.getElementById(
                'submit-button'
            );

            function selectedType() {
                return document.querySelector(
                    'input[name="type"]:checked'
                )?.value || 'image';
            }

            function selectedSource() {
                return document.querySelector(
                    'input[name="image_source"]:checked'
                )?.value || 'url';
            }

            function configureMediaType(clearSelection = false) {
                const type = selectedType();

                if (type === 'video') {
                    sourceLabel.textContent = 'مصدر الفيديو الجديد';

                    fileInput.accept =
                        'video/mp4,video/webm,video/ogg';

                    fileHelp.textContent =
                        'MP4، WEBM، OGG — الحد الأقصى 50 MB. ' +
                        'اترك الحقل فارغًا للاحتفاظ بالمحتوى الحالي.';

                    urlHelp.textContent =
                        'أدخل رابطًا مباشرًا للفيديو، أو اتركه ' +
                        'دون تغيير للاحتفاظ بالرابط الحالي.';

                    urlInput.placeholder =
                        'https://example.com/advertisement.mp4';
                } else {
                    sourceLabel.textContent = 'مصدر الصورة الجديدة';

                    fileInput.accept =
                        'image/jpeg,image/png,image/gif,image/webp';

                    fileHelp.textContent =
                        'JPG، JPEG، PNG، GIF، WEBP — الحد الأقصى ' +
                        '10 MB. اترك الحقل فارغًا للاحتفاظ بالمحتوى الحالي.';

                    urlHelp.textContent =
                        'أدخل رابطًا مباشرًا للصورة، أو اتركه ' +
                        'دون تغيير للاحتفاظ بالرابط الحالي.';

                    urlInput.placeholder =
                        'https://example.com/advertisement.jpg';
                }

                if (clearSelection) {
                    fileInput.value = '';
                    selectedFileName.textContent = '';
                    clearPreview();
                }
            }

            function configureMediaSource(showPreview = true) {
                const source = selectedSource();

                if (source === 'file') {
                    urlWrapper.style.display = 'none';
                    fileWrapper.style.display = 'block';

                    urlInput.disabled = true;
                    fileInput.disabled = false;

                    if (
                        showPreview &&
                        fileInput.files &&
                        fileInput.files[0]
                    ) {
                        previewFile(fileInput.files[0]);
                    }

                    return;
                }

                urlWrapper.style.display = 'block';
                fileWrapper.style.display = 'none';

                urlInput.disabled = false;
                fileInput.disabled = true;

                if (showPreview && urlInput.value.trim()) {
                    previewUrl(urlInput.value);
                }
            }

            function validateFile(file) {
                const type = selectedType();

                const isImage = file.type.startsWith('image/');
                const isVideo = file.type.startsWith('video/');

                if (type === 'image' && !isImage) {
                    alert('يرجى اختيار ملف صورة صالح.');

                    return false;
                }

                if (type === 'video' && !isVideo) {
                    alert('يرجى اختيار ملف فيديو صالح.');

                    return false;
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

                    return false;
                }

                return true;
            }

            function previewFile(file) {
                if (!file) {
                    clearPreview();
                    return;
                }

                if (!validateFile(file)) {
                    fileInput.value = '';
                    selectedFileName.textContent = '';
                    clearPreview();

                    return;
                }

                revokeObjectUrl();

                activeObjectUrl = URL.createObjectURL(file);
                selectedFileName.textContent = file.name;

                renderPreview(
                    activeObjectUrl,
                    selectedType()
                );
            }

            function previewUrl(value) {
                const url = value.trim();

                if (!url) {
                    clearPreview();
                    return;
                }

                revokeObjectUrl();

                renderPreview(url, selectedType());
            }

            function renderPreview(source, type) {
                previewWrapper.style.display = 'block';
                previewContent.innerHTML = '';

                if (type === 'video') {
                    const video = document.createElement('video');

                    video.src = source;
                    video.controls = true;
                    video.muted = true;
                    video.loop = true;
                    video.playsInline = true;
                    video.preload = 'metadata';

                    video.addEventListener('error', () => {
                        showPreviewError(
                            'تعذر تحميل الفيديو. تأكد أن الرابط ' +
                            'مباشر أو أن الملف صالح.'
                        );
                    });

                    previewContent.appendChild(video);

                    return;
                }

                const image = document.createElement('img');

                image.src = source;
                image.alt = 'معاينة الإعلان الجديد';

                image.addEventListener('error', () => {
                    showPreviewError(
                        'تعذر تحميل الصورة. تأكد أن الرابط ' +
                        'مباشر أو أن الملف صالح.'
                    );
                });

                previewContent.appendChild(image);
            }

            function showPreviewError(message) {
                previewContent.innerHTML = '';

                const errorElement = document.createElement('div');

                errorElement.className = 'preview-error';
                errorElement.textContent = message;

                previewContent.appendChild(errorElement);
                previewWrapper.style.display = 'block';
            }

            function revokeObjectUrl() {
                if (!activeObjectUrl) {
                    return;
                }

                URL.revokeObjectURL(activeObjectUrl);
                activeObjectUrl = null;
            }

            function clearPreview() {
                revokeObjectUrl();

                previewContent.innerHTML = '';
                previewWrapper.style.display = 'none';
            }

            typeInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    configureMediaType(true);

                    if (
                        selectedSource() === 'url' &&
                        urlInput.value.trim()
                    ) {
                        previewUrl(urlInput.value);
                    }
                });
            });

            sourceInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    clearPreview();
                    configureMediaSource(true);
                });
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files?.[0];

                previewFile(file);
            });

            urlInput.addEventListener('input', () => {
                window.clearTimeout(urlInput.previewTimer);

                urlInput.previewTimer = window.setTimeout(() => {
                    previewUrl(urlInput.value);
                }, 450);
            });

            clearPreviewButton.addEventListener('click', () => {
                clearPreview();

                if (selectedSource() === 'file') {
                    fileInput.value = '';
                    selectedFileName.textContent = '';
                }
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                uploadArea.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    uploadArea.classList.add('drag-active');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                uploadArea.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    uploadArea.classList.remove('drag-active');
                });
            });

            uploadArea.addEventListener('drop', (event) => {
                const file = event.dataTransfer.files?.[0];

                if (!file || !validateFile(file)) {
                    return;
                }

                const transfer = new DataTransfer();

                transfer.items.add(file);
                fileInput.files = transfer.files;

                previewFile(file);
            });

            form.addEventListener('submit', () => {
                submitButton.disabled = true;

                submitButton.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    جارٍ حفظ التعديلات...
                `;
            });

            window.addEventListener('beforeunload', () => {
                revokeObjectUrl();
            });

            configureMediaType(false);
            configureMediaSource(false);
        })();
    </script>
@endsection