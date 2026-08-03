<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Category;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query    = Video::published()->with('category')->latest('published_at');
        if ($request->filled('category_id')) $query->where('category_id',$request->category_id);
        $videos     = $query->paginate(12)->withQueryString();
        $categories = Category::active()->get();
        $featured   = Video::published()->featured()->latest()->limit(3)->get();
        return view('videos.index', compact('videos','categories','featured'));
    }

    public function show(string $slug)
    {
        $video   = Video::published()->where('slug',$slug)->with('category')->firstOrFail();
        $video->increment('views');
        $related = Video::published()->where('id','!=',$video->id)->with('category')->latest()->limit(6)->get();
        return view('videos.show', compact('video','related'));
    }
}
