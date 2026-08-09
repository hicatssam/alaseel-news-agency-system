<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\TeamMember;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AboutPageController extends Controller
{

public function index(Request $request): View
{
    $this->ensureAuthenticated($request);

    $aboutPage = AboutPage::query()->first();

    $teamMembersCount = TeamMember::query()->count();

    $activeTeamMembersCount = TeamMember::query()
        ->where('is_active', true)
        ->count();

    return view('admin.about.index', compact(
        'aboutPage',
        'teamMembersCount',
        'activeTeamMembersCount'
    ));
}

    /**
     * Show the About Us settings page.
     */
    public function edit(Request $request): View
    {
        $this->ensureAuthenticated($request);

        $aboutPage = AboutPage::query()->first();

        return view('admin.about.edit', compact('aboutPage'));
    }

    /**
     * Create or update the About Us page.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->ensureAuthenticated($request);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'subtitle' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'content' => [
                'required',
                'string',
                'max:200000',
            ],
            'vision' => [
                'nullable',
                'string',
                'max:100000',
            ],
            'mission' => [
                'nullable',
                'string',
                'max:100000',
            ],
            'values' => [
                'nullable',
                'string',
                'max:100000',
            ],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif',
                'max:5120',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'title.required' => 'عنوان الصفحة مطلوب.',
            'title.max' => 'يجب ألا يتجاوز عنوان الصفحة 255 حرفًا.',
            'subtitle.max' => 'يجب ألا يتجاوز العنوان التعريفي 1000 حرف.',
            'content.required' => 'محتوى صفحة من نحن مطلوب.',
            'image.image' => 'الملف المختار يجب أن يكون صورة صحيحة.',
            'image.mimes' => 'صيغة الصورة يجب أن تكون JPG أو JPEG أو PNG أو WEBP أو GIF.',
            'image.mimetypes' => 'نوع ملف الصورة غير مسموح به.',
            'image.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميجابايت.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $aboutPage = AboutPage::query()->first() ?? new AboutPage();

        $oldImagePath = $aboutPage->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $this->storeAboutImage($request);
            }

            $sanitizedContent = $this->sanitizeRichHtml(
                $validated['content']
            );

            $sanitizedVision = $this->sanitizeRichHtml(
                $validated['vision'] ?? null
            );

            $sanitizedMission = $this->sanitizeRichHtml(
                $validated['mission'] ?? null
            );

            $sanitizedValues = $this->sanitizeRichHtml(
                $validated['values'] ?? null
            );

            DB::transaction(function () use (
                $aboutPage,
                $validated,
                $sanitizedContent,
                $sanitizedVision,
                $sanitizedMission,
                $sanitizedValues,
                $newImagePath
            ): void {
                $aboutPage->title = trim($validated['title']);
                $aboutPage->subtitle = $this->nullableTrim(
                    $validated['subtitle'] ?? null
                );
                $aboutPage->content = $sanitizedContent;
                $aboutPage->vision = $sanitizedVision;
                $aboutPage->mission = $sanitizedMission;
                $aboutPage->values = $sanitizedValues;
                $aboutPage->is_active = $validated['is_active'];

                if ($newImagePath !== null) {
                    $aboutPage->image = $newImagePath;
                }

                $aboutPage->save();
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
                    'تعذر حفظ صفحة من نحن. يرجى المحاولة مرة أخرى.'
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
            ->route('admin.about.edit')
            ->with('success', 'تم تحديث صفحة من نحن بنجاح.');
    }

    /**
     * Store an About Us image using a unique safe filename.
     */
    private function storeAboutImage(Request $request): string
    {
        $image = $request->file('image');

        $extension = strtolower(
            $image->guessExtension()
            ?: $image->extension()
            ?: 'jpg'
        );

        $filename = 'about-' . Str::uuid() . '.' . $extension;

        return $image->storeAs(
            'about',
            $filename,
            'public'
        );
    }

    /**
     * Sanitize rich HTML before saving it to the database.
     */
    private function sanitizeRichHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $config = HTMLPurifier_Config::createDefault();

        $config->set(
            'HTML.Allowed',
            implode(',', [
                'p[class|style|dir]',
                'br',
                'hr',
                'h1[class|style|dir]',
                'h2[class|style|dir]',
                'h3[class|style|dir]',
                'h4[class|style|dir]',
                'h5[class|style|dir]',
                'h6[class|style|dir]',
                'strong',
                'b',
                'em',
                'i',
                'u',
                's',
                'strike',
                'span[class|style|dir]',
                'div[class|style|dir]',
                'blockquote[class|style|dir]',
                'ul[class|style|dir]',
                'ol[class|style|dir|start]',
                'li[class|style|dir]',
                'a[href|title|target|rel]',
                
              
                'img[src|alt|title|width|height|class|style]',
                'table[class|style]',
                'thead',
                'tbody',
                'tfoot',
                'tr',
                'th[colspan|rowspan|scope|class|style]',
                'td[colspan|rowspan|class|style]',
            ])
        );

        $config->set('HTML.SafeIframe', false);
        $config->set('HTML.Nofollow', true);
        $config->set('HTML.TargetBlank', true);
        $config->set('Attr.EnableID', false);

        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
        ]);

        $config->set('CSS.AllowedProperties', [
            'text-align',
            'direction',
            'color',
            'background-color',
            'font-family',
            'font-size',
            'font-weight',
            'font-style',
            'text-decoration',
            'margin',
            'margin-left',
            'margin-right',
            'padding',
            'width',
            'height',
            'max-width',
            'float',
            'clear',
            'border',
            'border-width',
            'border-style',
            'border-color',
        ]);

        return (new HTMLPurifier($config))->purify($html);
    }

    /**
     * Delete only locally stored public disk images.
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

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Authentication and role permissions remain controlled by the existing
     * protected admin route group.
     */
    private function ensureAuthenticated(Request $request): void
    {
        abort_unless($request->user(), 401);
    }
}