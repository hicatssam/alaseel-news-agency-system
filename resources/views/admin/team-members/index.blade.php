@extends('layouts.admin')

@section('title', 'أعضاء الفريق')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:20px;flex-wrap:wrap">
    <div>
        <h2 style="margin:0;color:#1a1a2e;font-size:22px;font-weight:800">
            <i class="fa-solid fa-users" style="color:#c9a84c"></i>
            أعضاء الفريق
        </h2>

        <p style="margin:5px 0 0;color:#888;font-size:13px">
            إدارة أعضاء الفريق الظاهرين في صفحة من نحن.
        </p>
    </div>

    <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        إضافة عضو جديد
    </a>
</div>

@if(session('success'))
    <div style="padding:14px 18px;margin-bottom:20px;border:1px solid #b7e4c7;border-radius:8px;background:#eafaf1;color:#19713b;font-size:13px;font-weight:700">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="padding:14px 18px;margin-bottom:20px;border:1px solid #f3c2c2;border-radius:8px;background:#fdf0f0;color:#b83232;font-size:13px;font-weight:700">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <span class="card-title">
            <i class="fa-solid fa-filter" style="color:#c9a84c"></i>
            البحث والتصفية
        </span>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.team-members.index') }}" class="team-filter-form">
            <div class="form-group" style="margin:0;flex:1;min-width:220px">
                <label for="search">البحث</label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="ابحث بالاسم أو المسمى الوظيفي"
                >
            </div>

            <div class="form-group" style="margin:0;width:190px;max-width:100%">
                <label for="status">الحالة</label>

                <select id="status" name="status" class="form-control">
                    <option value="">كل الحالات</option>
                    <option value="1" @selected(request('status') === '1')>نشط</option>
                    <option value="0" @selected(request('status') === '0')>غير نشط</option>
                </select>
            </div>

            <div style="display:flex;gap:8px;align-self:flex-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    بحث
                </button>

                <a href="{{ route('admin.team-members.index') }}" class="btn btn-outline">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fa-solid fa-address-card" style="color:#c9a84c"></i>
            قائمة أعضاء الفريق
        </span>

        <span class="badge badge-gold">
            {{ method_exists($teamMembers, 'total') ? $teamMembers->total() : $teamMembers->count() }} عضو
        </span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:65px">#</th>
                    <th>العضو</th>
                    <th>المسمى الوظيفي</th>
                    <th style="width:110px">الترتيب</th>
                    <th style="width:110px">الحالة</th>
                    <th style="width:150px">الإجراءات</th>
                </tr>
            </thead>

            <tbody>
                @forelse($teamMembers as $index => $member)
                    @php
                        $memberImageUrl = null;

                        if (method_exists($member, 'getFirstMediaUrl')) {
                            foreach (['team-member-image', 'team_member_image', 'photo', 'image'] as $collection) {
                                try {
                                    $mediaUrl = $member->getFirstMediaUrl($collection);

                                    if (!empty($mediaUrl)) {
                                        $memberImageUrl = $mediaUrl;
                                        break;
                                    }
                                } catch (\Throwable $exception) {
                                    // Continue to the image field.
                                }
                            }
                        }

                        $storedMemberImage = data_get($member, 'image');

                        if (!$memberImageUrl && filled($storedMemberImage)) {
                            if (\Illuminate\Support\Str::startsWith($storedMemberImage, ['http://', 'https://', '//'])) {
                                $memberImageUrl = $storedMemberImage;
                            } elseif (\Illuminate\Support\Str::startsWith($storedMemberImage, '/')) {
                                $memberImageUrl = url($storedMemberImage);
                            } elseif (\Illuminate\Support\Str::startsWith($storedMemberImage, 'storage/')) {
                                $memberImageUrl = asset($storedMemberImage);
                            } else {
                                $memberImageUrl = \Illuminate\Support\Facades\Storage::disk('public')
                                    ->url(ltrim($storedMemberImage, '/'));
                            }
                        }

                        $firstItem = method_exists($teamMembers, 'firstItem')
                            ? ($teamMembers->firstItem() ?? 1)
                            : 1;

                        $rowNumber = $firstItem + $index;
                    @endphp

                    <tr>
                        <td>
                            <span style="font-weight:800;color:#c9a84c">
                                {{ $rowNumber }}
                            </span>
                        </td>

                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                @if($memberImageUrl)
                                    <img
                                        src="{{ $memberImageUrl }}"
                                        alt="{{ $member->name }}"
                                        class="member-table-photo"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="member-table-photo member-photo-placeholder">
                                        {{ mb_substr($member->name ?: 'ع', 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <div style="font-size:13px;font-weight:800;color:#1a1a2e">
                                        {{ $member->name }}
                                    </div>

                                    <div style="font-size:10.5px;color:#aaa;margin-top:2px">
                                        أضيف {{ optional($member->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span style="font-size:12.5px;color:#555">
                                {{ $member->job_title ?: '—' }}
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-info">
                                {{ $member->display_order ?? 0 }}
                            </span>
                        </td>

                        <td>
                            @if($member->is_active)
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle" style="font-size:6px"></i>
                                    نشط
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    غير نشط
                                </span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;gap:6px;align-items:center">
                                <a
                                    href="{{ route('admin.team-members.edit', $member) }}"
                                    class="btn btn-outline btn-sm"
                                    title="تعديل"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form
                                    action="{{ route('admin.team-members.destroy', $member) }}"
                                    method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من حذف عضو الفريق؟');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm team-delete-btn" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:50px 20px">
                                <i class="fa-solid fa-users" style="font-size:40px;color:#ddd;margin-bottom:12px"></i>
                                <p style="margin:0 0 15px">لا يوجد أعضاء فريق حتى الآن.</p>

                                <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i>
                                    إضافة أول عضو
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($teamMembers, 'hasPages') && $teamMembers->hasPages())
        <div class="card-body" style="border-top:1px solid #eee">
            {{ $teamMembers->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .team-filter-form {
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
    }

    .member-table-photo {
        display:block;
        width:48px;
        height:48px;
        flex-shrink:0;
        border:2px solid #fff;
        border-radius:50%;
        object-fit:cover;
        box-shadow:0 2px 10px rgba(0,0,0,.13);
    }

    .member-photo-placeholder {
        display:flex;
        align-items:center;
        justify-content:center;
        color:#fff;
        background:linear-gradient(135deg,#c9a84c,#96752c);
        font-size:17px;
        font-weight:800;
    }

    .team-delete-btn {
        border:1px solid #f2c4c4;
        color:#e74c3c;
        background:#fff;
    }

    .team-delete-btn:hover {
        border-color:#e74c3c;
        color:#fff;
        background:#e74c3c;
    }

    @media(max-width:700px) {
        .team-filter-form > * {
            width:100% !important;
        }
    }
</style>
@endpush