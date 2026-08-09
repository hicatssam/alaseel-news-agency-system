<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('admin.profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

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

            'photo_source' => [
                'required',
                Rule::in(['url', 'file']),
            ],

            'photo' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'max:4096',
            ],

            'photo_url' => [
                'nullable',
                'url:http,https',
                'max:500',
            ],

            'remove_photo' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',

            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',

            'photo_source.required' => 'يرجى تحديد مصدر الصورة.',
            'photo_source.in' => 'مصدر الصورة المحدد غير صحيح.',

            'photo.file' => 'ملف الصورة المرفوع غير صحيح.',
            'photo.image' => 'الملف المختار يجب أن يكون صورة صحيحة.',
            'photo.mimes' => 'صيغة الصورة يجب أن تكون JPG أو PNG أو WEBP أو GIF.',
            'photo.mimetypes' => 'نوع ملف الصورة غير مسموح به.',
            'photo.max' => 'يجب ألا يتجاوز حجم الصورة 4 ميجابايت.',

            'photo_url.url' => 'رابط الصورة غير صحيح.',
            'photo_url.max' => 'يجب ألا يتجاوز رابط الصورة 500 حرف.',
        ]);

        $oldPhoto = $user->photo;
        $newPhoto = $oldPhoto;
        $newUploadedPhoto = null;

        try {
            /*
            |--------------------------------------------------------------------------
            | معالجة صورة المستخدم
            |--------------------------------------------------------------------------
            | الأولوية:
            | 1. الصورة المرفوعة
            | 2. رابط الصورة
            | 3. حذف الصورة
            */

            if (
                $validated['photo_source'] === 'file'
                && $request->hasFile('photo')
            ) {
                $newUploadedPhoto = $request
                    ->file('photo')
                    ->store('users', 'public');

                if (
                    !$newUploadedPhoto
                    || !Storage::disk('public')->exists(
                        $newUploadedPhoto
                    )
                ) {
                    throw new \RuntimeException(
                        'تعذر حفظ صورة المستخدم على القرص العام.'
                    );
                }

                $newPhoto = $newUploadedPhoto;
            } elseif (
                $validated['photo_source'] === 'url'
                && $request->filled('photo_url')
            ) {
                $newPhoto = trim(
                    $request->string('photo_url')->toString()
                );
            } elseif ($request->boolean('remove_photo')) {
                $newPhoto = null;
            }

            /*
            |--------------------------------------------------------------------------
            | تحديث بيانات المستخدم
            |--------------------------------------------------------------------------
            */

            $user->name = trim($validated['name']);
            $user->email = trim($validated['email']);

            $user->phone = filled($validated['phone'] ?? null)
                ? trim($validated['phone'])
                : null;

            /*
             * الحقل الأساسي المعتمد.
             */
            $user->photo = $newPhoto;

            /*
             * توافق مؤقت مع الصفحات القديمة التي ما زالت تستخدم avatar.
             * يمكن حذف هذا السطر بعد تحويل جميع الصفحات إلى photo.
             */
            $user->avatar = $newPhoto;

            if (!empty($validated['password'])) {
                $user->password = Hash::make(
                    $validated['password']
                );
            }

            $user->saveOrFail();
        } catch (Throwable $exception) {
            /*
             * إذا فشل تحديث قاعدة البيانات نحذف الصورة الجديدة فقط،
             * ولا نحذف الصورة القديمة.
             */
            if ($newUploadedPhoto !== null) {
                Storage::disk('public')->delete(
                    $newUploadedPhoto
                );
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'تعذر تحديث الملف الشخصي. يرجى المحاولة مرة أخرى.'
                );
        }

        /*
         * حذف الصورة القديمة بعد نجاح تحديث المستخدم فقط.
         */
        if (
            filled($oldPhoto)
            && $oldPhoto !== $newPhoto
        ) {
            $this->deleteLocalPhoto($oldPhoto);
        }

        try {
            ActivityLog::log(
                'update',
                'profile',
                "Updated own profile: {$user->email}"
            );
        } catch (Throwable $exception) {
            /*
             * لا نفشل تحديث الملف الشخصي إذا فشل تسجيل النشاط.
             */
            report($exception);
        }

        return back()->with(
            'success',
            __('admin.profile_updated')
        );
    }

    private function deleteLocalPhoto(?string $photo): void
    {
        if (!filled($photo)) {
            return;
        }

        $photo = trim(str_replace('\\', '/', $photo));

        if (
            str_starts_with($photo, 'http://')
            || str_starts_with($photo, 'https://')
            || str_starts_with($photo, '//')
        ) {
            return;
        }

        $photoPath = $photo;

        if (str_contains($photoPath, 'storage/app/public/')) {
            $photoPath = substr(
                $photoPath,
                strpos($photoPath, 'storage/app/public/')
                    + strlen('storage/app/public/')
            );
        }

        if (str_starts_with($photoPath, 'public/')) {
            $photoPath = substr(
                $photoPath,
                strlen('public/')
            );
        }

        $photoPath = ltrim($photoPath, '/');

        if (str_starts_with($photoPath, 'storage/')) {
            $photoPath = substr(
                $photoPath,
                strlen('storage/')
            );
        }

        if (
            $photoPath !== ''
            && Storage::disk('public')->exists($photoPath)
        ) {
            Storage::disk('public')->delete($photoPath);
        }
    }
}