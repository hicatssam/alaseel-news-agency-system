@extends('layouts.admin')

@section('title', __('admin.journalists_create'))

@section('breadcrumb')
    <a href="{{ route('admin.journalists.index') }}">
        {{ __('admin.nav_journalists') }}
    </a>
    <span class="sep">›</span>
    {{ __('admin.btn_add') }}
@endsection

@section('content')
<div style="max-width:760px">

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:20px">
            <ul style="margin:0;padding-inline-start:20px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.journalists.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    {{ __('admin.label_journalist_data') }}
                </span>
            </div>

            <div class="card-body">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_name') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_job_title') }}
                        </label>

                        <input
                            type="text"
                            name="job_title"
                            class="form-control"
                            value="{{ old('job_title') }}"
                            placeholder="{{ __('admin.placeholder_job_title') }}"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_email') }}
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_phone') }}
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone') }}"
                        >
                    </div>
                </div>

                {{-- صورة الصحفي --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-image"></i>
                        صورة الصحفي
                    </label>

                    <div
                        id="journalistImagePreviewBox"
                        style="
                            width:140px;
                            height:140px;
                            margin-bottom:15px;
                            border:2px dashed rgba(128,128,128,.35);
                            border-radius:16px;
                            overflow:hidden;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:rgba(128,128,128,.06);
                        "
                    >
                        <img
                            id="journalistImagePreview"
                            src=""
                            alt="معاينة صورة الصحفي"
                            style="
                                width:100%;
                                height:100%;
                                object-fit:cover;
                                display:none;
                            "
                        >

                        <span
                            id="journalistImagePlaceholder"
                            style="
                                text-align:center;
                                color:#8b8b8b;
                                font-size:13px;
                                padding:15px;
                            "
                        >
                            <i
                                class="fa-solid fa-user"
                                style="font-size:36px;display:block;margin-bottom:8px"
                            ></i>
                            معاينة الصورة
                        </span>
                    </div>

                    <label class="form-label">
                        رفع صورة من الجهاز
                    </label>

                    <input
                        type="file"
                        name="photo_file"
                        id="photoFile"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                    >

                    <small
                        style="
                            display:block;
                            margin-top:7px;
                            color:#8b8b8b;
                        "
                    >
                        الأنواع المسموحة: JPG، PNG، WEBP، GIF — الحجم الأقصى 5MB.
                    </small>
                </div>

                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:12px;
                        margin:18px 0;
                    "
                >
                    <span style="height:1px;background:rgba(128,128,128,.25);flex:1"></span>
                    <span style="font-size:13px;color:#8b8b8b">أو</span>
                    <span style="height:1px;background:rgba(128,128,128,.25);flex:1"></span>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-link"></i>
                        رابط صورة الصحفي
                    </label>

                    <input
                        type="url"
                        name="photo_url"
                        id="photoUrl"
                        class="form-control"
                        value="{{ old('photo_url') }}"
                        placeholder="https://example.com/journalist.jpg"
                    >

                    <small
                        style="
                            display:block;
                            margin-top:7px;
                            color:#8b8b8b;
                        "
                    >
                        عند رفع صورة من الجهاز وإضافة رابط معًا، سيتم اعتماد الصورة المرفوعة.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_bio') }}
                    </label>

                    <textarea
                        name="bio"
                        class="form-control"
                        rows="5"
                        placeholder="{{ __('admin.placeholder_bio') }}"
                    >{{ old('bio') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i
                                class="fa-brands fa-facebook"
                                style="color:#1877f2"
                            ></i>
                            Facebook
                        </label>

                        <input
                            type="url"
                            name="facebook"
                            class="form-control"
                            value="{{ old('facebook') }}"
                            placeholder="https://facebook.com/username"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i
                                class="fa-brands fa-x-twitter"
                                style="color:#000"
                            ></i>
                            Twitter / X
                        </label>

                        <input
                            type="url"
                            name="x_twitter"
                            class="form-control"
                            value="{{ old('x_twitter') }}"
                            placeholder="https://x.com/username"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i
                                class="fa-brands fa-instagram"
                                style="color:#e1306c"
                            ></i>
                            Instagram
                        </label>

                        <input
                            type="url"
                            name="instagram"
                            class="form-control"
                            value="{{ old('instagram') }}"
                            placeholder="https://instagram.com/username"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i
                                class="fa-brands fa-youtube"
                                style="color:#ff0000"
                            ></i>
                            YouTube
                        </label>

                        <input
                            type="url"
                            name="youtube"
                            class="form-control"
                            value="{{ old('youtube') }}"
                            placeholder="https://youtube.com/@channel"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(old('status', 1))
                        >
                        {{ __('admin.label_active_journalist') }}
                    </label>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i>
                        {{ __('admin.btn_save') }}
                    </button>

                    <a
                        href="{{ route('admin.journalists.index') }}"
                        class="btn btn-outline"
                    >
                        {{ __('admin.btn_cancel') }}
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('photoFile');
    const urlInput = document.getElementById('photoUrl');
    const preview = document.getElementById('journalistImagePreview');
    const placeholder = document.getElementById('journalistImagePlaceholder');

    function showPreview(source) {
        if (!source) {
            preview.removeAttribute('src');
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            return;
        }

        preview.src = source;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
    }

    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            showPreview(urlInput.value.trim());
            return;
        }

        if (!file.type.startsWith('image/')) {
            this.value = '';
            showPreview(urlInput.value.trim());
            alert('يرجى اختيار ملف صورة صالح.');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            showPreview(event.target.result);
        };

        reader.readAsDataURL(file);
    });

    urlInput.addEventListener('input', function () {
        if (!fileInput.files.length) {
            showPreview(this.value.trim());
        }
    });

    preview.addEventListener('error', function () {
        if (!fileInput.files.length) {
            showPreview(null);
        }
    });

    @if(old('photo_url'))
        showPreview(@json(old('photo_url')));
    @endif
});
</script>
@endpush