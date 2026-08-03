<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();
        if ($request->filled('search')) $query->where('name','like','%'.$request->search.'%')
            ->orWhere('email','like','%'.$request->search.'%');
        $users = $query->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users',
            'password'=> 'required|min:8|confirmed',
            'phone'   => 'nullable|string',
            'status'  => 'boolean',
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);
        $data['status']   = $request->boolean('status', true);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if ($request->filled('roles')) $user->roles()->sync($request->roles);
        ActivityLog::log('create','users',"Created user: {$user->email}");

        Notification::create([
            'title'   => 'مستخدم جديد: ' . $user->name,
            'message' => 'تم تسجيل مستخدم جديد بالبريد: ' . $user->email,
            'type'    => 'user',
        ]);

        return redirect()->route('admin.users.index')->with('success','تم إنشاء المستخدم بنجاح.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
            'status'=> 'boolean',
            'roles' => 'nullable|array',
            'roles.*'=> 'exists:roles,id',
        ]);
        if ($request->filled('password')) {
            $request->validate(['password'=>'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $data['status'] = $request->boolean('status', true);
        $user->update($data);
        $user->roles()->sync($request->roles ?? []);
        ActivityLog::log('update','users',"Updated user: {$user->email}");
        return redirect()->route('admin.users.index')->with('success','تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        ActivityLog::log('delete','users',"Deleted user: {$user->email}");
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','تم حذف المستخدم بنجاح.');
    }
}
