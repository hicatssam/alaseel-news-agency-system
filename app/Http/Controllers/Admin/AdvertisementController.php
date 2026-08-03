<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $query = Advertisement::latest();

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $ads = $query->paginate(20)->withQueryString();
        return view('admin.advertisements.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'position'    => 'required|string',
            'type'        => 'nullable|string',
            'image_file'  => 'nullable|image|max:4096',
            'image_url'   => 'nullable|url|max:500',
            'link'        => 'nullable|url',
            'status'      => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data['image']  = $this->resolveImage($request, null);
        $data['status'] = $request->boolean('status');
        $data['user_id']= auth()->id();
        unset($data['image_file'], $data['image_url']);

        $ad = Advertisement::create($data);
        ActivityLog::log('create', 'advertisements', "Created ad: {$ad->title}");
        return redirect()->route('admin.advertisements.index')
                         ->with('success', __('admin.ad_created'));
    }

    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'position'    => 'required|string',
            'type'        => 'nullable|string',
            'image_file'  => 'nullable|image|max:4096',
            'image_url'   => 'nullable|url|max:500',
            'link'        => 'nullable|url',
            'status'      => 'boolean',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        ]);

        $newImage = $this->resolveImage($request, $advertisement->image);
        if ($newImage !== $advertisement->image) {
            // Delete old stored file if it was a local upload
            if ($advertisement->image && !str_starts_with($advertisement->image, 'http')) {
                Storage::disk('public')->delete($advertisement->image);
            }
        }
        $data['image']  = $newImage;
        $data['status'] = $request->boolean('status');
        unset($data['image_file'], $data['image_url']);

        $advertisement->update($data);
        ActivityLog::log('update', 'advertisements', "Updated ad: {$advertisement->title}");
        return redirect()->route('admin.advertisements.index')
                         ->with('success', __('admin.ad_updated'));
    }

    public function destroy(Advertisement $advertisement)
    {
        if ($advertisement->image && !str_starts_with($advertisement->image, 'http')) {
            Storage::disk('public')->delete($advertisement->image);
        }
        ActivityLog::log('delete', 'advertisements', "Deleted ad: {$advertisement->title}");
        $advertisement->delete();
        return redirect()->route('admin.advertisements.index')
                         ->with('success', __('admin.ad_deleted'));
    }

    /** Resolve final image value: uploaded file → URL field → keep existing */
    private function resolveImage(Request $request, ?string $existing): ?string
    {
        if ($request->hasFile('image_file')) {
            return $request->file('image_file')->store('ads', 'public');
        }
        if ($request->filled('image_url')) {
            return $request->image_url;
        }
        return $existing;
    }
}
