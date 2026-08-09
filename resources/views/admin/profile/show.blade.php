@extends('layouts.admin')

@section('title', __('admin.menu_profile'))

@section('breadcrumb')
    {{ __('admin.menu_profile') }}
@endsection

@section('content')
@php
    $storedPhoto = $user->photo;

    $photoIsExternal = filled($storedPhoto)
        && \Illuminate\Support\Str::startsWith(
            $storedPhoto,
            ['http://', 'https://', '//']
        );

    if ($photoIsExternal) {
        $avatarUrl = $storedPhoto;
    } elseif (filled($storedPhoto)) {
        $localPhotoPath = str_replace('\\', '/', $storedPhoto);

        $localPhotoPath = \Illuminate\Support\Str::after(
            $localPhotoPath,
            'storage/app/public/'
        );

        $localPhotoPath = \Illuminate\Support\Str::after(
            $localPhotoPath,
            'public/'
        );

        if (\Illuminate\Support\Str::startsWith($localPhotoPath, '/storage/')) {
            $localPhotoPath = \Illuminate\Support\Str::after(
                $localPhotoPath,
                '/storage/'
            );
        } elseif (\Illuminate\Support\Str::startsWith($localPhotoPath, 'storage/')) {
            $localPhotoPath = \Illuminate\Support\Str::after(
                $localPhotoPath,
                'storage/'
            );
        }

        $avatarUrl = \Illuminate\Support\Facades\Storage::disk('public')
            ->url(ltrim($localPhotoPath, '/'));
    } else {
        $avatarUrl = null;
    }

    $selectedPhotoSource = old(
        'photo_source',
        $photoIsExternal || blank($storedPhoto)
            ? 'url'
            : 'file'
    );
@endphp

