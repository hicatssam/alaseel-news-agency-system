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
            عرض وإدارة محتوى صفحة من نحن وأعضاء الفريق.
        </p>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="{{ route('about') }}" target="_blank" class="btn btn-outline">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            عرض للزائر
        </a>

        <a href="{{ route('admin.about.edit') }}" class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square"></i>
            تعديل المحتوى
        </a>
    </div>
</div>

@if(session('success'))
    <div class="about-alert about-alert-success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="about-alert about-alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

<div class="stats-grid" style="margin-bottom:20px">
    <div class="stat-card" style="border-color:#c9a84c">
        <div
            class="stat-icon"
            style="background:rgba(201,168,76,.1);color:#c9a84c"
        >
            <i class="fa-solid fa-file-lines"></i>
        </div>

        <div>
            <div class="stat-value">
                {{ $aboutPage ? 1 : 0 }}
            </div>

            <div class="stat-label">محتوى صفحة من نحن</div>

            <div class="stat-change {{ $aboutPage ? 'up' : 'down' }}">
                {{ $aboutPage ? 'تمت إضافة المحتوى' : 'لم تتم إضافة المحتوى' }}
            </div>
        </div>
    </div>

    <div class="stat-card" style="border-color:#3498db">
        <div
            class="stat-icon"
            style="background:#eaf4fd;color:#3498db"
        >
            <i class="fa-solid fa-users"></i>
        </div>

        <div>
            <div class="stat-value">
                {{ number_format($teamMembersCount) }}
            </div>

            <div class="stat-label">إجمالي أعضاء الفريق</div>
        </div>
    </div>

    <div class="stat-card" style="border-color:#27ae60">
        <div
            class="stat-icon"
            style="background:#eafaf1;color:#27ae60"
        >
            <i class="fa-solid fa-user-check"></i>
        </div>

        <div>
            <div class="stat-value">
                {{ number_format($activeTeamMembersCount) }}
            </div>

            <div class="stat-label">الأعضاء النشطون</div>

            <div class="stat-change up">
                يظهرون حاليًا للزوار
            </div>
        </div>
    </div>
</div>

