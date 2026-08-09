<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class TeamMemberController extends Controller
{
    /**
     * Display the team members list.
     */
    public function index(Request $request): View
    {
        $this->ensureAuthenticated($request);

        $query = TeamMember::query();

        if ($request->filled('search')) {
            $search = trim(
                $request->string('search')->toString()
            );

            $query->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if (
            $request->filled('status')
            && in_array($request->input('status'), ['0', '1'], true)
        ) {
            $query->where(
                'is_active',
                $request->input('status') === '1'
            );
        }

        $teamMembers = $query
            ->orderBy('display_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.team-members.index',
            compact('teamMembers')
        );
    }

    /**
     * Show the team member creation form.
     */
    public function create(Request $request): View
    {
        $this->ensureAuthenticated($request);

        return view('admin.team-members.create');
    }

    /**
     * Store a new team member.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureAuthenticated($request);

        $validated = $request->validate(
            $this->validationRules(imageRequired: true),
            $this->validationMessages()
        );

        $validated['is_active'] = $request->boolean('is_active');

        $newImagePath = null;

        try {
            $newImagePath = $this->storeTeamMemberImage($request);

            DB::transaction(function () use (
                $validated,
                $newImagePath
            ): void {
                $teamMember = new TeamMember();

                $teamMember->name = trim($validated['name']);
                $teamMember->job_title = trim(
                    $validated['job_title']
                );
                $teamMember->image = $newImagePath;
                $teamMember->display_order =
                    $validated['display_order'] ?? 0;
                $teamMember->is_active =
                    $validated['is_active'];

                $teamMember->save();
            });
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->deletePublicImage($newImagePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'تعذر إضافة عضو الفريق. يرجى المحاولة مرة أخرى.'
                );
        }

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'تمت إضافة عضو الفريق بنجاح.');
    }

    /**
     * Show the team member edit form.
     */
    public function edit(
        Request $request,
        TeamMember $teamMember
    ): View {
        $this->ensureAuthenticated($request);

        return view(
            'admin.team-members.edit',
            compact('teamMember')
        );
    }

    /**
     * Update an existing team member.
     */
    public function update(
        Request $request,
        TeamMember $teamMember
    ): RedirectResponse {
        $this->ensureAuthenticated($request);

        $validated = $request->validate(
            $this->validationRules(imageRequired: false),
            $this->validationMessages()
        );

        $validated['is_active'] = $request->boolean('is_active');

        $oldImagePath = $teamMember->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeTeamMemberImage(
                    $request
                );
            }

            DB::transaction(function () use (
                $teamMember,
                $validated,
                $newImagePath
            ): void {
                $teamMember->name = trim($validated['name']);
                $teamMember->job_title = trim(
                    $validated['job_title']
                );
                $teamMember->display_order =
                    $validated['display_order'] ?? 0;
                $teamMember->is_active =
                    $validated['is_active'];

                if ($newImagePath !== null) {
                    $teamMember->image = $newImagePath;
                }

                $teamMember->save();
            });
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                $this->deletePublicImage($newImagePath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'تعذر تحديث عضو الفريق. يرجى المحاولة مرة أخرى.'
                );
        }

        if (
            $newImagePath !== null
            && filled($oldImagePath)
            && $oldImagePath !== $newImagePath
        ) {
            $this->deletePublicImage($oldImagePath);
        }

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'تم تحديث عضو الفريق بنجاح.');
    }

    /**
     * Delete a team member.
     */
    public function destroy(
        Request $request,
        TeamMember $teamMember
    ): RedirectResponse {
        $this->ensureAuthenticated($request);

        $imagePath = $teamMember->image;

        $usesSoftDeletes = in_array(
            SoftDeletes::class,
            class_uses_recursive($teamMember),
            true
        );

        try {
            DB::transaction(function () use ($teamMember): void {
                $teamMember->delete();
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'تعذر حذف عضو الفريق. يرجى المحاولة مرة أخرى.'
            );
        }

        /*
         * Keep the image if SoftDeletes is enabled so the member can be
         * restored later. For a permanent deletion, remove the local image.
         */
        if (!$usesSoftDeletes) {
            $this->deletePublicImage($imagePath);
        }

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'تم حذف عضو الفريق بنجاح.');
    }

    /**
     * Validation rules shared by create and update.
     */
    private function validationRules(
        bool $imageRequired
    ): array {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'job_title' => [
                'required',
                'string',
                'max:255',
            ],
            'image' => [
                $imageRequired ? 'required' : 'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'max:5120',
            ],
            'display_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Arabic validation messages.
     */
    private function validationMessages(): array
    {
        return [
            'name.required' => 'اسم عضو الفريق مطلوب.',
            'name.max' => 'يجب ألا يتجاوز الاسم 255 حرفًا.',
            'job_title.required' => 'المسمى الوظيفي مطلوب.',
            'job_title.max' => 'يجب ألا يتجاوز المسمى الوظيفي 255 حرفًا.',
            'image.required' => 'صورة عضو الفريق مطلوبة.',
            'image.image' => 'الملف المختار يجب أن يكون صورة صحيحة.',
            'image.mimes' => 'صيغة الصورة يجب أن تكون JPG أو JPEG أو PNG أو WEBP أو GIF.',
            'image.mimetypes' => 'نوع ملف الصورة غير مسموح به.',
            'image.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت.',
            'display_order.integer' => 'ترتيب الظهور يجب أن يكون رقمًا صحيحًا.',
            'display_order.min' => 'ترتيب الظهور لا يمكن أن يكون سالبًا.',
            'display_order.max' => 'ترتيب الظهور لا يمكن أن يتجاوز 9999.',
        ];
    }

    /**
     * Store a team image with a unique safe filename.
     */
    private function storeTeamMemberImage(
        Request $request
    ): string {
        $image = $request->file('image');

        $extension = strtolower(
            $image->guessExtension()
            ?: $image->extension()
            ?: 'jpg'
        );

        $filename = 'team-member-'
            . Str::uuid()
            . '.'
            . $extension;

        return $image->storeAs(
            'team-members',
            $filename,
            'public'
        );
    }

    /**
     * Delete only locally stored files from the public disk.
     */
    private function deletePublicImage(?string $path): void
    {
        if (!filled($path)) {
            return;
        }

        $path = trim(str_replace('\\', '/', $path));

        if (
            Str::startsWith($path, [
                'http://',
                'https://',
                '//',
            ])
        ) {
            return;
        }

        $path = Str::after($path, 'storage/app/public/');
        $path = Str::after($path, 'public/');

        if (Str::startsWith($path, '/storage/')) {
            $path = Str::after($path, '/storage/');
        } elseif (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        } elseif (Str::startsWith($path, '/')) {
            return;
        }

        if ($path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Authentication and permissions are inherited from the existing
     * protected admin route group.
     */
    private function ensureAuthenticated(Request $request): void
    {
        abort_unless($request->user(), 401);
    }
}