<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $subscribers = $query->paginate(20)->withQueryString();
        $totalActive = NewsletterSubscriber::active()->count();
        return view('admin.newsletter.index', compact('subscribers','totalActive'));
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->delete();
        return back()->with('success','تم حذف المشترك بنجاح.');
    }
}