@if($aboutPage)
    <div class="about-index-grid">
        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i
                            class="fa-solid fa-align-right"
                            style="color:#c9a84c"
                        ></i>
                        محتوى الصفحة
                    </span>

                    @if($aboutPage->is_active)
                        <span class="badge badge-success">نشط</span>
                    @else
                        <span class="badge badge-secondary">غير نشط</span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="about-content-field">
                        <span class="about-field-label">عنوان الصفحة</span>

                        <h3>{{ $aboutPage->title }}</h3>
                    </div>

                    @if(filled($aboutPage->subtitle))
                        <div class="about-content-field">
                            <span class="about-field-label">
                                العنوان التعريفي
                            </span>

                            <p>{{ $aboutPage->subtitle }}</p>
                        </div>
                    @endif

                    <div class="about-content-field">
                        <span class="about-field-label">
                            المحتوى الرئيسي
                        </span>

                        <p>
                            {{
                                \Illuminate\Support\Str::limit(
                                    trim(strip_tags($aboutPage->content)),
                                    500
                                )
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i
                            class="fa-solid fa-bullseye"
                            style="color:#c9a84c"
                        ></i>
                        الرؤية والرسالة والقيم
                    </span>
                </div>

                <div class="card-body">
                    <div class="about-details-grid">
                        <article class="about-detail-card">
                            <div class="about-detail-icon">
                                <i class="fa-solid fa-eye"></i>
                            </div>

                            <h4>رؤيتنا</h4>

                            @if(filled($aboutPage->vision))
                                <p>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            trim(strip_tags($aboutPage->vision)),
                                            220
                                        )
                                    }}
                                </p>
                            @else
                                <span class="about-empty-label">
                                    لم تتم الإضافة
                                </span>
                            @endif
                        </article>

                        <article class="about-detail-card">
                            <div class="about-detail-icon">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>

                            <h4>رسالتنا</h4>

                            @if(filled($aboutPage->mission))
                                <p>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            trim(strip_tags($aboutPage->mission)),
                                            220
                                        )
                                    }}
                                </p>
                            @else
                                <span class="about-empty-label">
                                    لم تتم الإضافة
                                </span>
                            @endif
                        </article>

                        <article class="about-detail-card">
                            <div class="about-detail-icon">
                                <i class="fa-solid fa-heart"></i>
                            </div>

                            <h4>قيمنا</h4>

                            @if(filled($aboutPage->values))
                                <p>
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            trim(strip_tags($aboutPage->values)),
                                            220
                                        )
                                    }}
                                </p>
                            @else
                                <span class="about-empty-label">
                                    لم تتم الإضافة
                                </span>
                            @endif
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i
                            class="fa-solid fa-image"
                            style="color:#c9a84c"
                        ></i>
                        صورة الصفحة
                    </span>
                </div>

                <div class="card-body">
                    @if(filled($aboutPage->image_url))
                        <img
                            src="{{ $aboutPage->image_url }}"
                            alt="{{ $aboutPage->title }}"
                            class="about-index-image"
                        >
                    @else
                        <div class="about-index-image-placeholder">
                            <i class="fa-regular fa-image"></i>
                            <span>لا توجد صورة</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i
                            class="fa-solid fa-people-group"
                            style="color:#c9a84c"
                        ></i>
                        أعضاء الفريق
                    </span>
                </div>

                <div class="card-body">
                    <div class="team-count-row">
                        <span>إجمالي الأعضاء</span>
                        <strong>{{ number_format($teamMembersCount) }}</strong>
                    </div>

                    <div class="team-count-row">
                        <span>الأعضاء النشطون</span>
                        <strong style="color:#27ae60">
                            {{ number_format($activeTeamMembersCount) }}
                        </strong>
                    </div>

                    <a
                        href="{{ route('admin.team-members.index') }}"
                        class="btn btn-outline"
                        style="width:100%;justify-content:center;margin-top:18px"
                    >
                        <i class="fa-solid fa-users-gear"></i>
                        إدارة أعضاء الفريق
                    </a>
                </div>
            </div>

            <a
                href="{{ route('admin.about.edit') }}"
                class="btn btn-primary"
                style="width:100%;justify-content:center;padding:12px"
            >
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل صفحة من نحن
            </a>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="empty-state" style="padding:60px 20px">
                <i
                    class="fa-solid fa-circle-info"
                    style="display:block;margin-bottom:15px;color:#c9a84c;font-size:55px"
                ></i>

                <h3 style="margin:0 0 8px;color:#1a1a2e">
                    لم تتم إضافة محتوى صفحة من نحن
                </h3>

                <p style="margin:0 0 20px;color:#999">
                    أضف العنوان والمحتوى والرؤية والرسالة لتظهر الصفحة للزوار.
                </p>

                <a href="{{ route('admin.about.edit') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    إضافة المحتوى
                </a>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .about-alert {
        display:flex;
        align-items:center;
        gap:8px;
        margin-bottom:20px;
        padding:14px 18px;
        border-radius:8px;
        font-size:13px;
        font-weight:700;
    }

    .about-alert-success {
        border:1px solid #b7e4c7;
        color:#19713b;
        background:#eafaf1;
    }

    .about-alert-error {
        border:1px solid #f3c2c2;
        color:#b83232;
        background:#fdf0f0;
    }

    .about-index-grid {
        display:grid;
        grid-template-columns:minmax(0,1.6fr) minmax(280px,.65fr);
        gap:20px;
        align-items:start;
    }

    .about-content-field + .about-content-field {
        margin-top:24px;
        padding-top:20px;
        border-top:1px solid #eee;
    }

    .about-field-label {
        display:block;
        margin-bottom:7px;
        color:#c9a84c;
        font-size:11px;
        font-weight:800;
    }

    .about-content-field h3 {
        margin:0;
        color:#1a1a2e;
        font-size:22px;
        font-weight:800;
    }

    .about-content-field p {
        margin:0;
        color:#666;
        font-size:13px;
        line-height:2;
    }

    .about-details-grid {
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:15px;
    }

    .about-detail-card {
        padding:18px;
        border:1px solid #eee;
        border-radius:8px;
        background:#fafafa;
    }

    .about-detail-icon {
        display:flex;
        width:38px;
        height:38px;
        align-items:center;
        justify-content:center;
        margin-bottom:12px;
        border-radius:50%;
        color:#c9a84c;
        background:rgba(201,168,76,.1);
    }

    .about-detail-card h4 {
        margin:0 0 8px;
        color:#1a1a2e;
        font-size:14px;
    }

    .about-detail-card p {
        margin:0;
        color:#777;
        font-size:11.5px;
        line-height:1.9;
    }

    .about-empty-label {
        color:#aaa;
        font-size:11px;
    }

    .about-index-image {
        display:block;
        width:100%;
        max-height:300px;
        border-radius:8px;
        object-fit:cover;
    }

    .about-index-image-placeholder {
        display:flex;
        min-height:220px;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        gap:10px;
        border:2px dashed #ddd;
        border-radius:8px;
        color:#aaa;
        background:#fafafa;
    }

    .about-index-image-placeholder i {
        color:#c9a84c;
        font-size:42px;
        opacity:.65;
    }

    .team-count-row {
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:11px 0;
        border-bottom:1px solid #eee;
        color:#666;
        font-size:12px;
    }

    .team-count-row strong {
        color:#c9a84c;
        font-size:17px;
    }

    @media(max-width:1000px) {
        .about-index-grid {
            grid-template-columns:1fr;
        }
    }

    @media(max-width:750px) {
        .about-details-grid {
            grid-template-columns:1fr;
        }
    }
</style>
@endpush