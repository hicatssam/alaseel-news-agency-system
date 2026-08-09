@extends('layouts.admin')

@section('title', 'إدارة صفحة من نحن')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:20px;flex-wrap:wrap">
    <div>
        <h2 style="margin:0;color:#1a1a2e;font-size:22px;font-weight:800">
            <i class="fa-solid fa-circle-info" style="color:#c9a84c"></i>
            إدارة صفحة من نحن
        </h2>
        <p style="margin:5px 0 0;color:#888;font-size:13px">
            تعديل المحتوى الذي يظهر للزوار في صفحة من نحن.
        </p>
    </div>

    @if(\Illuminate\Support\Facades\Route::has('about'))
        <a href="{{ route('about') }}" target="_blank" class="btn btn-outline">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            معاينة الصفحة
        </a>
    @endif
</div>

@if(session('success'))
    <div style="padding:14px 18px;margin-bottom:20px;border:1px solid #b7e4c7;border-radius:8px;background:#eafaf1;color:#19713b;font-size:13px;font-weight:700">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="padding:14px 18px;margin-bottom:20px;border:1px solid #f3c2c2;border-radius:8px;background:#fdf0f0;color:#b83232">
        <div style="font-weight:800;margin-bottom:7px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            يرجى مراجعة الأخطاء التالية:
        </div>

        <ul style="margin:0;padding-right:20px;font-size:13px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $aboutImageUrl = null;
    $storedAboutImage = data_get($aboutPage ?? null, 'image');

    if ($aboutPage && method_exists($aboutPage, 'getFirstMediaUrl')) {
        foreach (['about-image', 'about_image', 'image'] as $collection) {
            try {
                $mediaUrl = $aboutPage->getFirstMediaUrl($collection);

                if (!empty($mediaUrl)) {
                    $aboutImageUrl = $mediaUrl;
                    break;
                }
            } catch (\Throwable $exception) {
                // Continue to the normal image field.
            }
        }
    }

    if (!$aboutImageUrl && filled($storedAboutImage)) {
        if (\Illuminate\Support\Str::startsWith($storedAboutImage, ['http://', 'https://', '//'])) {
            $aboutImageUrl = $storedAboutImage;
        } elseif (\Illuminate\Support\Str::startsWith($storedAboutImage, '/')) {
            $aboutImageUrl = url($storedAboutImage);
        } elseif (\Illuminate\Support\Str::startsWith($storedAboutImage, 'storage/')) {
            $aboutImageUrl = asset($storedAboutImage);
        } else {
            $aboutImageUrl = \Illuminate\Support\Facades\Storage::disk('public')
                ->url(ltrim($storedAboutImage, '/'));
        }
    }
@endphp

