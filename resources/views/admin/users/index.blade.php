@extends('layouts.admin')

@section('title', __('admin.users_index'))

@section('breadcrumb')
    {{ __('admin.nav_users') }}
@endsection

@section('content')

<div class="users-page-header">
    <div></div>

    <a
        href="{{ route('admin.users.create') }}"
        class="btn btn-primary"
    >
        <i class="fa-solid fa-plus"></i>
        {{ __('admin.users_create') }}
    </a>
</div>

<div class="filter-bar">
    <form method="GET" class="users-search-form">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="{{ __('admin.placeholder_search_user') }}"
            value="{{ request('search') }}"
        >

        <button
            type="submit"
            class="btn btn-secondary"
            title="{{ __('admin.btn_search') }}"
        >
            <i class="fa-solid fa-search"></i>
        </button>

        @if(request()->filled('search'))
            <a
                href="{{ route('admin.users.index') }}"
                class="btn btn-secondary"
                title="{{ __('admin.btn_reset') }}"
            >
                <i class="fa-solid fa-xmark"></i>
            </a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            {{ __('admin.users_index') }}
            ({{ $users->total() }})
        </span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('admin.col_user') }}</th>
                    <th>{{ __('admin.col_email') }}</th>
                    <th>{{ __('admin.col_permissions') }}</th>
                    <th>{{ __('admin.col_status') }}</th>
                    <th>{{ __('admin.col_last_login') }}</th>
                    <th>{{ __('admin.col_actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $u)
                    @php
                        $userPhoto = null;

                        if (!empty($u->photo)) {
                            $photoPath = ltrim($u->photo, '/');

                            if (
                                \Illuminate\Support\Str::startsWith(
                                    $photoPath,
                                    ['http://', 'https://', '//']
                                )
                            ) {
                                $userPhoto = $u->photo;
                            } else {
                                $photoPath = preg_replace(
                                    '#^(public/)?storage/#',
                                    '',
                                    $photoPath
                                );

                                $userPhoto = asset(
                                    'storage/' . $photoPath
                                );
                            }
                        }

                        $firstLetter = mb_strtoupper(
                            mb_substr(trim($u->name), 0, 1)
                        );
                    @endphp

                    <tr>
                        <td>
                            <div class="user-information">
                                <div class="user-avatar">
                                    @if($userPhoto)
                                        <img
                                            src="{{ $userPhoto }}"
                                            alt="{{ $u->name }}"
                                            loading="lazy"
                                            onerror="
                                                this.style.display='none';
                                                this.nextElementSibling.style.display='flex';
                                            "
                                        >

                                        <span
                                            class="user-avatar-fallback"
                                            style="display:none"
                                        >
                                            {{ $firstLetter }}
                                        </span>
                                    @else
                                        <span class="user-avatar-fallback">
                                            {{ $firstLetter }}
                                        </span>
                                    @endif
                                </div>

                                <div class="user-details">
                                    <div class="user-name">
                                        {{ $u->name }}
                                    </div>

                                    @if($u->phone)
                                        <div class="user-phone">
                                            <i class="fa-solid fa-phone"></i>
                                            {{ $u->phone }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="user-email">
                            {{ $u->email }}
                        </td>

                        <td>
                            <div class="user-roles">
                                @forelse($u->roles as $role)
                                    <span class="badge badge-gold">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="empty-value">—</span>
                                @endforelse
                            </div>
                        </td>

                       <td>
    @if(auth()->id() === $u->id)
        <span class="badge badge-success">
            نشط
        </span>
    @else
        <span class="badge badge-danger">
            غير نشط
        </span>
    @endif
</td>

                        <td class="last-login">
                            {{ $u->last_login_at?->format('Y/m/d H:i') ?? '—' }}
                        </td>

                        <td>
                            <div class="user-actions">
                                {{-- زر عرض المستخدم --}}
                                <a
                                    href="{{ route('admin.users.show', $u) }}"
                                    class="btn btn-secondary btn-sm btn-icon"
                                    title="{{ __('admin.btn_view') }}"
                                    aria-label="{{ __('admin.btn_view') }}"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- زر تعديل المستخدم --}}
                                <a
                                    href="{{ route('admin.users.edit', $u) }}"
                                    class="btn btn-primary btn-sm btn-icon"
                                    title="{{ __('admin.btn_edit') }}"
                                    aria-label="{{ __('admin.btn_edit') }}"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                {{-- زر حذف المستخدم --}}
                                @if($u->id !== auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $u) }}"
                                        onsubmit="return confirm(
                                            @js(__('admin.confirm_delete_user'))
                                        )"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm btn-icon"
                                            title="{{ __('admin.btn_delete') }}"
                                            aria-label="{{ __('admin.btn_delete') }}"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-users"></i>

                                <p>
                                    {{ __('admin.empty_users') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="users-pagination">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>

<style>
    .users-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .users-search-form {
        display: flex;
        flex: 1;
        flex-wrap: wrap;
        gap: 10px;
    }

    .users-search-form .form-control {
        width: 100%;
        max-width: 320px;
    }

    .user-information {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 180px;
    }

    .user-avatar {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid rgba(201, 168, 76, .35);
        border-radius: 50%;
        background: rgba(201, 168, 76, .15);
        box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
    }

    .user-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .user-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c9a84c;
        font-size: 16px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .user-details {
        min-width: 0;
    }

    .user-name {
        overflow: hidden;
        color: #1a1a2e;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .user-phone {
        margin-top: 3px;
        color: #999;
        font-size: 11px;
    }

    .user-phone i {
        margin-inline-end: 3px;
    }

    .user-email {
        color: #666;
        font-size: 13px;
    }

    .user-roles {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .empty-value {
        color: #999;
        font-size: 12px;
    }

    .last-login {
        color: #888;
        font-size: 12px;
        white-space: nowrap;
    }

    .user-actions {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .user-actions form {
        margin: 0;
    }

    .users-pagination {
        padding: 16px;
    }
</style>

@endsection