<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journalist;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JournalistController extends Controller
{
    public function index(Request $request)
    {
        $query = Journalist::withCount('articles')->latest();
        if ($request->filled('search')) $query->where('name','like','%'.$request->search.'%');
        if ($request->filled('status')) $query->where('status', (bool)$request->status);
        $journalists = $query->paginate(20)->withQueryString();
        return view('admin.journalists.index', compact('journalists'));
    }

    public function create()
    {
        return view('admin.journalists.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string',
            'photo'     => 'nullable|string',
            'job_title' => 'nullable|string',
            'bio'       => 'nullable|string',
            'facebook'  => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube'   => 'nullable|string',
            'x_twitter' => 'nullable|string',
            'status'    => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        Journalist::create($data);
        ActivityLog::log('create','journalists',"Created journalist: {$data['name']}");
        return redirect()->route('admin.journalists.index')->with('success','تم إضافة الصحفي بنجاح.');
    }

    public function show(Journalist $journalist)
    {
        $articles = $journalist->articles()->with('category')->latest()->paginate(15);
        return view('admin.journalists.show', compact('journalist','articles'));
    }

    public function edit(Journalist $journalist)
    {
        return view('admin.journalists.edit', compact('journalist'));
    }

    public function update(Request $request, Journalist $journalist)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string',
            'photo'     => 'nullable|string',
            'job_title' => 'nullable|string',
            'bio'       => 'nullable|string',
            'facebook'  => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube'   => 'nullable|string',
            'x_twitter' => 'nullable|string',
            'status'    => 'boolean',
        ]);
        $data['status'] = $request->boolean('status', true);
        $journalist->update($data);
        ActivityLog::log('update','journalists',"Updated journalist: {$journalist->name}");
        return redirect()->route('admin.journalists.index')->with('success','تم تحديث بيانات الصحفي بنجاح.');
    }

    public function destroy(Journalist $journalist)
    {
        ActivityLog::log('delete','journalists',"Deleted journalist: {$journalist->name}");
        $journalist->delete();
        return redirect()->route('admin.journalists.index')->with('success','تم حذف الصحفي بنجاح.');
    }
}
