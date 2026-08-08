<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    /**
     * عرض صفحة جميع الفيديوهات المنشورة.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
        ]);

        $videos = Video::query()
            ->published()
            ->with('category')
            ->when(
                $validated['category_id'] ?? null,
                fn ($query, $categoryId) =>
                    $query->where('category_id', $categoryId)
            )
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->active()
            ->orderBy('name')
            ->get();

        $featured = Video::query()
            ->published()
            ->featured()
            ->with('category')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('videos.index', compact(
            'videos',
            'categories',
            'featured'
        ));
    }

    /**
     * عرض تفاصيل فيديو واحد.
     */
    public function show(string $slug): View
    {
        $video = Video::query()
            ->published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $video->increment('views');

        $related = Video::query()
            ->published()
            ->with('category')
            ->whereKeyNot($video->getKey())
            ->when(
                $video->category_id,
                fn ($query, $categoryId) =>
                    $query->where('category_id', $categoryId)
            )
            ->latest('published_at')
            ->limit(6)
            ->get();

        return view('videos.show', compact(
            'video',
            'related'
        ));
    }
}