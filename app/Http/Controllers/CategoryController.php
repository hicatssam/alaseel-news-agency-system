<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Article;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        $query    = Article::published()->where('category_id', $category->id)
            ->with('journalist','category')->latest('published_at');
        $articles   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount(['articles'=>fn($q)=>$q->published()])->get();
        return view('categories.show', compact('category','articles','categories'));
    }
}
