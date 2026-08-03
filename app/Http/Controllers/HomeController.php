<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Video;
use App\Models\Advertisement;
use App\Models\NewsletterSubscriber;
use App\Models\Notification;

class HomeController extends Controller
{
    public function index()
    {
        $breakingNews     = Article::breaking()->with('category','journalist')->latest('published_at')->limit(5)->get();
        $featuredArticles = Article::featured()->with('category','journalist')->latest('published_at')->limit(6)->get();
        $latestArticles   = Article::published()->with('category','journalist')->latest('published_at')->limit(12)->get();
        $editorPicks      = Article::editorPick()->with('category','journalist')->latest('published_at')->limit(4)->get();
        $trendingArticles = Article::published()->with('category','journalist')->orderByDesc('views')->limit(8)->get();
        $categories       = Category::active()->withCount(['articles'=>fn($q)=>$q->published()])->orderBy('sort_order')->limit(10)->get();
        $featuredVideos   = Video::published()->featured()->with('category')->latest()->limit(4)->get();
        $sidebarAds       = Advertisement::active()->forPosition('sidebar')->get();
        $homepageAds      = Advertisement::active()->forPosition('homepage')->get();

        return view('home', compact(
            'breakingNews','featuredArticles','latestArticles','editorPicks',
            'trendingArticles','categories','featuredVideos','sidebarAds','homepageAds'
        ));
    }

    public function subscribeNewsletter(\Illuminate\Http\Request $request)
    {
        $request->validate(['email'=>'required|email']);
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            ['status' => 'active', 'subscribed_at' => now()]
        );

        if ($subscriber->wasRecentlyCreated) {
            Notification::create([
                'title'   => 'مشترك جديد في النشرة البريدية',
                'message' => $request->email . ' اشترك في النشرة البريدية.',
                'type'    => 'newsletter',
            ]);
        }

        return back()->with('success','تم الاشتراك في النشرة البريدية بنجاح.');
    }

    public function unsubscribeNewsletter(\Illuminate\Http\Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        NewsletterSubscriber::where('email', $request->query('email'))
            ->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]);

        return view('newsletter.unsubscribed');
    }
}
