@extends('layouts.admin')

@section('title', __('admin.nav_advertisements'))

@section('breadcrumb')
    {{ __('admin.nav_advertisements') }}
@endsection

@section('content')
<div class="advertisements-page">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        margin-bottom:20px;
        flex-wrap:wrap;
    ">
        <div>
            <h1 style="margin:0;color:#fff;font-size:24px">
                {{ __('admin.nav_advertisements') }}
            </h1>

            <p style="
                margin:6px 0 0;
                color:rgba(255,255,255,.45);
                font-size:12px;
            ">
                إدارة الإعلانات ومواضع ظهورها
            </p>
        </div>

        <a
            href="{{ route('admin.advertisements.create') }}"
            class="btn btn-primary"
        >
            <i class="fa-solid fa-plus"></i>
            إعلان جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="margin-bottom:20px">
        <div style="padding:18px">
            <form
                method="GET"
                action="{{ route('admin.advertisements.index') }}"
                style="
                    display:flex;
                    align-items:end;
                    gap:12px;
                    flex-wrap:wrap;
                "
            >
                <div style="flex:1;min-width:180px">
                    <label class="form-label">موضع الإعلان</label>

                    <select name="position" class="form-control">
                        <option value="">جميع المواضع</option>

                        <option
                            value="header"
                            @selected(request('position') === 'header')
                        >
                            أعلى الصفحة
                        </option>

                        <option
                            value="homepage"
                            @selected(request('position') === 'homepage')
                        >
                            الصفحة الرئيسية
                        </option>

                        <option
                            value="sidebar"
                            @selected(request('position') === 'sidebar')
                        >
                            الشريط الجانبي
                        </option>

                        <option
                            value="inside_article"
                            @selected(request('position') === 'inside_article')
                        >
                            داخل المقال
                        </option>

                        <option
                            value="footer"
                            @selected(request('position') === 'footer')
                        >
                            أسفل الصفحة
                        </option>

                        <option
                            value="popup"
                            @selected(request('position') === 'popup')
                        >
                            إعلان منبثق
                        </option>

                        <option
                            value="video"
                            @selected(request('position') === 'video')
                        >
                            قسم الفيديو
                        </option>
                    </select>
                </div>

                <div style="flex:1;min-width:180px">
                    <label class="form-label">الحالة</label>

                    <select name="status" class="form-control">
                        <option value="">جميع الحالات</option>

                        <option
                            value="1"
                            @selected(request('status') === '1')
                        >
                            مفعّل
                        </option>

                        <option
                            value="0"
                            @selected(request('status') === '0')
                        >
                            غير مفعّل
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>

                <a
                    href="{{ route('admin.advertisements.index') }}"
                    class="btn btn-secondary"
                >
                    إعادة ضبط
                </a>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x:auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>المحتوى</th>
                        <th>العنوان</th>
                        <th>الموضع</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>المشاهدات</th>
                        <th>النقرات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($ads as $advertisement)
                        <tr>
                            <td>
                                @if($advertisement->image_url)
                                    @if($advertisement->type === 'video')
                                        <div class="media-thumbnail">
                                            <i class="fa-solid fa-video"></i>
                                        </div>
                                    @else
                                        <img
                                            src="{{ $advertisement->image_url }}"
                                            alt="{{ $advertisement->title }}"
                                            class="advertisement-thumbnail"
                                        >
                                    @endif
                                @else
                                    <div class="media-thumbnail">
                                        <i class="fa-regular fa-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <strong style="color:#fff">
                                    {{ $advertisement->title }}
                                </strong>

                                <small style="
                                    display:block;
                                    margin-top:4px;
                                    color:rgba(255,255,255,.4);
                                ">
                                    #{{ $advertisement->id }}
                                </small>
                            </td>

                            <td>
                                @switch($advertisement->position)
                                    @case('header')
                                        أعلى الصفحة
                                        @break
                                    @case('homepage')
                                        الصفحة الرئيسية
                                        @break
                                    @case('sidebar')
                                        الشريط الجانبي
                                        @break
                                    @case('inside_article')
                                        داخل المقال
                                        @break
                                    @case('footer')
                                        أسفل الصفحة
                                        @break
                                    @case('popup')
                                        إعلان منبثق
                                        @break
                                    @case('video')
                                        قسم الفيديو
                                        @break
                                    @default
                                        {{ $advertisement->position }}
                                @endswitch
                            </td>

                            <td>
                                {{ $advertisement->type === 'video'
                                    ? 'فيديو'
                                    : 'صورة' }}
                            </td>

                            <td>
                                @if($advertisement->is_expired)
                                    <span class="status-label status-expired">
                                        منتهي
                                    </span>
                                @elseif($advertisement->status)
                                    <span class="status-label status-active">
                                        مفعّل
                                    </span>
                                @else
                                    <span class="status-label status-disabled">
                                        غير مفعّل
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ number_format($advertisement->views ?? 0) }}
                            </td>

                            <td>
                                {{ number_format($advertisement->clicks ?? 0) }}
                            </td>

                            <td>
                                <div class="table-actions">
                                    <a
                                        href="{{ route(
                                            'admin.advertisements.show',
                                            $advertisement
                                        ) }}"
                                        class="action-button view-button"
                                        title="عرض"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </a>

                                    <a
                                        href="{{ route(
                                            'admin.advertisements.edit',
                                            $advertisement
                                        ) }}"
                                        class="action-button edit-button"
                                        title="تعديل"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.advertisements.destroy',
                                            $advertisement
                                        ) }}"
                                        onsubmit="return confirm(
                                            'هل أنت متأكد من حذف هذا الإعلان؟'
                                        )"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-button delete-button"
                                            title="حذف"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fa-regular fa-rectangle-ad"></i>
                                    <strong>لا توجد إعلانات</strong>
                                    <span>
                                        لم تتم إضافة أي إعلانات حتى الآن.
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ads->hasPages())
            <div style="padding:18px">
                {{ $ads->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .advertisement-thumbnail,
    .media-thumbnail {
        width: 62px;
        height: 48px;
        border-radius: 9px;
    }

    .advertisement-thumbnail {
        display: block;
        object-fit: cover;
        background: #000;
    }

    .media-thumbnail {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c89a2b;
        border: 1px solid rgba(200, 154, 43, .2);
        background: rgba(200, 154, 43, .08);
        font-size: 18px;
    }

    .status-label {
        padding: 5px 10px;
        display: inline-flex;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-active {
        color: #22c55e;
        background: rgba(34, 197, 94, .1);
    }

    .status-disabled {
        color: #94a3b8;
        background: rgba(148, 163, 184, .1);
    }

    .status-expired {
        color: #ef4444;
        background: rgba(239, 68, 68, .1);
    }

    .table-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .table-actions form {
        margin: 0;
    }

    .action-button {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 8px;
        background: rgba(255,255,255,.03);
        cursor: pointer;
        text-decoration: none;
    }

    .view-button {
        color: #60a5fa;
    }

    .edit-button {
        color: #c89a2b;
    }

    .delete-button {
        color: #ef4444;
    }

    .action-button:hover {
        border-color: currentColor;
        background: rgba(255,255,255,.07);
    }

    .empty-state {
        padding: 55px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 9px;
        color: rgba(255,255,255,.42);
        text-align: center;
    }

    .empty-state i {
        color: #c89a2b;
        font-size: 35px;
    }

    .empty-state strong {
        color: rgba(255,255,255,.75);
    }

    .empty-state span {
        font-size: 12px;
    }
</style>
@endsection