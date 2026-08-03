<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Video;
use App\Models\Journalist;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q','');

        $articles = Article::published()
            ->where(fn($query) => $query->where('title','like',"%{$q}%")->orWhere('content','like',"%{$q}%"))
            ->with('category','journalist')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $videos      = Video::published()->where('title','like',"%{$q}%")->limit(6)->get();
        $journalists = Journalist::active()->where('name','like',"%{$q}%")->limit(6)->get();
        $categories  = Category::active()->where('name','like',"%{$q}%")->limit(6)->get();
        $total       = $articles->total() + $videos->count() + $journalists->count() + $categories->count();

        return view('search.index', compact('articles','videos','journalists','categories','total','q'));
    }
}
