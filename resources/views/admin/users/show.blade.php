@extends('layouts.admin')

@section('title', 'عرض المستخدم')

@section('breadcrumb')
    <a href="{{ route('admin.users.index') }}">
        {{ __('admin.nav_users') }}
    </a>

    <span style="margin-inline:6px">/</span>

    <span>{{ $user->name }}</span>
@endsection

@section('content')

@php
    $userPhoto = null;

    if (!empty($user->photo)) {
        $photoPath = ltrim($user->photo, '/');

        if (
            \Illuminate\Support\Str::startsWith(
                $photoPath,
                ['http://', 'https://', '//']
            )
        ) {
            $userPhoto = $user->photo;
        } else {
            $photoPath = preg_replace(
                '#^(public/)?storage/#',
                '',
                $photoPath
            );

            $userPhoto = asset('storage/' . $photoPath);
        }
    }

    $firstLetter = mb_strtoupper(
        mb_substr(trim($user->name), 0, 1)
    );

    $isCurrentUser = $user->id === auth()->id();
@endphp

<div class="user-show-page">

    {{-- رسائل النجاح والخطأ --}}
    @if(session('success'))
        <div class="alert-message alert-success-message">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-message alert-danger-message">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ترويسة الصفحة --}}
    <div class="page-actions">
        <a
            href="{{ route('admin.users.index') }}"
            class="btn btn-secondary"
        >
            <i class="fa-solid fa-arrow-right"></i>
            العودة إلى المستخدمين
        </a>

        <a
            href="{{ route('admin.users.edit', $user) }}"
            class="btn btn-primary"
        >
            <i class="fa-solid fa-pen"></i>
            تعديل المستخدم
        </a>
    </div>

    <div class="user-profile-grid">

        {{-- البطاقة الشخصية --}}
        <div class="card user-profile-card">
            <div class="profile-cover"></div>

            <div class="profile-content">
                <div class="profile-avatar">
                    @if($userPhoto)
                        <img
                            src="{{ $userPhoto }}"
                            alt="{{ $user->name }}"
                            onerror="
                                this.style.display='none';
                                this.nextElementSibling.style.display='flex';
                            "
                        >

                        <span
                            class="profile-avatar-fallback"
                            style="display:none"
                        >
                            {{ $firstLetter }}
                        </span>
                    @else
                        <span class="profile-avatar-fallback">
                            {{ $firstLetter }}
                        </span>
                    @endif
                </div>

                <h2 class="profile-name">
                    {{ $user->name }}
                </h2>

                <div class="profile-email">
                    {{ $user->email }}
                </div>

                <div class="profile-status">
                    @if($user->status)
                        <span class="badge badge-success">
                            <i class="fa-solid fa-circle-check"></i>
                            مستخدم نشط
                        </span>
                    @else
                        <span class="badge badge-danger">
                            <i class="fa-solid fa-circle-xmark"></i>
                            مستخدم معطّل
                        </span>
                    @endif
                </div>

                <div class="profile-roles">
                    @forelse($user->roles as $role)
                        <span class="badge badge-gold">
                            <i class="fa-solid fa-shield-halved"></i>
                            {{ $role->name }}
                        </span>
                    @empty
                        <span class="empty-role">
                            لا توجد أدوار مخصصة
                        </span>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- بيانات المستخدم --}}
        <div class="card details-card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-user"></i>
                    بيانات المستخدم
                </span>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-id-card"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            رقم المستخدم
                        </span>

                        <strong class="detail-value">
                            #{{ $user->id }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            الاسم الكامل
                        </span>

                        <strong class="detail-value">
                            {{ $user->name }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            البريد الإلكتروني
                        </span>

                        <strong class="detail-value">
                            {{ $user->email }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            رقم الهاتف
                        </span>

                        <strong class="detail-value">
                            {{ $user->phone ?: 'غير مسجل' }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            تاريخ إنشاء الحساب
                        </span>

                        <strong class="detail-value">
                            {{ $user->created_at?->format('Y/m/d - H:i') ?? '—' }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            آخر تسجيل دخول
                        </span>

                        <strong class="detail-value">
                            {{ $user->last_login_at?->format('Y/m/d - H:i') ?? 'لم يسجل الدخول بعد' }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            آخر تحديث للحساب
                        </span>

                        <strong class="detail-value">
                            {{ $user->updated_at?->format('Y/m/d - H:i') ?? '—' }}
                        </strong>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div>
                        <span class="detail-label">
                            كلمة المرور
                        </span>

                        <strong class="detail-value password-hidden">
                            ••••••••••••
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="management-grid">

        {{-- تغيير كلمة المرور --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-key"></i>
                    تغيير كلمة المرور
                </span>
            </div>

            <form
                method="POST"
                action="{{ route('admin.users.update-password', $user) }}"
                class="management-form"
            >
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="password">
                        كلمة المرور الجديدة
                        <span class="required">*</span>
                    </label>

                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            minlength="8"
                            autocomplete="new-password"
                            required
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
                        <small class="field-error">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        تأكيد كلمة المرور
                        <span class="required">*</span>
                    </label>

                    <div class="password-field">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
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

                <div class="password-notice">
                    <i class="fa-solid fa-circle-info"></i>

                    <span>
                        لا يمكن عرض كلمة المرور الحالية لأنها محفوظة
                        بصورة مشفّرة. يمكنك فقط استبدالها بكلمة جديدة.
                    </span>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    حفظ كلمة المرور
                </button>
            </form>
        </div>

        {{-- حالة المستخدم --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-user-shield"></i>
                    حالة الحساب
                </span>
            </div>

            <div class="status-management">
                <div class="status-summary">
                    <div class="status-summary-icon {{ $user->status ? 'active' : 'disabled' }}">
                        <i class="fa-solid {{ $user->status ? 'fa-user-check' : 'fa-user-slash' }}"></i>
                    </div>

                    <div>
                        <h3>
                            {{ $user->status ? 'الحساب نشط' : 'الحساب معطّل' }}
                        </h3>

                        <p>
                            @if($user->status)
                                يستطيع المستخدم حاليًا تسجيل الدخول واستخدام النظام.
                            @else
                                تم تعطيل وصول المستخدم إلى النظام.
                            @endif
                        </p>
                    </div>
                </div>

                @if($isCurrentUser)
                    <div class="self-account-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>

                        لا يمكنك تعطيل الحساب الذي تستخدمه حاليًا.
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ route('admin.users.toggle-status', $user) }}"
                        onsubmit="return confirm(
                            {{ \Illuminate\Support\Js::from(
                                $user->status
                                    ? 'هل أنت متأكد من تعطيل هذا المستخدم؟'
                                    : 'هل أنت متأكد من تفعيل هذا المستخدم؟'
                            ) }}
                        )"
                    >
                        @csrf
                        @method('PATCH')

                        @if($user->status)
                            <button
                                type="submit"
                                class="btn btn-danger status-button"
                            >
                                <i class="fa-solid fa-user-slash"></i>
                                تعطيل المستخدم
                            </button>
                        @else
                            <button
                                type="submit"
                                class="btn btn-success status-button"
                            >
                                <i class="fa-solid fa-user-check"></i>
                                تفعيل المستخدم
                            </button>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .user-show-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .page-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .alert-message {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 10px;
        font-weight: 700;
    }

    .alert-success-message {
        color: #166534;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
    }

    .alert-danger-message {
        color: #991b1b;
        border: 1px solid #fecaca;
        background: #fef2f2;
    }

    .user-profile-grid {
        display: grid;
        grid-template-columns: minmax(260px, 340px) minmax(0, 1fr);
        gap: 20px;
    }

    .user-profile-card {
        position: relative;
        overflow: hidden;
    }

    .profile-cover {
        height: 115px;
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, .22),
                transparent 35%
            ),
            linear-gradient(
                135deg,
                #1a1a2e,
                #34345a 55%,
                #c9a84c
            );
    }

    .profile-content {
        padding: 0 24px 28px;
        text-align: center;
    }

    .profile-avatar {
        width: 116px;
        height: 116px;
        margin: -58px auto 16px;
        overflow: hidden;
        border: 5px solid #fff;
        border-radius: 50%;
        background: #f8f1dc;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .16);
    }

    .profile-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .profile-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c9a84c;
        font-size: 42px;
        font-weight: 900;
    }

    .profile-name {
        margin: 0 0 5px;
        color: #1a1a2e;
        font-size: 22px;
        font-weight: 900;
    }

    .profile-email {
        color: #777;
        font-size: 13px;
        word-break: break-word;
    }

    .profile-status {
        margin-top: 15px;
    }

    .profile-roles {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 14px;
    }

    .empty-role {
        color: #999;
        font-size: 12px;
    }

    .details-card .card-title i,
    .card-title i {
        margin-inline-end: 7px;
        color: #c9a84c;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1px;
        padding: 1px;
        background: #eee;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 13px;
        min-height: 92px;
        padding: 18px;
        background: #fff;
    }

    .detail-icon {
        width: 42px;
        height: 42px;
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        color: #c9a84c;
        border-radius: 10px;
        background: rgba(201, 168, 76, .13);
    }

    .detail-label {
        display: block;
        margin-bottom: 6px;
        color: #999;
        font-size: 12px;
    }

    .detail-value {
        display: block;
        color: #1a1a2e;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .password-hidden {
        letter-spacing: 3px;
    }

    .management-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .management-form,
    .status-management {
        padding: 22px;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-size: 13px;
        font-weight: 700;
    }

    .required,
    .field-error {
        color: #dc2626;
    }

    .field-error {
        display: block;
        margin-top: 6px;
        font-size: 12px;
    }

    .password-field {
        position: relative;
    }

    .password-field .form-control {
        padding-inline-end: 46px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        inset-inline-end: 8px;
        width: 34px;
        height: 34px;
        border: 0;
        color: #777;
        border-radius: 7px;
        background: transparent;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .password-toggle:hover {
        color: #c9a84c;
        background: rgba(201, 168, 76, .12);
    }

    .password-notice {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 18px;
        padding: 12px;
        color: #765d17;
        border: 1px solid #f1dfaa;
        border-radius: 8px;
        background: #fffbeb;
        font-size: 12px;
        line-height: 1.8;
    }

    .status-summary {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 23px;
    }

    .status-summary-icon {
        width: 58px;
        height: 58px;
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 23px;
    }

    .status-summary-icon.active {
        color: #15803d;
        background: #dcfce7;
    }

    .status-summary-icon.disabled {
        color: #b91c1c;
        background: #fee2e2;
    }

    .status-summary h3 {
        margin: 0 0 6px;
        color: #1a1a2e;
        font-size: 17px;
    }

    .status-summary p {
        margin: 0;
        color: #777;
        font-size: 13px;
        line-height: 1.7;
    }

    .status-button {
        width: 100%;
        justify-content: center;
    }

    .self-account-warning {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 13px;
        color: #92400e;
        border: 1px solid #fde68a;
        border-radius: 9px;
        background: #fffbeb;
        font-size: 13px;
        font-weight: 700;
    }

    .btn-success {
        color: #fff;
        border-color: #15803d;
        background: #15803d;
    }

    .btn-success:hover {
        border-color: #166534;
        background: #166534;
    }

    @media (max-width: 1000px) {
        .user-profile-grid,
        .management-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 680px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

        .page-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .page-actions .btn {
            justify-content: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(
                    button.dataset.target
                );

                if (!input) {
                    return;
                }

                const isHidden = input.type === 'password';
                const icon = button.querySelector('i');

                input.type = isHidden ? 'text' : 'password';

                icon.classList.toggle('fa-eye', !isHidden);
                icon.classList.toggle('fa-eye-slash', isHidden);

                button.setAttribute(
                    'aria-label',
                    isHidden
                        ? 'إخفاء كلمة المرور'
                        : 'إظهار كلمة المرور'
                );
            });
        });
    });
</script>

@endsection