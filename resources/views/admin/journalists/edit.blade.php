@extends('layouts.admin')

@section('title', __('admin.journalists_edit'))

@section('breadcrumb')
    <a href="{{ route('admin.journalists.index') }}">
        {{ __('admin.nav_journalists') }}
    </a>

    <span class="sep">›</span>
    {{ __('admin.btn_edit') }}
@endsection

@section('content')

<style>
    .journalist-photo-section {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }

    .journalist-photo-preview {
        width: 100px;
        height: 100px;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid rgba(201, 168, 76, .25);
        border-radius: 50%;
        background: rgba(201, 168, 76, .12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c9a84c;
        font-size: 34px;
        font-weight: 900;
    }

    .journalist-photo-preview img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .journalist-photo-fields {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .journalist-photo-help {
        color: #888;
        font-size: 12px;
        line-height: 1.7;
    }

    @media (max-width: 600px) {
        .journalist-photo-section {
            align-items: stretch;
            flex-direction: column;
        }

        .journalist-photo-preview {
            margin: auto;
        }
    }
</style>

<div style="max-width:700px">

    <form
        method="POST"
        action="{{ route('admin.journalists.update', $journalist) }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    {{ __('admin.journalists_edit') }}:
                    {{ $journalist->name }}
                </span>
            </div>

            <div class="card-body">

                {{-- صورة الصحفي --}}
                <div class="form-group">
                    <label class="form-label">صورة الصحفي</label>

                    <div class="journalist-photo-section">
                        <div class="journalist-photo-preview">
                            <img
                                id="photoPreview"
                                src="{{ $journalist->photo_url ?: '' }}"
                                alt="{{ $journalist->name }}"
                                @if(!$journalist->photo_url) style="display:none" @endif
                                onerror="
                                    this.style.display='none';
                                    document.getElementById('photoLetter').style.display='flex';
                                "
                            >

                            <span
                                id="photoLetter"
                                style="
                                    width:100%;
                                    height:100%;
                                    align-items:center;
                                    justify-content:center;
                                    {{ $journalist->photo_url ? 'display:none' : 'display:flex' }}
                                "
                            >
                                {{ mb_substr($journalist->name, 0, 1) }}
                            </span>
                        </div>

                        <div class="journalist-photo-fields">
                            <input
                                type="file"
                                name="photo_file"
                                id="photoFile"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <input
                                type="url"
                                name="photo_url"
                                class="form-control"
                                value="{{ old('photo_url', str_starts_with((string) $journalist->photo, 'http') ? $journalist->photo : '') }}"
                                placeholder="أو أدخل رابط الصورة https://..."
                            >

                            <div class="journalist-photo-help">
                                ارفع صورة جديدة أو أدخل رابطًا مباشرًا. إذا لم تختر
                                صورة جديدة، ستبقى الصورة الحالية محفوظة.
                            </div>

                            @error('photo_file')
                                <div style="color:#e74c3c;font-size:12px">
                                    {{ $message }}
                                </div>
                            @enderror

                            @error('photo_url')
                                <div style="color:#e74c3c;font-size:12px">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

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
                            value="{{ old('name', $journalist->name) }}"
                            required
                        >

                        @error('name')
                            <div style="color:#e74c3c;font-size:12px">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_job_title') }}
                        </label>

                        <input
                            type="text"
                            name="job_title"
                            class="form-control"
                            value="{{ old('job_title', $journalist->job_title) }}"
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
                            value="{{ old('email', $journalist->email) }}"
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
                            value="{{ old('phone', $journalist->phone) }}"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_bio') }}
                    </label>

                    <textarea
                        name="bio"
                        class="form-control"
                        rows="4"
                    >{{ old('bio', $journalist->bio) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-brands fa-facebook" style="color:#1877f2"></i>
                            Facebook
                        </label>

                        <input
                            type="url"
                            name="facebook"
                            class="form-control"
                            value="{{ old('facebook', $journalist->facebook) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-brands fa-x-twitter" style="color:#000"></i>
                            Twitter / X
                        </label>

                        <input
                            type="url"
                            name="x_twitter"
                            class="form-control"
                            value="{{ old('x_twitter', $journalist->x_twitter) }}"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-brands fa-instagram" style="color:#e1306c"></i>
                            Instagram
                        </label>

                        <input
                            type="url"
                            name="instagram"
                            class="form-control"
                            value="{{ old('instagram', $journalist->instagram) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fa-brands fa-youtube" style="color:#ff0000"></i>
                            YouTube
                        </label>

                        <input
                            type="url"
                            name="youtube"
                            class="form-control"
                            value="{{ old('youtube', $journalist->youtube) }}"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(old('status', $journalist->status))
                        >

                        {{ __('admin.label_active_journalist') }}
                    </label>
                </div>

                <div style="display:flex;gap:8px">
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

<script>
    document.getElementById('photoFile')?.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        const preview = document.getElementById('photoPreview');
        const letter = document.getElementById('photoLetter');

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
        letter.style.display = 'none';
    });
</script>
@endsection