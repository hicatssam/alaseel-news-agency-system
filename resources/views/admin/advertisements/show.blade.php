@extends('layouts.admin')

@section('title', 'تفاصيل الإعلان')

@section('breadcrumb')
    {{ __('admin.nav_advertisements') }} / {{ $advertisement->title }}
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | تجهيز رابط صورة الإعلان
    |--------------------------------------------------------------------------
    | إذا كانت الصورة رابطًا خارجيًا نستخدمها مباشرة.
    | وإذا كانت ملفًا محليًا نعرضها من storage.
    */
    $imageValue = $advertisement->image ?? null;
    $imageUrl = null;

    if ($imageValue) {
        $isExternalImage =
            str_starts_with($imageValue, 'http://') ||
            str_starts_with($imageValue, 'https://');

        $imageUrl = $isExternalImage
            ? $imageValue
            : asset('storage/' . ltrim($imageValue, '/'));
    }

    $views = (int) ($advertisement->views ?? 0);
    $clicks = (int) ($advertisement->clicks ?? 0);

    $clickRate = $views > 0
        ? ($clicks / $views) * 100
        : 0;

    $positionLabels = [
        'header'         => 'رأس الصفحة',
        'homepage'       => 'الصفحة الرئيسية',
        'sidebar'        => 'الشريط الجانبي',
        'inside_article' => 'داخل المقال',
        'footer'         => 'تذييل الصفحة',
        'popup'          => 'إعلان منبثق',
        'video'          => 'إعلان فيديو',
    ];

    $typeLabels = [
        'image'  => 'صورة',
        'video'  => 'فيديو',
        'banner' => 'بانر',
        'code'   => 'كود إعلاني',
    ];

    $positionName = $positionLabels[$advertisement->position] ?? $advertisement->position;
    $typeName = $typeLabels[$advertisement->type] ?? ($advertisement->type ?: '—');
@endphp

{{-- أزرار الصفحة --}}
<div class="ad-page-actions">
    <a href="{{ route('admin.advertisements.index') }}"
       class="btn btn-secondary">
        <i class="fa-solid fa-arrow-right"></i>
        رجوع
    </a>

    <div class="ad-actions-group">
        @if($advertisement->link)
            <a href="{{ $advertisement->link }}"
               target="_blank"
               rel="noopener noreferrer"
               class="btn btn-secondary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                فتح رابط الإعلان
            </a>
        @endif

        <a href="{{ route('admin.advertisements.edit', $advertisement) }}"
           class="btn btn-primary">
            <i class="fa-solid fa-pen"></i>
            تعديل الإعلان
        </a>
    </div>
</div>

<div class="ad-details-grid">

    {{-- معاينة الإعلان --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-eye"></i>
                معاينة الإعلان
            </span>
        </div>

        <div class="ad-preview-content">
            @if($imageUrl)
                <div class="ad-image-wrapper">

                    @if($advertisement->link)
                        <a href="{{ $advertisement->link }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="ad-image-link">

                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $advertisement->title }}"
                                class="ad-preview-image"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <div class="ad-image-error">
                                <i class="fa-solid fa-image"></i>
                                <strong>تعذّر تحميل صورة الإعلان</strong>

                                <span>
                                    قد يكون الموقع الخارجي يمنع عرض الصورة داخل مواقع أخرى.
                                </span>

                                <small>
                                    اضغط هنا لفتح رابط الإعلان.
                                </small>
                            </div>
                        </a>
                    @else
                        <div class="ad-image-link">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $advertisement->title }}"
                                class="ad-preview-image"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <div class="ad-image-error">
                                <i class="fa-solid fa-image"></i>
                                <strong>تعذّر تحميل صورة الإعلان</strong>

                                @if($isExternalImage ?? false)
                                    <span>
                                        الموقع الخارجي يمنع عرض الصورة داخل مواقع أخرى.
                                    </span>

                                    <a href="{{ $imageUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-secondary btn-sm">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        فتح رابط الصورة
                                    </a>
                                @else
                                    <span>
                                        تأكد من وجود الصورة ومن تنفيذ أمر ربط التخزين.
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="ad-image-path">
                    <i class="fa-solid fa-link"></i>

                    <a href="{{ $imageUrl }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        فتح الصورة في نافذة جديدة
                    </a>
                </div>

            @elseif(!empty($advertisement->code))
                <div class="ad-code-preview">
                    {!! $advertisement->code !!}
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-image"></i>
                    <p>لا توجد صورة أو معاينة لهذا الإعلان</p>
                </div>
            @endif
        </div>
    </div>

    {{-- بيانات الإعلان --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="fa-solid fa-circle-info"></i>
                تفاصيل الإعلان
            </span>
        </div>

        <div class="ad-info-content">
            <div class="ad-info-list">

                {{-- العنوان --}}
                <div class="ad-info-item">
                    <div class="ad-info-label">عنوان الإعلان</div>

                    <div class="ad-title">
                        {{ $advertisement->title }}
                    </div>
                </div>

                {{-- الحالة --}}
                <div class="ad-info-item">
                    <div class="ad-info-label">الحالة</div>

                    <div>
                        @if($advertisement->status && !$advertisement->is_expired)
                            <span class="badge badge-success">
                                نشط
                            </span>
                        @elseif($advertisement->is_expired)
                            <span class="badge badge-danger">
                                منتهي
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                معطّل
                            </span>
                        @endif
                    </div>
                </div>

                {{-- الموقع والنوع --}}
                <div class="ad-two-columns">
                    <div class="ad-info-item">
                        <div class="ad-info-label">موقع الإعلان</div>

                        <span class="badge badge-info">
                            {{ $positionName }}
                        </span>
                    </div>

                    <div class="ad-info-item">
                        <div class="ad-info-label">نوع الإعلان</div>

                        <strong>{{ $typeName }}</strong>
                    </div>
                </div>

                {{-- المشاهدات والنقرات --}}
                <div class="ad-statistics">
                    <div class="ad-stat-box ad-stat-views">
                        <div class="ad-info-label">المشاهدات</div>

                        <div class="ad-stat-number">
                            {{ number_format($views) }}
                        </div>
                    </div>

                    <div class="ad-stat-box ad-stat-clicks">
                        <div class="ad-info-label">النقرات</div>

                        <div class="ad-stat-number">
                            {{ number_format($clicks) }}
                        </div>
                    </div>
                </div>

                {{-- نسبة النقر --}}
                <div class="ad-info-item">
                    <div class="ad-info-label">نسبة النقر إلى الظهور</div>

                    <div class="ad-click-rate">
                        <strong>{{ number_format($clickRate, 2) }}%</strong>

                        <div class="ad-progress">
                            <div
                                class="ad-progress-value"
                                style="width:{{ min($clickRate, 100) }}%">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- رابط الإعلان --}}
                <div class="ad-info-item">
                    <div class="ad-info-label">رابط الإعلان</div>

                    @if($advertisement->link)
                        <a href="{{ $advertisement->link }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="ad-external-link">
                            {{ $advertisement->link }}

                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    @else
                        <span>—</span>
                    @endif
                </div>

                {{-- رابط الصورة --}}
                <div class="ad-info-item">
                    <div class="ad-info-label">مسار صورة الإعلان</div>

                    @if($imageValue)
                        <span class="ad-image-value">
                            {{ $imageValue }}
                        </span>
                    @else
                        <span>—</span>
                    @endif
                </div>

                {{-- تواريخ التشغيل --}}
                <div class="ad-two-columns">
                    <div class="ad-info-item">
                        <div class="ad-info-label">تاريخ البداية</div>

                        <strong>
                            {{ $advertisement->starts_at?->format('Y/m/d H:i') ?? '—' }}
                        </strong>
                    </div>

                    <div class="ad-info-item">
                        <div class="ad-info-label">تاريخ الانتهاء</div>

                        <strong>
                            {{ $advertisement->ends_at?->format('Y/m/d H:i') ?? 'غير محدد' }}
                        </strong>
                    </div>
                </div>

                {{-- تواريخ النظام --}}
                <div class="ad-two-columns">
                    <div class="ad-info-item">
                        <div class="ad-info-label">تاريخ الإنشاء</div>

                        <strong>
                            {{ $advertisement->created_at?->format('Y/m/d H:i') ?? '—' }}
                        </strong>
                    </div>

                    <div class="ad-info-item">
                        <div class="ad-info-label">آخر تحديث</div>

                        <strong>
                            {{ $advertisement->updated_at?->format('Y/m/d H:i') ?? '—' }}
                        </strong>
                    </div>
                </div>

                {{-- صاحب الإعلان --}}
                @if(!empty($advertisement->user_id))
                    <div class="ad-info-item">
                        <div class="ad-info-label">معرّف منشئ الإعلان</div>

                        <strong>#{{ $advertisement->user_id }}</strong>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

