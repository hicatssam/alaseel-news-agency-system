<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::with('user')->latest();
        if ($request->filled('file_type')) $query->where('file_type', $request->file_type);
        if ($request->filled('folder'))    $query->where('folder', $request->folder);
        if ($request->filled('search'))    $query->where('file_name','like','%'.$request->search.'%');
        $files   = $query->paginate(24)->withQueryString();
        $folders = MediaFile::select('folder')->distinct()->whereNotNull('folder')->pluck('folder');
        $stats   = [
            'total'    => MediaFile::count(),
            'images'   => MediaFile::where('file_type','image')->count(),
            'videos'   => MediaFile::where('file_type','video')->count(),
            'documents'=> MediaFile::where('file_type','document')->count(),
            'total_size'=> MediaFile::sum('size'),
        ];
        return view('admin.media.index', compact('files','folders','stats'));
    }

    public function store(Request $request)
    {
        $request->validate(['file' => 'required|file|max:51200']);
        $file     = $request->file('file');
        $mime     = $file->getMimeType();
        $fileType = str_contains($mime,'image') ? 'image'
                  : (str_contains($mime,'video') ? 'video'
                  : (str_contains($mime,'audio') ? 'audio' : 'document'));

        $folder   = $request->input('folder','general');
        $path     = $file->store("media/{$folder}", 'public');

        $media = MediaFile::create([
            'user_id'  => auth()->id(),
            'file_name'=> $file->getClientOriginalName(),
            'file_path'=> $path,
            'file_type'=> $fileType,
            'mime_type'=> $mime,
            'size'     => $file->getSize(),
            'folder'   => $folder,
            'alt_text' => $request->input('alt_text'),
        ]);

        ActivityLog::log('upload','media',"Uploaded file: {$media->file_name}");
        return response()->json(['success'=>true,'media'=>$media]);
    }

    public function update(Request $request, MediaFile $mediaFile)
    {
        $data = $request->validate([
            'alt_text'=> 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'folder'  => 'nullable|string|max:100',
        ]);
        $mediaFile->update($data);
        return back()->with('success','تم تحديث بيانات الملف بنجاح.');
    }

    public function destroy(MediaFile $mediaFile)
    {
        ActivityLog::log('delete','media',"Deleted file: {$mediaFile->file_name}");
        $mediaFile->delete();
        return back()->with('success','تم حذف الملف بنجاح.');
    }
}
