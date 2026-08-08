<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Article;
use App\Models\Category;
use App\Models\NewsletterSubscriber;
use App\Models\Notification;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $breakingNews = Article::breaking()
            ->with(['category', 'journalist'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $featuredArticles = Article::featured()
            ->with(['category', 'journalist'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        $latestArticles = Article::published()
            ->with(['category', 'journalist'])
            ->latest('published_at')
            ->limit(12)
            ->get();

        $editorPicks = Article::editorPick()
            ->with(['category', 'journalist'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $trendingArticles = Article::published()
            ->with(['category', 'journalist'])
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->withCount([
                'articles' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        $featuredVideos = Video::published()
            ->featured()
            ->with('category')
            ->latest()
            ->limit(4)
            ->get();

        /*
         * جلب الإعلانات مباشرة دون الاعتماد على Scopes الموديل.
         * يسمح بتاريخ بداية أو نهاية فارغ.
         */
        $advertisementsQuery = Advertisement::query()
            ->where(function ($query) {
                $query->where('status', 1)
                    ->orWhere('status', 'active');
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });

      

       


            $homepageAds = Advertisement::query()
    ->active()
    ->forPosition('homepage')
    ->latest()
    ->get();

$sidebarAds = Advertisement::query()
    ->active()
    ->forPosition('sidebar')
    ->latest()
    ->get();

        return view('home', compact(
            'breakingNews',
            'featuredArticles',
            'latestArticles',
            'editorPicks',
            'trendingArticles',
            'categories',
            'featuredVideos',
            'sidebarAds',
            'homepageAds'
        ));
    }

    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $validated['email']],
            [
                'status' => 'active',
                'subscribed_at' => now(),
            ]
        );

        if ($subscriber->wasRecentlyCreated) {
            Notification::create([
                'title' => 'مشترك جديد في النشرة البريدية',
                'message' => $validated['email'] . ' اشترك في النشرة البريدية.',
                'type' => 'newsletter',
            ]);
        }

        return back()->with(
            'success',
            'تم الاشتراك في النشرة البريدية بنجاح.'
        );
    }

    public function unsubscribeNewsletter(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        NewsletterSubscriber::where(
            'email',
            $request->query('email')
        )->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return view('newsletter.unsubscribed');
    }
}