<form
    id="about-page-form"
    action="{{ route('admin.about.update') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf
    @method('PUT')

    <div style="display:grid;grid-template-columns:minmax(0,1.6fr) minmax(280px,.7fr);gap:20px;align-items:start" class="about-admin-grid">
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-heading" style="color:#c9a84c"></i>
                        معلومات الصفحة الأساسية
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="title">
                            عنوان الصفحة
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', data_get($aboutPage ?? null, 'title', 'من نحن')) }}"
                            maxlength="255"
                           
                        >

                        @error('title')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label for="subtitle">العنوان التعريفي</label>

                        <textarea
                            id="subtitle"
                            name="subtitle"
                            class="form-control @error('subtitle') is-invalid @enderror"
                            rows="3"
                            maxlength="1000"
                            placeholder="نص مختصر يظهر أسفل عنوان الصفحة"
                        >{{ old('subtitle', data_get($aboutPage ?? null, 'subtitle')) }}</textarea>

                        @error('subtitle')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-align-right" style="color:#c9a84c"></i>
                        المحتوى الرئيسي
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0">
                        <label for="content">
                            محتوى من نحن
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            class="form-control js-about-editor @error('content') is-invalid @enderror"
                            rows="14"
                            
                        >{{ old('content', data_get($aboutPage ?? null, 'content')) }}</textarea>

                        @error('content')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-bullseye" style="color:#c9a84c"></i>
                        الرؤية والرسالة والقيم
                    </span>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="vision">رؤيتنا</label>

                        <textarea
                            id="vision"
                            name="vision"
                            class="form-control js-about-editor @error('vision') is-invalid @enderror"
                            rows="6"
                        >{{ old('vision', data_get($aboutPage ?? null, 'vision')) }}</textarea>

                        @error('vision')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="mission">رسالتنا</label>

                        <textarea
                            id="mission"
                            name="mission"
                            class="form-control js-about-editor @error('mission') is-invalid @enderror"
                            rows="6"
                        >{{ old('mission', data_get($aboutPage ?? null, 'mission')) }}</textarea>

                        @error('mission')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label for="values">قيمنا</label>

                        <textarea
                            id="values"
                            name="values"
                            class="form-control js-about-editor @error('values') is-invalid @enderror"
                            rows="6"
                        >{{ old('values', data_get($aboutPage ?? null, 'values')) }}</textarea>

                        @error('values')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-image" style="color:#c9a84c"></i>
                        صورة الصفحة
                    </span>
                </div>

                <div class="card-body">
                    <div class="about-image-preview" id="about-image-preview">
                        @if($aboutImageUrl)
                            <img src="{{ $aboutImageUrl }}" alt="صورة صفحة من نحن">
                        @else
                            <div class="about-image-placeholder">
                                <i class="fa-regular fa-image"></i>
                                <span>لم يتم اختيار صورة</span>
                            </div>
                        @endif
                    </div>

                    <div class="form-group" style="margin-top:18px;margin-bottom:0">
                        <label for="image">اختيار صورة جديدة</label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
                        >

                        <small class="field-help">
                            JPG أو PNG أو WEBP أو GIF. يفضل استخدام صورة أفقية واضحة.
                        </small>

                        @error('image')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-gear" style="color:#c9a84c"></i>
                        إعدادات النشر
                    </span>
                </div>

                <div class="card-body">
                    <label class="status-switch-row" for="is_active">
                        <div>
                            <div style="font-weight:800;color:#1a1a2e;font-size:13px">
                                إظهار الصفحة للزوار
                            </div>

                            <div style="color:#999;font-size:11px;margin-top:3px">
                                عند التعطيل لن تظهر بيانات الصفحة العامة.
                            </div>
                        </div>

                        <span class="switch-control">
                            <input type="hidden" name="is_active" value="0">

                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                @checked((bool) old('is_active', data_get($aboutPage ?? null, 'is_active', true)))
                            >

                            <span class="switch-slider"></span>
                        </span>
                    </label>

                    @error('is_active')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

          <button
    type="submit"
    form="about-page-form"
    class="btn btn-primary"
    style="width:100%;justify-content:center;padding:12px"
>
    <i class="fa-solid fa-floppy-disk"></i>
    حفظ التعديلات
</button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/vendor/ckeditor5-48.4.0/ckeditor5.css') }}"
>

