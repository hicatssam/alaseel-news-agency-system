@extends('layouts.app')

@section('title', $category->name)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="main-content">
    <div class="container">

        {{-- عنوان التصنيف --}}
        <div class="category-page-header">
            <div class="category-breadcrumb">
                <a href="{{ route('home') }}">
                    {{ __('messages.nav_home') }}
                </a>

                <span>/</span>

                <span>{{ __('messages.categories_breadcrumb') }}</span>

                @if ($category->parent)
                    <span>/</span>

                    <a href="{{ route('categories.show', $category->parent->slug) }}">
                        {{ $category->parent->name }}
                    </a>
                @endif
            </div>

            <h1>{{ $category->name }}</h1>

            @if ($category->description)
                <p>{{ $category->description }}</p>
            @endif

            <div class="category-article-count">
                {{ __('messages.article_count', ['count' => $articles->total()]) }}
            </div>
        </div>

        <div class="category-page-layout">

            {{-- المقالات --}}
            <div>
                <div class="articles-grid">
                    @forelse ($articles as $article)
                        <div class="article-card">
                            <div class="article-card-img">
                                @if ($article->main_image)
                                    <img
                                        src="{{ $article->main_image }}"
                                        alt="{{ $article->title }}"
                                        loading="lazy"
                                        onerror="this.style.display='none'"
                                    >
                                @else
                                    <div class="article-image-placeholder">
                                        <i class="fa-solid fa-newspaper"></i>
                                    </div>
                                @endif

                                <div class="badge-overlay">
                                    @if ($article->is_breaking)
                                        <span class="badge-breaking">
                                            {{ __('messages.badge_breaking') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="article-card-body">
                                <div class="article-title">
                                    <a href="{{ route('articles.show', $article->slug) }}">
                                        {{ Str::limit($article->title, 80) }}
                                    </a>
                                </div>

                                <div class="article-meta">
                                    @if ($article->journalist)
                                        <span>
                                            <i class="fa-solid fa-user-pen"></i>
                                            {{ $article->journalist->name }}
                                        </span>
                                    @endif

                                    <span>
                                        <i class="fa-solid fa-clock"></i>
                                        {{ $article->published_at?->diffForHumans() }}
                                    </span>

                                    <span>
                                        <i class="fa-solid fa-eye"></i>
                                        {{ number_format($article->views) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-category">
                            <i class="fa-solid fa-newspaper"></i>

                            <span>
                                {{ __('messages.no_articles_category') }}
                            </span>
                        </div>
                    @endforelse
                </div>

                @if ($articles->hasPages())
                    <div class="category-pagination">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>

            {{-- قائمة التصنيفات --}}
            <aside>
                <div class="sidebar-widget categories-widget">
                    <div class="widget-header">
                        <span class="widget-title">
                            <i class="fa-solid fa-folder"></i>
                            {{ __('messages.all_categories') }}
                        </span>
                    </div>

                    <div class="widget-body categories-widget-body">
                        @forelse ($categories as $cat)
                            @php
                                $hasChildren = $cat->children->isNotEmpty();

                                $isParentActive =
                                    $category->id === $cat->id ||
                                    $category->parent_id === $cat->id;

                                $parentTotalCount =
                                    $cat->published_articles_count +
                                    $cat->children->sum('published_articles_count');
                            @endphp

                            @if ($hasChildren)
                                <details
                                    class="category-dropdown {{ $isParentActive ? 'is-active' : '' }}"
                                    @if ($isParentActive) open @endif
                                >
                                    <summary class="category-parent-row">
                                        <span class="category-parent-content">
                                            <span class="category-toggle-icon">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </span>

                                            <a
                                                href="{{ route('categories.show', $cat->slug) }}"
                                                class="category-parent-link"
                                                onclick="event.stopPropagation()"
                                            >
                                                {{ $cat->name }}
                                            </a>
                                        </span>

                                        <span class="category-count">
                                            {{ number_format($parentTotalCount) }}
                                        </span>
                                    </summary>

                                    <div class="category-children">
                                        @foreach ($cat->children as $child)
                                            <a
                                                href="{{ route('categories.show', $child->slug) }}"
                                                class="category-child-row
                                                    {{ $category->id === $child->id ? 'is-current' : '' }}"
                                            >
                                                <span class="category-child-name">
                                                    <i class="fa-solid fa-angle-{{ $isRtl ? 'left' : 'right' }}"></i>
                                                    {{ $child->name }}
                                                </span>

                                                <span class="category-count category-child-count">
                                                    {{ number_format($child->published_articles_count) }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <a
                                    href="{{ route('categories.show', $cat->slug) }}"
                                    class="category-single-row
                                        {{ $category->id === $cat->id ? 'is-current' : '' }}"
                                >
                                    <span class="category-single-name">
                                        <i class="fa-regular fa-folder"></i>
                                        {{ $cat->name }}
                                    </span>

                                    <span class="category-count">
                                        {{ number_format($cat->published_articles_count) }}
                                    </span>
                                </a>
                            @endif
                        @empty
                            <div class="categories-empty">
                                لا توجد تصنيفات متاحة.
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<style>
    .category-page-header {
        padding: 28px;
        margin-bottom: 28px;
        color: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        background:
            linear-gradient(
                135deg,
                var(--surface),
                var(--surface2)
            );
    }

    .category-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 12px;
    }

    .category-breadcrumb a {
        color: rgba(255, 255, 255, 0.4);
        transition: color 0.2s ease;
    }

    .category-breadcrumb a:hover {
        color: var(--gold);
    }

    .category-page-header h1 {
        margin: 0;
        color: var(--white);
        font-size: 26px;
        font-weight: 900;
    }

    .category-page-header p {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.8;
    }

    .category-article-count {
        margin-top: 12px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 13px;
    }

    .category-page-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 28px;
        align-items: start;
    }

    .article-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: var(--surface2);
    }

    .article-image-placeholder i {
        color: rgba(200, 154, 43, 0.15);
        font-size: 36px;
    }

    .empty-category {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 60px 20px;
        color: rgba(255, 255, 255, 0.25);
        text-align: center;
    }

    .empty-category i {
        font-size: 48px;
        opacity: 0.3;
    }

    .category-pagination {
        margin-top: 24px;
    }

    /*
    |--------------------------------------------------------------------------
    | قائمة التصنيفات
    |--------------------------------------------------------------------------
    */

    .categories-widget {
        overflow: visible;
    }

    .categories-widget-body {
        padding: 12px;
    }

    .category-dropdown {
        position: relative;
        margin-bottom: 4px;
        border-radius: 8px;
        transition:
            background-color 0.2s ease,
            border-color 0.2s ease;
    }

    .category-dropdown:last-child {
        margin-bottom: 0;
    }

    .category-dropdown summary {
        list-style: none;
    }

    .category-dropdown summary::-webkit-details-marker {
        display: none;
    }

    .category-parent-row,
    .category-single-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 42px;
        padding: 8px 10px;
        color: rgba(255, 255, 255, 0.68);
        border: 1px solid transparent;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition:
            color 0.2s ease,
            background-color 0.2s ease,
            border-color 0.2s ease;
    }

    .category-parent-row:hover,
    .category-single-row:hover,
    .category-dropdown.is-active > .category-parent-row,
    .category-single-row.is-current {
        color: var(--gold);
        border-color: rgba(200, 154, 43, 0.18);
        background: rgba(200, 154, 43, 0.08);
    }

    .category-parent-content,
    .category-single-name,
    .category-child-name {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .category-parent-link {
        overflow: hidden;
        color: inherit;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-decoration: none;
    }

    .category-parent-link:hover {
        color: var(--gold);
    }

    .category-toggle-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        color: rgba(255, 255, 255, 0.35);
        font-size: 10px;
        transition:
            color 0.2s ease,
            transform 0.25s ease;
    }

    .category-dropdown[open] .category-toggle-icon {
        color: var(--gold);
        transform: rotate(180deg);
    }

    .category-count {
        flex: 0 0 auto;
        min-width: 27px;
        padding: 2px 8px;
        color: var(--gold);
        border: 1px solid rgba(200, 154, 43, 0.2);
        border-radius: 10px;
        background: rgba(200, 154, 43, 0.12);
        font-size: 11px;
        text-align: center;
    }

    .category-children {
        display: grid;
        grid-template-rows: 0fr;
        overflow: hidden;
        opacity: 0;
        transition:
            grid-template-rows 0.25s ease,
            opacity 0.2s ease;
    }

    .category-children::before {
        content: "";
        min-height: 0;
    }

    .category-dropdown[open] .category-children {
        grid-template-rows: 1fr;
        padding: 4px 8px 8px;
        opacity: 1;
    }

    .category-child-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 3px;
        padding: 8px 10px;
        color: rgba(255, 255, 255, 0.5);
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition:
            color 0.2s ease,
            background-color 0.2s ease,
            transform 0.2s ease;
    }

    html[dir="rtl"] .category-child-row {
        padding-right: 24px;
    }

    html[dir="ltr"] .category-child-row {
        padding-left: 24px;
    }

    .category-child-row:hover,
    .category-child-row.is-current {
        color: var(--gold);
        background: rgba(255, 255, 255, 0.04);
    }

    html[dir="rtl"] .category-child-row:hover {
        transform: translateX(-3px);
    }

    html[dir="ltr"] .category-child-row:hover {
        transform: translateX(3px);
    }

    .category-child-name i {
        color: rgba(200, 154, 43, 0.55);
        font-size: 9px;
    }

    .category-child-count {
        padding: 1px 7px;
        font-size: 10px;
    }

    .categories-empty {
        padding: 25px 10px;
        color: rgba(255, 255, 255, 0.3);
        font-size: 13px;
        text-align: center;
    }

    /*
    |--------------------------------------------------------------------------
    | فتح القائمة بالـ Hover على الكمبيوتر
    |--------------------------------------------------------------------------
    */

    @media (hover: hover) and (pointer: fine) {
        .category-dropdown:hover > .category-children {
            grid-template-rows: 1fr;
            padding: 4px 8px 8px;
            opacity: 1;
        }

        .category-dropdown:hover .category-toggle-icon {
            color: var(--gold);
            transform: rotate(180deg);
        }

        .category-dropdown:hover > .category-parent-row {
            color: var(--gold);
            border-color: rgba(200, 154, 43, 0.18);
            background: rgba(200, 154, 43, 0.08);
        }
    }

    @media (max-width: 992px) {
        .category-page-layout {
            grid-template-columns: minmax(0, 1fr) 260px;
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .category-page-layout {
            grid-template-columns: 1fr;
        }

        .category-page-layout aside {
            grid-row: 1;
        }

        .category-page-header {
            padding: 20px;
        }

        .category-page-header h1 {
            font-size: 22px;
        }
    }
</style>
@endsection