@extends('layouts.admin')

@section('title', __('admin.journalists_index'))

@section('breadcrumb')
    {{ __('admin.nav_journalists') }}
@endsection

@section('content')

<style>
    .journalists-toolbar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .journalists-filter-form {
        display: flex;
        flex: 1;
        flex-wrap: wrap;
        gap: 10px;
    }

    .journalist-list-avatar {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid rgba(201, 168, 76, .25);
        border-radius: 50%;
        background: rgba(201, 168, 76, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c9a84c;
        font-size: 16px;
        font-weight: 800;
    }

    .journalist-list-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .journalist-list-letter {
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
    }

    .journalist-list-name {
        color: #1a1a2e;
        font-weight: 700;
        text-decoration: none;
    }

    .journalist-list-name:hover {
        color: #c9a84c;
    }

    @media (max-width: 650px) {
        .journalists-toolbar {
            align-items: stretch;
            flex-direction: column;
            gap: 10px;
        }

        .journalists-filter-form .form-control {
            width: 100%;
            max-width: none !important;
        }
    }
</style>

<div class="journalists-toolbar">
    <div></div>

    <a
        href="{{ route('admin.journalists.create') }}"
        class="btn btn-primary"
    >
        <i class="fa-solid fa-plus"></i>
        {{ __('admin.journalists_create') }}
    </a>
</div>

<div class="filter-bar">
    <form method="GET" class="journalists-filter-form">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="{{ __('admin.placeholder_search_name') }}"
            value="{{ request('search') }}"
            style="max-width:280px"
        >

        <select
            name="status"
            class="form-control"
            style="max-width:150px"
        >
            <option value="">
                {{ __('admin.filter_all') }}
            </option>

            <option
                value="1"
                @selected(request('status') === '1')
            >
                {{ __('admin.status_active') }}
            </option>

            <option
                value="0"
                @selected(request('status') === '0')
            >
                {{ __('admin.status_disabled') }}
            </option>
        </select>

        <button type="submit" class="btn btn-secondary">
            <i class="fa-solid fa-search"></i>
        </button>

        @if(request()->filled('search') || request()->filled('status'))
            <a
                href="{{ route('admin.journalists.index') }}"
                class="btn btn-outline"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            {{ __('admin.journalists_index') }}
            ({{ number_format($journalists->total()) }})
        </span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('admin.col_journalist') }}</th>
                    <th>{{ __('admin.col_position') }}</th>
                    <th>{{ __('admin.col_email') }}</th>
                    <th>{{ __('admin.col_articles_count') }}</th>
                    <th>{{ __('admin.col_status') }}</th>
                    <th>{{ __('admin.col_actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($journalists as $journalist)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:11px">

                                <div class="journalist-list-avatar">
                                    @if($journalist->photo_url)
                                        <img
                                            src="{{ $journalist->photo_url }}"
                                            alt="{{ $journalist->name }}"
                                            loading="lazy"
                                            onerror="
                                                this.style.display='none';
                                                this.nextElementSibling.style.display='flex';
                                            "
                                        >

                                        <span
                                            class="journalist-list-letter"
                                            style="display:none"
                                        >
                                            {{ mb_substr($journalist->name, 0, 1) }}
                                        </span>
                                    @else
                                        <span
                                            class="journalist-list-letter"
                                            style="display:flex"
                                        >
                                            {{ mb_substr($journalist->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <a
                                        href="{{ route('admin.journalists.show', $journalist) }}"
                                        class="journalist-list-name"
                                    >
                                        {{ $journalist->name }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <td style="font-size:13px;color:#666">
                            {{ $journalist->job_title ?: '—' }}
                        </td>

                        <td style="font-size:12px;color:#888">
                            {{ $journalist->email ?: '—' }}
                        </td>

                        <td>
                            <span class="badge badge-gold">
                                {{ number_format($journalist->articles_count ?? 0) }}
                            </span>
                        </td>

                        <td>
                            @if($journalist->status)
                                <span class="badge badge-success">
                                    {{ __('admin.status_active') }}
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    {{ __('admin.status_disabled') }}
                                </span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;gap:4px">
                                <a
                                    href="{{ route('admin.journalists.show', $journalist) }}"
                                    class="btn btn-outline btn-sm btn-icon"
                                    title="عرض"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a
                                    href="{{ route('admin.journalists.edit', $journalist) }}"
                                    class="btn btn-primary btn-sm btn-icon"
                                    title="تعديل"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.journalists.destroy', $journalist) }}"
                                    onsubmit="return confirm(@js(__('admin.confirm_delete_journalist')))"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm btn-icon"
                                        title="حذف"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-user-tie"></i>
                                <p>{{ __('admin.empty_journalists') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($journalists->hasPages())
        <div style="padding:16px">
            {{ $journalists->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection