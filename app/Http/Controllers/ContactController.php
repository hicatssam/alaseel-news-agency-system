<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Notification;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email',
            'phone'  => 'nullable|string',
            'subject'=> 'nullable|string|max:255',
            'message'=> 'required|string|min:10',
        ]);

        $msg = ContactMessage::create($request->only('name','email','phone','subject','message'));

        Notification::create([
            'title'   => 'رسالة تواصل جديدة من: ' . $msg->name,
            'message' => $msg->subject ? 'الموضوع: ' . $msg->subject : mb_substr($msg->message, 0, 80),
            'type'    => 'contact',
        ]);

        return back()->with('success','تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.');
    }
}