<style>
    .field-error {
        margin-top:6px;
        color:#e74c3c;
        font-size:11.5px;
        font-weight:700;
    }

    .field-help {
        display:block;
        margin-top:7px;
        color:#999;
        font-size:11px;
        line-height:1.7;
    }

    .is-invalid {
        border-color:#e74c3c !important;
    }

    .about-image-preview {
        display:flex;
        width:100%;
        min-height:220px;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        border:2px dashed #ddd;
        border-radius:10px;
        background:#fafafa;
    }

    .about-image-preview img {
        display:block;
        width:100%;
        height:240px;
        object-fit:cover;
    }

    .about-image-placeholder {
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:10px;
        color:#aaa;
        font-size:12px;
    }

    .about-image-placeholder i {
        color:#c9a84c;
        font-size:42px;
        opacity:.65;
    }

    .status-switch-row {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        margin:0;
        cursor:pointer;
    }

    .switch-control {
        position:relative;
        display:inline-block;
        width:48px;
        height:26px;
        flex-shrink:0;
    }

    .switch-control input[type="checkbox"] {
        width:0;
        height:0;
        opacity:0;
    }

    .switch-slider {
        position:absolute;
        inset:0;
        border-radius:50px;
        background:#d6d6d6;
        transition:.2s;
    }

    .switch-slider::before {
        position:absolute;
        right:3px;
        bottom:3px;
        width:20px;
        height:20px;
        border-radius:50%;
        background:#fff;
        box-shadow:0 2px 6px rgba(0,0,0,.2);
        content:"";
        transition:.2s;
    }

    .switch-control input:checked + .switch-slider {
        background:#27ae60;
    }

    .switch-control input:checked + .switch-slider::before {
        transform:translateX(-22px);
    }

    .ck.ck-editor {
        width:100%;
    }

    .ck.ck-editor__main > .ck-editor__editable {
        min-height:190px;
        color:#1a1a2e;
        background:#fff;
        font-family:Cairo, sans-serif;
        line-height:1.9;
    }

    #content + .ck-editor .ck-editor__editable {
        min-height:380px;
    }

    .ck.ck-toolbar,
    .ck.ck-editor__main > .ck-editor__editable {
        border-color:#ddd !important;
    }

    .ck-content {
        direction:rtl;
        text-align:right;
    }

    @media(max-width:900px) {
        .about-admin-grid {
            grid-template-columns:1fr !important;
        }
    }
</style>
@endpush

@push('scripts')
<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Link,
        List,
        ListProperties,
        Alignment,
        BlockQuote,
        Indent,
        IndentBlock,
        Font,
        Highlight,
        HorizontalLine,
        RemoveFormat,
        SourceEditing,
        Table,
        TableToolbar,
        TableProperties,
        TableCellProperties
    } from "{{ asset('assets/vendor/ckeditor5-48.4.0/ckeditor5.js') }}";

    import translations from "{{ asset('assets/vendor/ckeditor5-48.4.0/translations/ar.js') }}";

    const editorInstances = [];

    const editorConfig = {
        licenseKey: 'GPL',

        plugins: [
            Essentials,
            Paragraph,
            Heading,
            Bold,
            Italic,
            Underline,
            Strikethrough,
            Link,
            List,
            ListProperties,
            Alignment,
            BlockQuote,
            Indent,
            IndentBlock,
            Font,
            Highlight,
            HorizontalLine,
            RemoveFormat,
            SourceEditing,
            Table,
            TableToolbar,
            TableProperties,
            TableCellProperties
        ],

        toolbar: {
            items: [
                'undo',
                'redo',
                '|',
                'heading',
                '|',
                'fontFamily',
                'fontSize',
                'fontColor',
                'fontBackgroundColor',
                '|',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                'highlight',
                '|',
                'alignment',
                '|',
                'bulletedList',
                'numberedList',
                'outdent',
                'indent',
                '|',
                'link',
                'blockQuote',
                'insertTable',
                'horizontalLine',
                '|',
                'removeFormat',
                'sourceEditing'
            ],
            shouldNotGroupWhenFull: false
        },

        language: {
            ui: 'ar',
            content: 'ar'
        },

        translations: [translations],

        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
                'tableProperties',
                'tableCellProperties'
            ]
        },

        link: {
            addTargetToExternalLinks: true,
            defaultProtocol: 'https://'
        }
    };

    document.querySelectorAll('.js-about-editor').forEach((textarea) => {
        ClassicEditor.create(textarea, editorConfig)
            .then((editor) => {
                editorInstances.push(editor);
            })
            .catch((error) => {
                console.error('CKEditor initialization error:', error);
            });
    });

    document.getElementById('about-page-form')?.addEventListener('submit', () => {
        editorInstances.forEach((editor) => editor.updateSourceElement());
    });

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('about-image-preview');

    imageInput?.addEventListener('change', function () {
        const file = this.files?.[0];

        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        const imageUrl = URL.createObjectURL(file);

        imagePreview.innerHTML = '';

        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = 'معاينة الصورة المختارة';

        image.onload = () => URL.revokeObjectURL(imageUrl);

        imagePreview.appendChild(image);
    });
</script>
@endpush