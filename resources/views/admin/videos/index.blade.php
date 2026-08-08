@extends('layouts.admin')

@section('title', __('admin.videos_index'))

@section('breadcrumb')
    {{ __('admin.nav_videos') }}
@endsection

@section('content')
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:20px;
    ">
        <div></div>

        <a
            href="{{ route('admin.videos.create') }}"
            class="btn btn-primary"
        >
            <i class="fa-solid fa-plus"></i>
            {{ __('admin.videos_create') }}
        </a>
    </div>

    <div class="filter-bar">
        <form
            method="GET"
            action="{{ route('admin.videos.index') }}"
            style="
                display:flex;
                gap:10px;
                flex:1;
                flex-wrap:wrap;
            "
        >
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="{{ __('admin.placeholder_search') }}"
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
                    value="draft"
                    @selected(request('status') === 'draft')
                >
                    {{ __('admin.status_draft') }}
                </option>

                <option
                    value="published"
                    @selected(request('status') === 'published')
                >
                    {{ __('admin.status_published') }}
                </option>
            </select>

            <select
                name="category_id"
                class="form-control"
                style="max-width:180px"
            >
                <option value="">
                    {{ __('admin.filter_all_categories') }}
                </option>

                @foreach($categories as $cat)
                    <option
                        value="{{ $cat->id }}"
                        @selected(
                            (string) request('category_id') ===
                            (string) $cat->id
                        )
                    >
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button
                type="submit"
                class="btn btn-secondary"
                title="{{ __('admin.placeholder_search') }}"
            >
                <i class="fa-solid fa-search"></i>
            </button>

            @if(request()->hasAny(['search', 'status', 'category_id']))
                <a
                    href="{{ route('admin.videos.index') }}"
                    class="btn btn-outline"
                    title="إزالة الفلاتر"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">
                {{ __('admin.videos_index') }}
                ({{ $videos->total() }})
            </span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('admin.col_title') }}</th>
                        <th>{{ __('admin.col_category') }}</th>
                        <th>{{ __('admin.col_status') }}</th>
                        <th>{{ __('admin.col_featured') }}</th>
                        <th>{{ __('admin.col_views') }}</th>
                        <th>{{ __('admin.col_actions') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($videos as $video)
                        <tr>
                            <td style="font-weight:600">
                                {{ \Illuminate\Support\Str::limit(
                                    $video->title,
                                    60
                                ) }}
                            </td>

                            <td>
                                <span class="badge badge-secondary">
                                    {{ $video->category?->name ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if($video->status === 'published')
                                    <span class="badge badge-success">
                                        {{ __('admin.status_published') }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        {{ __('admin.status_draft') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($video->is_featured)
                                    <span
                                        class="badge badge-gold"
                                        title="{{ __('admin.col_featured') }}"
                                    >
                                        ⭐
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td style="
                                font-weight:700;
                                color:#c9a84c;
                            ">
                                {{ number_format($video->views ?? 0) }}
                            </td>

                            <td>
                                <div style="
                                    display:flex;
                                    gap:4px;
                                    align-items:center;
                                ">
                                    @if(
                                        $video->status === 'published' &&
                                        filled($video->slug)
                                    )
                                        <a
                                            href="{{ route(
                                                'videos.show',
                                                $video->slug
                                            ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="
                                                btn
                                                btn-secondary
                                                btn-sm
                                                btn-icon
                                            "
                                            title="مشاهدة الفيديو"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif

                                    <a
                                        href="{{ route(
                                            'admin.videos.edit',
                                            $video
                                        ) }}"
                                        class="
                                            btn
                                            btn-primary
                                            btn-sm
                                            btn-icon
                                        "
                                        title="تعديل"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.videos.destroy',
                                            $video
                                        ) }}"
                                        onsubmit="return confirm(
                                            @js(
                                                __('admin.confirm_delete_video')
                                            )
                                        )"
                                        style="margin:0"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="
                                                btn
                                                btn-danger
                                                btn-sm
                                                btn-icon
                                            "
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
                                    <i class="fa-solid fa-video"></i>

                                    <p>
                                        {{ __('admin.empty_videos') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($videos->hasPages())
            <div style="padding:16px">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
@endsection