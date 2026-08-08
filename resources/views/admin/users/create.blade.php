@extends('layouts.admin')

@section('title', __('admin.users_create'))

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">
        {{ __('admin.nav_users') }}
    </a>

    <span class="sep">›</span>

    {{ __('admin.btn_add') }}
@endsection

@section('content')

<div style="max-width:650px">

    <form
        method="POST"
        action="{{ route('admin.users.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="card">
            <div class="card-body">

                {{-- صورة المستخدم --}}
                <div class="form-group">
                    <label class="form-label">
                        صورة المستخدم
                        <span style="color:#e74c3c">*</span>
                    </label>

                    <div class="photo-upload-wrapper">
                        <div class="photo-preview" id="photoPreview">
                            <i class="fa-solid fa-user"></i>

                            <img
                                id="photoPreviewImage"
                                src=""
                                alt="معاينة صورة المستخدم"
                                style="display:none"
                            >
                        </div>

                        <div class="photo-upload-content">
                            <input
                                type="file"
                                name="photo"
                                id="photo"
                                class="form-control @error('photo') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                required
                            >

                            <small class="form-help">
                                الصيغ المسموحة: JPG، JPEG، PNG، WEBP.
                                الحد الأقصى: 4MB.
                            </small>

                            @error('photo')
                                <div class="field-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- الاسم --}}
                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_name') }}
                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- البريد الإلكتروني --}}
                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_email') }}
                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                    >

                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- كلمة المرور --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_password') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <div class="password-field">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password"
                                aria-label="إظهار كلمة المرور"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            {{ __('admin.label_password_confirm') }}
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <div class="password-field">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_confirmation"
                                aria-label="إظهار تأكيد كلمة المرور"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- الهاتف --}}
                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_phone') }}
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                    >

                    @error('phone')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- الأدوار --}}
                <div class="form-group">
                    <label class="form-label">
                        {{ __('admin.label_permissions') }}
                    </label>

                    <div class="roles-wrapper">
                        @forelse($roles as $role)
                            <label class="role-option">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->id }}"
                                    @checked(in_array($role->id, old('roles', [])))
                                >

                                <span>
                                    <i class="fa-solid fa-shield-halved"></i>
                                    {{ $role->name }}
                                </span>
                            </label>
                        @empty
                            <div class="empty-roles">
                                لا توجد أدوار متاحة.
                            </div>
                        @endforelse
                    </div>

                    @error('roles')
                        <div class="field-error">{{ $message }}</div>
                    @enderror

                    @error('roles.*')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- حالة الحساب --}}
                <div class="form-group">
                    <label class="status-option">
                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            @checked(old('status', '1') == '1')
                        >

                        <span class="status-switch"></span>

                        <span>
                            {{ __('admin.label_active_user') }}
                        </span>
                    </label>
                </div>

                {{-- الأزرار --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i>
                        {{ __('admin.btn_save') }}
                    </button>

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="btn btn-outline"
                    >
                        {{ __('admin.btn_cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .photo-upload-wrapper {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 18px;
        border: 1px dashed #d7c47e;
        border-radius: 12px;
        background: #fffdf7;
    }

    .photo-preview {
        width: 100px;
        height: 100px;
        display: flex;
        flex: 0 0 100px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #c9a84c;
        border: 3px solid #fff;
        border-radius: 50%;
        background: #f5ecd1;
        box-shadow: 0 5px 18px rgba(0, 0, 0, .12);
        font-size: 36px;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-upload-content {
        flex: 1;
        min-width: 0;
    }

    .form-help {
        display: block;
        margin-top: 7px;
        color: #888;
        font-size: 12px;
    }

    .field-error {
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
        font-weight: 600;
    }

    .is-invalid {
        border-color: #dc2626 !important;
    }

    .password-field {
        position: relative;
    }

    .password-field .form-control {
        padding-inline-end: 45px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        inset-inline-end: 7px;
        width: 34px;
        height: 34px;
        padding: 0;
        color: #777;
        border: 0;
        border-radius: 7px;
        background: transparent;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .password-toggle:hover {
        color: #c9a84c;
        background: rgba(201, 168, 76, .12);
    }

    .roles-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .role-option {
        cursor: pointer;
    }

    .role-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .role-option span {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        color: #555;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        transition: .2s ease;
    }

    .role-option input:checked + span {
        color: #806817;
        border-color: #c9a84c;
        background: rgba(201, 168, 76, .16);
        box-shadow: 0 0 0 2px rgba(201, 168, 76, .08);
    }

    .empty-roles {
        color: #888;
        font-size: 13px;
    }

    .status-option {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 700;
    }

    .status-option input {
        position: absolute;
        opacity: 0;
    }

    .status-switch {
        position: relative;
        width: 44px;
        height: 24px;
        border-radius: 999px;
        background: #cbd5e1;
        transition: .2s ease;
    }

    .status-switch::after {
        position: absolute;
        top: 3px;
        inset-inline-start: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .2);
        content: "";
        transition: .2s ease;
    }

    .status-option input:checked + .status-switch {
        background: #22a45d;
    }

    .status-option input:checked + .status-switch::after {
        transform: translateX(20px);
    }

    [dir="rtl"] .status-option input:checked + .status-switch::after {
        transform: translateX(-20px);
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 5px;
    }

    @media (max-width: 600px) {
        .photo-upload-wrapper {
            align-items: stretch;
            flex-direction: column;
        }

        .photo-preview {
            margin: auto;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('photo');
        const previewImage = document.getElementById('photoPreviewImage');
        const previewIcon = document.querySelector('#photoPreview > i');

        photoInput?.addEventListener('change', function () {
            const file = this.files?.[0];

            if (!file) {
                previewImage.src = '';
                previewImage.style.display = 'none';
                previewIcon.style.display = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                this.value = '';
                alert('يرجى اختيار ملف صورة صالح.');
                return;
            }

            const reader = new FileReader();

            reader.onload = function (event) {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
                previewIcon.style.display = 'none';
            };

            reader.readAsDataURL(file);
        });

        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.target);

                if (!input) {
                    return;
                }

                const showPassword = input.type === 'password';
                const icon = button.querySelector('i');

                input.type = showPassword ? 'text' : 'password';

                icon.classList.toggle('fa-eye', !showPassword);
                icon.classList.toggle('fa-eye-slash', showPassword);
            });
        });
    });
</script>

@endsection