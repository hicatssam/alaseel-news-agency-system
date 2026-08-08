@extends('layouts.admin')

@section('title', __('admin.label_journalist_profile'))

@section('breadcrumb')
    <a href="{{ route('admin.journalists.index') }}">
        {{ __('admin.nav_journalists') }}
    </a>

    <span class="sep">›</span>

    {{ $journalist->name }}
@endsection

@section('content')

<style>
    .journalist-profile-layout {
        display: grid;
        grid-template-columns: 300px minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .journalist-avatar {
        width: 120px;
        height: 120px;
        margin: 0 auto 15px;
        border: 4px solid rgba(201, 168, 76, .18);
        border-radius: 50%;
        overflow: hidden;
        background: rgba(201, 168, 76, .15);
        box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .journalist-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .journalist-avatar-letter {
        font-size: 40px;
        font-weight: 900;
        color: #c9a84c;
    }

    .journalist-name {
        margin-bottom: 5px;
        color: #1a1a2e;
        font-size: 20px;
        font-weight: 900;
        line-height: 1.5;
    }

    .journalist-job-title {
        margin-bottom: 15px;
        color: #888;
        font-size: 13px;
    }

    .journalist-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .journalist-status.active {
        color: #16804c;
        background: rgba(39, 174, 96, .12);
    }

    .journalist-status.inactive {
        color: #a94442;
        background: rgba(231, 76, 60, .12);
    }

    .journalist-bio {
        margin: 0;
        color: #555;
        font-size: 13px;
        line-height: 1.9;
        text-align: start;
        white-space: pre-line;
    }

    .journalist-contact-list {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(128, 128, 128, .15);
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .journalist-contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #555;
        font-size: 13px;
        text-align: start;
        overflow-wrap: anywhere;
    }

    .journalist-contact-item i {
        width: 18px;
        color: #c9a84c;
        text-align: center;
    }

    .journalist-socials {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .journalist-social-link {
        width: 38px;
        height: 38px;
        border: 1px solid rgba(128, 128, 128, .15);
        border-radius: 50%;
        background: rgba(128, 128, 128, .04);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .journalist-social-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
    }

    .journalist-actions {
        margin-top: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .journalist-table-title {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .journalist-article-link {
        color: #1a1a2e;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.6;
        text-decoration: none;
    }

    .journalist-article-link:hover {
        color: #c9a84c;
    }

    @media (max-width: 900px) {
        .journalist-profile-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .journalist-avatar {
            width: 105px;
            height: 105px;
        }

        .journalist-table-title {
            font-size: 14px;
        }

        .journalist-profile-layout {
            gap: 15px;
        }
    }
</style>

<div class="journalist-profile-layout">

    {{-- بيانات الصحفي --}}
    <div class="card">
        <div class="card-body" style="text-align:center">

            <div class="journalist-avatar">

                @if($journalist->photo_url)
                    <img
                        src="{{ $journalist->photo_url }}"
                        alt="{{ $journalist->name }}"
                        onerror="
                            this.style.display='none';
                            this.nextElementSibling.style.display='flex';
                        "
                    >

                    <span
                        class="journalist-avatar-letter"
                        style="display:none;width:100%;height:100%;align-items:center;justify-content:center"
                    >
                        {{ mb_substr($journalist->name, 0, 1) }}
                    </span>
                @else
                    <span class="journalist-avatar-letter">
                        {{ mb_substr($journalist->name, 0, 1) }}
                    </span>
                @endif

            </div>

            <div class="journalist-name">
                {{ $journalist->name }}
            </div>

            @if($journalist->job_title)
                <div class="journalist-job-title">
                    {{ $journalist->job_title }}
                </div>
            @endif

            @if($journalist->status)
                <div class="journalist-status active">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ __('admin.status_active') }}
                </div>
            @else
                <div class="journalist-status inactive">
                    <i class="fa-solid fa-circle-xmark"></i>
                    {{ __('admin.status_inactive') }}
                </div>
            @endif

            @if($journalist->bio)
                <p class="journalist-bio">
                    {{ $journalist->bio }}
                </p>
            @endif

            @if($journalist->email || $journalist->phone)
                <div class="journalist-contact-list">

                    @if($journalist->email)
                        <a
                            href="mailto:{{ $journalist->email }}"
                            class="journalist-contact-item"
                            style="text-decoration:none"
                        >
                            <i class="fa-solid fa-envelope"></i>
                            <span>{{ $journalist->email }}</span>
                        </a>
                    @endif

                    @if($journalist->phone)
                        <a
                            href="tel:{{ $journalist->phone }}"
                            class="journalist-contact-item"
                            style="text-decoration:none"
                        >
                            <i class="fa-solid fa-phone"></i>
                            <span>{{ $journalist->phone }}</span>
                        </a>
                    @endif

                </div>
            @endif

            @if(
                $journalist->facebook ||
                $journalist->x_twitter ||
                $journalist->instagram ||
                $journalist->youtube
            )
                <div class="journalist-socials">

                    @if($journalist->facebook)
                        <a
                            href="{{ $journalist->facebook }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="journalist-social-link"
                            style="color:#1877f2"
                            title="Facebook"
                        >
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    @endif

                    @if($journalist->x_twitter)
                        <a
                            href="{{ $journalist->x_twitter }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="journalist-social-link"
                            style="color:#000"
                            title="Twitter / X"
                        >
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                    @endif

                    @if($journalist->instagram)
                        <a
                            href="{{ $journalist->instagram }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="journalist-social-link"
                            style="color:#e1306c"
                            title="Instagram"
                        >
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    @endif

                    @if($journalist->youtube)
                        <a
                            href="{{ $journalist->youtube }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="journalist-social-link"
                            style="color:#ff0000"
                            title="YouTube"
                        >
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    @endif

                </div>
            @endif

            <div class="journalist-actions">
                <a
                    href="{{ route('admin.journalists.edit', $journalist) }}"
                    class="btn btn-primary"
                >
                    <i class="fa-solid fa-pen"></i>
                    {{ __('admin.btn_edit') }}
                </a>

                <a
                    href="{{ route('admin.journalists.index') }}"
                    class="btn btn-outline"
                >
                    <i class="fa-solid fa-arrow-right"></i>
                    {{ __('admin.btn_back') }}
                </a>
            </div>

        </div>
    </div>

    {{-- مقالات الصحفي --}}
    <div class="card">

        <div class="card-header">
            <span class="card-title journalist-table-title">
                <i class="fa-solid fa-newspaper" style="color:#c9a84c"></i>

                {{ __('admin.label_journalist_articles') }}

                <span>{{ $journalist->name }}</span>

                <span class="badge badge-secondary">
                    {{ number_format($articles->total()) }}
                </span>
            </span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('admin.col_title') }}</th>
                        <th>{{ __('admin.col_category') }}</th>
                        <th>{{ __('admin.col_status') }}</th>
                        <th>{{ __('admin.col_views') }}</th>
                        <th>{{ __('admin.col_date') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($articles as $article)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('admin.articles.edit', $article) }}"
                                    class="journalist-article-link"
                                >
                                    {{ \Illuminate\Support\Str::limit($article->title, 60) }}
                                </a>
                            </td>

                            <td>
                                <span class="badge badge-secondary">
                                    {{ $article->category?->name ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if($article->status === 'published')
                                    <span class="badge badge-success">
                                        {{ __('admin.status_published') }}
                                    </span>
                                @elseif($article->status === 'draft')
                                    <span class="badge badge-secondary">
                                        {{ __('admin.status_draft') }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        {{ $article->status ?: '—' }}
                                    </span>
                                @endif
                            </td>

                            <td style="font-weight:700;color:#c9a84c">
                                {{ number_format($article->views ?? 0) }}
                            </td>

                            <td style="font-size:12px;color:#888;white-space:nowrap">
                                {{ $article->created_at?->format('Y/m/d') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i
                                        class="fa-regular fa-newspaper"
                                        style="font-size:30px;color:#c9a84c;margin-bottom:10px"
                                    ></i>

                                    <p>{{ __('admin.empty_articles') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div style="padding:16px">
                {{ $articles->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection