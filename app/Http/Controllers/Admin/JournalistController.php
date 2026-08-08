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
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'job_title' => ['nullable', 'string', 'max:255'],
        'bio' => ['nullable', 'string'],

        'photo_file' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp,gif',
            'max:5120',
        ],

        'photo_url' => [
            'nullable',
            'url',
            'max:2048',
        ],

        'facebook' => ['nullable', 'url', 'max:2048'],
        'instagram' => ['nullable', 'url', 'max:2048'],
        'youtube' => ['nullable', 'url', 'max:2048'],
        'x_twitter' => ['nullable', 'url', 'max:2048'],
        'status' => ['nullable', 'boolean'],
    ]);

    $photo = $validated['photo_url'] ?? null;

    if ($request->hasFile('photo_file')) {
        $photo = $request
            ->file('photo_file')
            ->store('journalists', 'public');
    }

    Journalist::create([
        'user_id' => auth()->id(),
        'name' => $validated['name'],
        'email' => $validated['email'] ?? null,
        'phone' => $validated['phone'] ?? null,
        'photo' => $photo,
        'job_title' => $validated['job_title'] ?? null,
        'bio' => $validated['bio'] ?? null,
        'facebook' => $validated['facebook'] ?? null,
        'instagram' => $validated['instagram'] ?? null,
        'youtube' => $validated['youtube'] ?? null,
        'x_twitter' => $validated['x_twitter'] ?? null,
        'status' => $request->boolean('status'),
    ]);

    return redirect()
        ->route('admin.journalists.index')
        ->with('success', 'تمت إضافة الصحفي بنجاح.');
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
