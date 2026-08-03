<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with('category')->latest();
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('search'))      $query->where('title','like','%'.$request->search.'%');
        $videos     = $query->paginate(20)->withQueryString();
        $categories = Category::active()->get();
        return view('admin.videos.index', compact('videos','categories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.videos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|string',
            'video_url'   => 'nullable|url',
            'embed_url'   => 'nullable|string',
            'status'      => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);
        $data['slug']       = Str::slug($data['title']) . '-' . Str::random(6);
        $data['is_featured']= $request->boolean('is_featured');
        $data['user_id']    = auth()->id();
        if ($data['status'] === 'published') $data['published_at'] = now();
        Video::create($data);
        ActivityLog::log('create','videos',"Created video: {$data['title']}");
        return redirect()->route('admin.videos.index')->with('success','تم إضافة الفيديو بنجاح.');
    }

    public function edit(Video $video)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.videos.edit', compact('video','categories'));
    }

    public function update(Request $request, Video $video)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'thumbnail'   => 'nullable|string',
            'video_url'   => 'nullable|url',
            'embed_url'   => 'nullable|string',
            'status'      => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
        ]);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['status'] === 'published' && !$video->published_at) $data['published_at'] = now();
        $video->update($data);
        ActivityLog::log('update','videos',"Updated video: {$video->title}");
        return redirect()->route('admin.videos.index')->with('success','تم تحديث الفيديو بنجاح.');
    }

    public function destroy(Video $video)
    {
        ActivityLog::log('delete','videos',"Deleted video: {$video->title}");
        $video->delete();
        return redirect()->route('admin.videos.index')->with('success','تم حذف الفيديو بنجاح.');
    }
}
