<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'photo_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'remove_photo' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | حذف الصورة الحالية
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('remove_photo')) {
            $this->deleteLocalPhoto($user->photo);
            $validated['photo'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | رفع صورة جديدة
        |--------------------------------------------------------------------------
        | الصورة المرفوعة لها الأولوية على رابط الصورة.
        */

        if ($request->hasFile('photo')) {
            $this->deleteLocalPhoto($user->photo);

            $validated['photo'] = $request
                ->file('photo')
                ->store('users', 'public');
        } elseif ($request->filled('photo_url')) {
            $this->deleteLocalPhoto($user->photo);

            $validated['photo'] = $request->input('photo_url');
        }

        /*
        |--------------------------------------------------------------------------
        | تحديث كلمة المرور
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make(
                $validated['password']
            );
        } else {
            unset($validated['password']);
        }

        unset(
            $validated['photo_url'],
            $validated['remove_photo']
        );

        $user->update($validated);

        ActivityLog::log(
            'update',
            'profile',
            "Updated own profile: {$user->email}"
        );

        return back()->with(
            'success',
            __('admin.profile_updated')
        );
    }

    private function deleteLocalPhoto(?string $photo): void
    {
        if (
            empty($photo) ||
            str_starts_with($photo, 'http://') ||
            str_starts_with($photo, 'https://') ||
            str_starts_with($photo, '//')
        ) {
            return;
        }

        $photoPath = ltrim($photo, '/');
        $photoPath = preg_replace('#^storage/#', '', $photoPath);

        if (
            $photoPath &&
            Storage::disk('public')->exists($photoPath)
        ) {
            Storage::disk('public')->delete($photoPath);
        }
    }
}