<div style="max-width:620px">
    @if(session('success'))
        <div
            style="display:flex;align-items:center;gap:10px;padding:14px 18px;margin-bottom:16px;border:1px solid #b7e4c7;border-radius:8px;background:#eafaf1;color:#19713b;font-size:13px;font-weight:700"
        >
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            style="display:flex;align-items:center;gap:10px;padding:14px 18px;margin-bottom:16px;border:1px solid #f3c2c2;border-radius:8px;background:#fdf0f0;color:#b83232;font-size:13px;font-weight:700"
        >
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div
            style="padding:14px 18px;margin-bottom:16px;border:1px solid #f3c2c2;border-radius:8px;background:#fdf0f0;color:#b83232"
        >
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

    <form
        method="POST"
        action="{{ route('admin.profile.update') }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        {{-- Profile photo --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('admin.profile_avatar') }}
                </h3>
            </div>

            <div class="card-body">
                <div
                    style="display:flex;align-items:center;gap:20px;margin-bottom:18px"
                >
                    <div
                        id="avatar-preview-wrap"
                        style="flex-shrink:0"
                    >
                        @if($avatarUrl)
                            <img
                                id="avatar-preview"
                                src="{{ $avatarUrl }}"
                                alt="{{ $user->name }}"
                                style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)"
                            >
                        @else
                            <div
                                id="avatar-initials"
                                style="width:80px;height:80px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#111"
                            >
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div style="flex:1">
                        <p
                            style="color:rgba(255,255,255,.5);font-size:13px;margin:0 0 10px"
                        >
                            {{ __('admin.profile_avatar_hint') }}
                        </p>

                        <div
                            style="display:flex;gap:16px;margin-bottom:12px;flex-wrap:wrap"
                        >
                            <label
                                style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"
                            >
                                <input
                                    type="radio"
                                    name="photo_source"
                                    value="url"
                                    id="src_url"
                                    @checked($selectedPhotoSource === 'url')
                                    onchange="togglePhotoSource('url')"
                                >

                                {{ __('admin.profile_url') }}
                            </label>

                            <label
                                style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"
                            >
                                <input
                                    type="radio"
                                    name="photo_source"
                                    value="file"
                                    id="src_file"
                                    @checked($selectedPhotoSource === 'file')
                                    onchange="togglePhotoSource('file')"
                                >

                                {{ __('admin.profile_from_device') }}
                            </label>
                        </div>

                        <div
                            id="photo_url_wrap"
                            style="{{ $selectedPhotoSource === 'url' ? '' : 'display:none' }}"
                        >
                            <input
                                type="url"
                                name="photo_url"
                                id="photo_url_input"
                                class="form-control @error('photo_url') is-invalid @enderror"
                                value="{{ old('photo_url', $photoIsExternal ? $storedPhoto : '') }}"
                                placeholder="https://example.com/photo.jpg"
                                oninput="previewPhotoUrl(this.value)"
                            >

                            @error('photo_url')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div
                            id="photo_file_wrap"
                            style="{{ $selectedPhotoSource === 'file' ? '' : 'display:none' }}"
                        >
                            <input
                                type="file"
                                name="photo"
                                id="photo_file_input"
                                class="form-control @error('photo') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                onchange="previewPhotoFile(this)"
                            >

                            <p
                                style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px"
                            >
                                JPG, PNG, WEBP —
                                {{ __('admin.profile_max_size') }}
                            </p>

                            @error('photo')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @if(filled($storedPhoto))
                            <label
                                style="display:flex;align-items:center;gap:7px;margin-top:14px;cursor:pointer;color:#e74c3c;font-size:12px"
                            >
                                <input
                                    type="checkbox"
                                    name="remove_photo"
                                    value="1"
                                    @checked(old('remove_photo'))
                                >

                                <span>حذف الصورة الحالية</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal information --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('admin.profile_personal_info') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="name">
                        {{ __('admin.label_name') }}

                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        required
                    >

                    @error('name')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        {{ __('admin.label_email') }}

                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                    >

                    @error('email')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">
                        {{ __('admin.label_phone') }}
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="+966..."
                    >

                    @error('phone')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Change password --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <h3 class="card-title">
                    {{ __('admin.profile_change_password') }}
                </h3>
            </div>

            <div class="card-body">
                <p
                    style="color:rgba(255,255,255,.45);font-size:13px;margin:0 0 14px"
                >
                    {{ __('admin.profile_password_hint') }}
                </p>

                <div class="form-group">
                    <label class="form-label" for="password">
                        {{ __('admin.label_new_password') }}
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        autocomplete="new-password"
                    >

                    @error('password')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        class="form-label"
                        for="password_confirmation"
                    >
                        {{ __('admin.label_password_confirm') }}
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        autocomplete="new-password"
                    >
                </div>
            </div>
        </div>

        <div style="display:flex;gap:8px">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i>
                {{ __('admin.btn_save') }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .field-error {
        margin-top:6px;
        color:#e74c3c;
        font-size:11.5px;
        font-weight:700;
    }

    .is-invalid {
        border-color:#e74c3c !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function togglePhotoSource(source) {
        const urlWrapper = document.getElementById(
            'photo_url_wrap'
        );

        const fileWrapper = document.getElementById(
            'photo_file_wrap'
        );

        const urlInput = document.getElementById(
            'photo_url_input'
        );

        const fileInput = document.getElementById(
            'photo_file_input'
        );

        urlWrapper.style.display = source === 'url'
            ? ''
            : 'none';

        fileWrapper.style.display = source === 'file'
            ? ''
            : 'none';

        if (source === 'url') {
            fileInput.value = '';
        } else {
            urlInput.value = '';
        }
    }

    function updatePhotoPreview(source) {
        const previewWrapper = document.getElementById(
            'avatar-preview-wrap'
        );

        if (!previewWrapper || !source) {
            return;
        }

        let previewImage = document.getElementById(
            'avatar-preview'
        );

        if (!previewImage) {
            previewWrapper.innerHTML = '';

            previewImage = document.createElement('img');
            previewImage.id = 'avatar-preview';
            previewImage.alt = 'Profile photo';
            previewImage.style.cssText =
                'width:80px;height:80px;border-radius:50%;' +
                'object-fit:cover;border:2px solid var(--gold)';

            previewWrapper.appendChild(previewImage);
        }

        previewImage.src = source;
    }

    function previewPhotoUrl(url) {
        if (!url) {
            return;
        }

        updatePhotoPreview(url);
    }

    function previewPhotoFile(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];

        if (!file.type.startsWith('image/')) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            updatePhotoPreview(event.target.result);
        };

        reader.readAsDataURL(file);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const selectedSource = document.querySelector(
            'input[name="photo_source"]:checked'
        );

        togglePhotoSource(
            selectedSource?.value ?? 'url'
        );
    });
</script>
@endpush