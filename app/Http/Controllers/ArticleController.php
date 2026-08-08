<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\Notification;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $article = Article::query()
            ->with([
                'category',
                'journalist',
                'tags',
                'approvedComments',
                'mainImageMedia',
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | تسجيل مشاهدة فريدة واحدة لكل IP على كل مقال
        |--------------------------------------------------------------------------
        */

        $ipHash = hash_hmac(
            'sha256',
            (string) $request->ip(),
            (string) config('app.key')
        );

        $inserted = ArticleView::query()->insertOrIgnore([
            'article_id' => $article->id,
            'ip_hash' => $ipHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted === 1) {
            $article->increment('views');
        }

        /*
         * تحديث عدد المشاهدات مع الحفاظ على العلاقات المحمّلة.
         */
        $article->refresh()->load([
            'category',
            'journalist',
            'tags',
            'approvedComments',
            'mainImageMedia',
        ]);

        /*
        |--------------------------------------------------------------------------
        | الأخبار ذات الصلة
        |--------------------------------------------------------------------------
        */

        $related = Article::query()
            ->with([
                'category',
                'journalist',
                'mainImageMedia',
            ])
            ->whereKeyNot($article->id)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->when(
                $article->category_id,
                fn ($query) => $query->where(
                    'category_id',
                    $article->category_id
                )
            )
            ->latest('published_at')
            ->limit(4)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | الأكثر قراءة
        |--------------------------------------------------------------------------
        */

        $popular = Article::query()
            ->with([
                'category',
                'journalist',
                'mainImageMedia',
            ])
            ->whereKeyNot($article->id)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | الإعلانات
        |--------------------------------------------------------------------------
        */

        $sidebarAds = Advertisement::active()
            ->forPosition('sidebar')
            ->get();

        $insideAds = Advertisement::active()
            ->forPosition('inside_article')
            ->get();

        return view('articles.show', compact(
            'article',
            'related',
            'popular',
            'sidebarAds',
            'insideAds'
        ));
    }

    public function postComment(Request $request, Article $article)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $article->comments()->create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        Notification::create([
            'title' => 'تعليق جديد بانتظار المراجعة',
            'message' => 'علّق ' . $validated['name']
                . ' على: ' . $article->title,
            'type' => 'comment',
        ]);

        return back()->with(
            'success',
            'تم إرسال تعليقك وسيظهر بعد المراجعة.'
        );
    }
}