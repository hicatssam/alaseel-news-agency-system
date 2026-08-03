<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Advertisement;
use App\Models\Notification;

class ArticleController extends Controller
{
    public function show(string $slug)
    {
        $article = Article::with('category','journalist','tags','approvedComments')
            ->where('slug', $slug)
            ->where('status','published')
            ->firstOrFail();

        $article->incrementViews();

        $related = Article::published()
            ->where('category_id', $article->category_id)
            ->where('id','!=',$article->id)
            ->with('category','journalist')
            ->latest('published_at')
            ->limit(4)
            ->get();

        $sidebarAds  = Advertisement::active()->forPosition('sidebar')->get();
        $insideAds   = Advertisement::active()->forPosition('inside_article')->get();
        $popular     = Article::published()->orderByDesc('views')->with('category')->limit(5)->get();

        return view('articles.show', compact('article','related','sidebarAds','insideAds','popular'));
    }

    public function postComment(\Illuminate\Http\Request $request, Article $article)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'nullable|email',
            'content'=> 'required|string|max:2000',
        ]);
        $article->comments()->create([
            'name'   => $request->name,
            'email'  => $request->email,
            'content'=> $request->content,
            'status' => 'pending',
        ]);

        Notification::create([
            'title'   => 'تعليق جديد بانتظار المراجعة',
            'message' => 'علّق ' . $request->name . ' على: ' . $article->title,
            'type'    => 'comment',
        ]);

        return back()->with('success','تم إرسال تعليقك وسيظهر بعد المراجعة.');
    }
}
