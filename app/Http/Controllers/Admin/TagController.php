<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::withCount('articles')->latest();
        if ($request->filled('search')) $query->where('name','like','%'.$request->search.'%');
        $tags = $query->paginate(20)->withQueryString();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255|unique:tags,name',
            'status'=> 'boolean',
        ]);
        $data['slug']   = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);
        Tag::create($data);
        ActivityLog::log('create','tags',"Created tag: {$data['name']}");
        return redirect()->route('admin.tags.index')->with('success','تم إنشاء الوسم بنجاح.');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255|unique:tags,name,'.$tag->id,
            'status'=> 'boolean',
        ]);
        $data['slug']   = Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);
        $tag->update($data);
        ActivityLog::log('update','tags',"Updated tag: {$tag->name}");
        return redirect()->route('admin.tags.index')->with('success','تم تحديث الوسم بنجاح.');
    }

    public function destroy(Tag $tag)
    {
        ActivityLog::log('delete','tags',"Deleted tag: {$tag->name}");
        $tag->delete();
        return redirect()->route('admin.tags.index')->with('success','تم حذف الوسم بنجاح.');
    }
}