<style>
    .ad-page-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .ad-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ad-details-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(300px, 1fr);
        gap: 20px;
        align-items: start;
    }

    .ad-preview-content,
    .ad-info-content {
        padding: 20px;
    }

    .ad-image-wrapper {
        padding: 12px;
        background: #f7f7f7;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
        overflow: hidden;
    }

    .ad-image-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .ad-preview-image {
        display: block;
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        border-radius: 8px;
    }

    .ad-image-error {
        display: none;
        min-height: 250px;
        padding: 30px 20px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        color: #777;
        text-align: center;
    }

    .ad-image-error i {
        font-size: 42px;
        opacity: .55;
    }

    .ad-image-error strong {
        font-size: 16px;
    }

    .ad-image-error span,
    .ad-image-error small {
        line-height: 1.8;
    }

    .ad-image-path {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        margin-top: 12px;
        font-size: 13px;
    }

    .ad-code-preview {
        overflow: hidden;
        border-radius: 12px;
    }

    .ad-info-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .ad-info-item {
        min-width: 0;
    }

    .ad-info-label {
        margin-bottom: 6px;
        color: #888;
        font-size: 12px;
    }

    .ad-title {
        font-size: 18px;
        font-weight: 800;
        line-height: 1.7;
    }

    .ad-two-columns,
    .ad-statistics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .ad-stat-box {
        padding: 14px;
        border-radius: 10px;
    }

    .ad-stat-views {
        background: rgba(59, 130, 246, .08);
    }

    .ad-stat-clicks {
        background: rgba(16, 185, 129, .08);
    }

    .ad-stat-number {
        margin-top: 4px;
        font-size: 24px;
        font-weight: 900;
    }

    .ad-click-rate {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .ad-progress {
        width: 100%;
        height: 7px;
        overflow: hidden;
        background: rgba(148, 163, 184, .25);
        border-radius: 999px;
    }

    .ad-progress-value {
        height: 100%;
        min-width: 0;
        background: #10b981;
        border-radius: inherit;
    }

    .ad-external-link,
    .ad-image-value {
        display: inline-block;
        max-width: 100%;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.7;
    }

    .ad-external-link i {
        margin-right: 4px;
        font-size: 11px;
    }

    @media (max-width: 900px) {
        .ad-details-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .ad-page-actions,
        .ad-actions-group {
            width: 100%;
        }

        .ad-actions-group .btn {
            flex: 1;
        }

        .ad-two-columns,
        .ad-statistics {
            grid-template-columns: 1fr;
        }

        .ad-preview-content,
        .ad-info-content {
            padding: 14px;
        }
    }
</style>

@endsection