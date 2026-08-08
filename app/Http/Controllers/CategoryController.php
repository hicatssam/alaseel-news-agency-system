<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug, Request $request): View
    {
        $category = Category::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $articles = Article::query()
            ->published()
            ->where('category_id', $category->id)
            ->with([
                'journalist',
                'category',
            ])
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | التصنيفات الرئيسية مع التصنيفات الفرعية
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->active()
            ->root()
            ->withCount([
                'articles as published_articles_count' => function ($query) {
                    $query->published();
                },
            ])
            ->with([
                'children' => function ($query) {
                    $query
                        ->active()
                        ->withCount([
                            'articles as published_articles_count' => function ($articlesQuery) {
                                $articlesQuery->published();
                            },
                        ])
                        ->orderBy('sort_order')
                        ->orderBy('name');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('categories.show', compact(
            'category',
            'articles',
            'categories'
        ));
    }
}