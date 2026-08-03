<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('admin.profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'phone'                 => 'nullable|string|max:30',
            'password'              => 'nullable|min:8|confirmed',
            'avatar_file'           => 'nullable|image|max:4096',
            'avatar_url'            => 'nullable|url|max:500',
        ]);

        // Avatar: uploaded file takes priority over URL
        if ($request->hasFile('avatar_file')) {
            // Remove old file if it was a stored upload
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar_file')->store('avatars', 'public');
        } elseif ($request->filled('avatar_url')) {
            $data['avatar'] = $request->avatar_url;
        }
        unset($data['avatar_file'], $data['avatar_url']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        ActivityLog::log('update', 'profile', 'Updated own profile');

        return back()->with('success', __('admin.profile_updated'));
    }
}
