<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Journalist;
use App\Models\Video;
use App\Models\Comment;
use App\Models\Advertisement;
use App\Models\NewsletterSubscriber;
use App\Models\Notification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_articles'         => Article::count(),
            'published_articles'     => Article::where('status','published')->count(),
            'draft_articles'         => Article::where('status','draft')->count(),
            'pending_review'         => Article::where('status','under_review')->count(),
            'total_journalists'      => Journalist::count(),
            'total_videos'           => Video::count(),
            'total_categories'       => Category::count(),
            'total_views'            => Article::sum('views'),
            'total_comments'         => Comment::count(),
            'pending_comments'       => Comment::where('status','pending')->count(),
            'active_ads'             => Advertisement::active()->count(),
            'newsletter_subscribers' => NewsletterSubscriber::active()->count(),
            'breaking_news'          => Article::where('is_breaking',true)->where('status','published')->count(),
            'unread_notifications'   => Notification::whereNull('read_at')->count(),
        ];

        $topArticles = Article::with('category','journalist')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $categoryPerformance = Category::withCount('articles')
            ->withSum('articles','views')
            ->orderByDesc('articles_count')
            ->limit(8)
            ->get();

        $journalistPerformance = Journalist::withCount('articles')
            ->withSum('articles','views')
            ->orderByDesc('articles_sum_views')
            ->limit(8)
            ->get();

        $statusBreakdown = [
            ['status'=>'draft',       'count'=>Article::where('status','draft')->count()],
            ['status'=>'under_review','count'=>Article::where('status','under_review')->count()],
            ['status'=>'approved',    'count'=>Article::where('status','approved')->count()],
            ['status'=>'published',   'count'=>Article::where('status','published')->count()],
            ['status'=>'scheduled',   'count'=>Article::where('status','scheduled')->count()],
            ['status'=>'archived',    'count'=>Article::where('status','archived')->count()],
            ['status'=>'rejected',    'count'=>Article::where('status','rejected')->count()],
        ];

        $statusLabels = collect($statusBreakdown)->pluck('status')->map(function ($s) {
            return match ($s) {
                'draft'        => 'مسودة',
                'under_review' => 'قيد المراجعة',
                'approved'     => 'معتمد',
                'published'    => 'منشور',
                'scheduled'    => 'مجدول',
                'archived'     => 'مؤرشف',
                'rejected'     => 'مرفوض',
                default        => $s,
            };
        })->values()->toArray();

        $statusCounts = collect($statusBreakdown)->pluck('count')->toArray();

        $recentActivity = ActivityLog::with('user')->latest()->limit(15)->get();

        $notifications = Notification::latest()->limit(10)->get();

        return view('admin.dashboard', compact(
            'stats','topArticles','categoryPerformance',
            'journalistPerformance','statusBreakdown','statusLabels','statusCounts',
            'recentActivity','notifications'
        ));
    }
}
