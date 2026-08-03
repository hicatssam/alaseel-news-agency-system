<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('articles')->latest();
        if ($request->filled('search')) $query->where('name','like','%'.$request->search.'%');
        $categories = $query->paginate(20)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->active()->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|unique:categories',
            'parent_id'       => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|string',
            'color'           => 'nullable|string|max:20',
            'status'          => 'boolean',
            'sort_order'      => 'integer',
            'show_in_header'  => 'boolean',
            'show_in_footer'  => 'boolean',
            'show_on_homepage'=> 'boolean',
        ]);
        $data['slug']            = $data['slug'] ?? Str::slug($data['name']);
        $data['status']          = $request->boolean('status', true);
        $data['show_in_header']  = $request->boolean('show_in_header', true);
        $data['show_in_footer']  = $request->boolean('show_in_footer', true);
        $data['show_on_homepage']= $request->boolean('show_on_homepage', true);
        Category::create($data);
        ActivityLog::log('create','categories',"Created category: {$data['name']}");
        return redirect()->route('admin.categories.index')->with('success','تم إنشاء التصنيف بنجاح.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->active()->where('id','!=',$category->id)->get();
        return view('admin.categories.edit', compact('category','parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|unique:categories,slug,'.$category->id,
            'parent_id'       => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|string',
            'color'           => 'nullable|string|max:20',
            'status'          => 'boolean',
            'sort_order'      => 'integer',
            'show_in_header'  => 'boolean',
            'show_in_footer'  => 'boolean',
            'show_on_homepage'=> 'boolean',
        ]);
        $data['status']          = $request->boolean('status', true);
        $data['show_in_header']  = $request->boolean('show_in_header', false);
        $data['show_in_footer']  = $request->boolean('show_in_footer', false);
        $data['show_on_homepage']= $request->boolean('show_on_homepage', false);
        $category->update($data);
        ActivityLog::log('update','categories',"Updated category: {$category->name}");
        return redirect()->route('admin.categories.index')->with('success','تم تحديث التصنيف بنجاح.');
    }

    public function destroy(Category $category)
    {
        ActivityLog::log('delete','categories',"Deleted category: {$category->name}");
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success','تم حذف التصنيف بنجاح.');
    }
}
