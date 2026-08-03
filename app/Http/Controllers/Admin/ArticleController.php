<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\Journalist;
use App\Models\Tag;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /*
    |--------------------------------------------------------------------
    | ROLE HELPERS
    |--------------------------------------------------------------------
    */
    protected function isSuperAdmin(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    protected function isEditor(): bool
    {
        return auth()->user()->hasRole('editor');
    }

    protected function isJournalist(): bool
    {
        return auth()->user()->hasRole('journalist');
    }

    /**
     * Ensure a journalist can only act on their own article.
     * Super admin and editor always pass through.
     */
    protected function authorizeJournalistArticle(Article $article): void
    {
        if ($this->isJournalist() && $article->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا المقال.');
        }
    }

    public function index(Request $request)
    {
        $query = Article::with('category','journalist','tags')
            ->latest();

        // Journalists can only see their own articles
        if ($this->isJournalist()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('category_id'))  $query->where('category_id', $request->category_id);
        if ($request->filled('journalist_id'))$query->where('journalist_id', $request->journalist_id);
        if ($request->filled('search'))       $query->where('title','like','%'.$request->search.'%');
        if ($request->boolean('is_breaking')) $query->where('is_breaking', true);
        if ($request->boolean('is_featured')) $query->where('is_featured', true);

        $articles    = $query->paginate(20)->withQueryString();
        $categories  = Category::active()->get();

        // Journalist should only be able to select their own Journalist profile
        if ($this->isJournalist()) {
            $journalists = Journalist::active()->where('user_id', auth()->id())->get();
        } else {
            $journalists = Journalist::active()->get();
        }

        return view('admin.articles.index', compact('articles','categories','journalists'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();

        // Journalist should only select his own Journalist profile.
        // Editors and Super Admin can select all.
        if ($this->isJournalist()) {
            $journalists = Journalist::active()->where('user_id', auth()->id())->orderBy('name')->get();
        } else {
            $journalists = Journalist::active()->orderBy('name')->get();
        }

        $tags = Tag::active()->orderBy('name')->get();
        return view('admin.articles.create', compact('categories','journalists','tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:500',
            'content'        => 'required|string',
            'summary'        => 'nullable|string',
            'category_id'    => 'nullable|exists:categories,id',
            'journalist_id'  => 'nullable|exists:journalists,id',
            'main_image'     => 'nullable|string',
            'status'         => 'required|in:draft,under_review,approved,scheduled,published,archived,rejected',
            'is_breaking'    => 'boolean',
            'is_featured'    => 'boolean',
            'is_editor_pick' => 'boolean',
            'scheduled_at'   => 'nullable|date',
            'seo_title'      => 'nullable|string|max:255',
            'seo_description'=> 'nullable|string',
            'meta_keywords'  => 'nullable|string',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
        ]);

        $data['user_id']     = auth()->id();
        $data['slug']        = Str::slug($data['title']) . '-' . Str::random(6);
        $data['is_breaking'] = $request->boolean('is_breaking');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_editor_pick'] = $request->boolean('is_editor_pick');

        // Journalist restrictions: force status and disable breaking/featured/editor_pick
        // regardless of submitted values.
        if ($this->isJournalist()) {
            $data['status']         = 'under_review';
            $data['is_breaking']    = false;
            $data['is_featured']    = false;
            $data['is_editor_pick'] = false;
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $article = Article::create($data);

        if ($request->filled('tags')) {
            $article->tags()->sync($request->tags);
        }

        ActivityLog::log('create','articles',"Created article: {$article->title}");

        Notification::create([
            'title'   => 'مقال جديد: ' . $article->title,
            'message' => 'تمت إضافة مقال جديد بحالة: ' . $article->status,
            'type'    => 'article',
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم إنشاء المقال بنجاح.');
    }

    public function show(Article $article)
    {
        $article->load('category','journalist','tags','revisions.editor','comments');
        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        // Journalist can only edit his own article
        $this->authorizeJournalistArticle($article);

        $article->load('tags');
        $categories = Category::active()->orderBy('name')->get();

        if ($this->isJournalist()) {
            $journalists = Journalist::active()->where('user_id', auth()->id())->orderBy('name')->get();
        } else {
            $journalists = Journalist::active()->orderBy('name')->get();
        }

        $tags = Tag::active()->orderBy('name')->get();
        return view('admin.articles.edit', compact('article','categories','journalists','tags'));
    }

    public function update(Request $request, Article $article)
    {
        // Journalist can only update his own article
        $this->authorizeJournalistArticle($article);

        $data = $request->validate([
            'title'          => 'required|string|max:500',
            'content'        => 'required|string',
            'summary'        => 'nullable|string',
            'category_id'    => 'nullable|exists:categories,id',
            'journalist_id'  => 'nullable|exists:journalists,id',
            'main_image'     => 'nullable|string',
            'status'         => 'required|in:draft,under_review,approved,scheduled,published,archived,rejected',
            'is_breaking'    => 'boolean',
            'is_featured'    => 'boolean',
            'is_editor_pick' => 'boolean',
            'scheduled_at'   => 'nullable|date',
            'seo_title'      => 'nullable|string|max:255',
            'seo_description'=> 'nullable|string',
            'meta_keywords'  => 'nullable|string',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            'revision_note'  => 'nullable|string',
        ]);

        // Save revision before update
        ArticleRevision::create([
            'article_id'   => $article->id,
            'user_id'      => auth()->id(),
            'old_title'    => $article->title,
            'old_summary'  => $article->summary,
            'old_content'  => $article->content,
            'old_status'   => $article->status,
            'revision_note'=> $request->revision_note,
        ]);

        $data['is_breaking']    = $request->boolean('is_breaking');
        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_editor_pick'] = $request->boolean('is_editor_pick');

        // Journalist restrictions: force status to under_review and strip
        // breaking / featured / editor_pick flags. Prevents publishing.
        if ($this->isJournalist()) {
            $data['status'] = 'under_review';
            unset($data['is_breaking'], $data['is_featured'], $data['is_editor_pick']);
        }

        if ($data['status'] === 'published' && !$article->published_at) {
            $data['published_at'] = now();
        }

        $article->update($data);
        $article->tags()->sync($request->tags ?? []);

        ActivityLog::log('update','articles',"Updated article: {$article->title}");

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم تحديث المقال بنجاح.');
    }

    public function destroy(Article $article)
    {
        // Journalist can only delete his own article (if enabled below)
        $this->authorizeJournalistArticle($article);

        // ---------------------------------------------------------------
        // OPTIONAL: Disable article deletion for journalists.
        // To PREVENT journalists from deleting their own articles,
        // uncomment the block below.
        //
        // if ($this->isJournalist()) {
        //     abort(403, 'غير مصرح لك بحذف المقالات.');
        // }
        // ---------------------------------------------------------------

        ActivityLog::log('delete','articles',"Deleted article: {$article->title}");
        $article->delete();
        return redirect()->route('admin.articles.index')
            ->with('success', 'تم حذف المقال بنجاح.');
    }

    public function updateStatus(Request $request, Article $article)
    {
        // Journalists are never allowed to change article status
        // (publish / approve / reject / archive, etc.)
        if ($this->isJournalist()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:draft,under_review,approved,scheduled,published,archived,rejected']);

        if ($request->status === 'published' && !$article->published_at) {
            $article->published_at = now();
        }

        $article->update(['status' => $request->status]);
        ActivityLog::log('status_change','articles',"Changed article status to {$request->status}: {$article->title}");

        return back()->with('success', 'تم تغيير حالة المقال.');
    }

    public function revisions(Article $article)
    {
        // Journalist can only view revisions of his own article
        $this->authorizeJournalistArticle($article);

        $revisions = $article->revisions()->with('editor')->paginate(20);
        return view('admin.articles.revisions', compact('article','revisions'));
    }
}