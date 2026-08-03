<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with('article','user')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $comments = $query->paginate(20)->withQueryString();
        return view('admin.comments.index', compact('comments'));
    }

    public function updateStatus(Request $request, Comment $comment)
    {
        $request->validate(['status'=>'required|in:approved,rejected']);
        $comment->update(['status'=>$request->status]);
        ActivityLog::log('update','comments',"Comment status changed to {$request->status}");
        return back()->with('success','تم تحديث حالة التعليق.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success','تم حذف التعليق بنجاح.');
    }
}
