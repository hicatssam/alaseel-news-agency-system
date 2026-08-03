@extends('layouts.admin')
@section('title','مكتبة الوسائط')
@section('breadcrumb') مكتبة الوسائط @endsection
@section('content')
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  @php
    $totalSizeMB = number_format(($stats['total_size']??0)/1048576,1);
  @endphp
  <div class="stat-card" style="border-color:#c9a84c;padding:14px">
    <div class="stat-icon" style="background:rgba(201,168,76,.1);color:#c9a84c;width:38px;height:38px;font-size:16px"><i class="fa-solid fa-photo-film"></i></div>
    <div><div class="stat-value" style="font-size:18px">{{ $stats['total'] }}</div><div class="stat-label">إجمالي الملفات</div></div>
  </div>
  <div class="stat-card" style="border-color:#3498db;padding:14px">
    <div class="stat-icon" style="background:#eaf4fd;color:#3498db;width:38px;height:38px;font-size:16px"><i class="fa-solid fa-image"></i></div>
    <div><div class="stat-value" style="font-size:18px">{{ $stats['images'] }}</div><div class="stat-label">صور</div></div>
  </div>
  <div class="stat-card" style="border-color:#e74c3c;padding:14px">
    <div class="stat-icon" style="background:#fdf0f0;color:#e74c3c;width:38px;height:38px;font-size:16px"><i class="fa-solid fa-video"></i></div>
    <div><div class="stat-value" style="font-size:18px">{{ $stats['videos'] }}</div><div class="stat-label">فيديوهات</div></div>
  </div>
  <div class="stat-card" style="border-color:#27ae60;padding:14px">
    <div class="stat-icon" style="background:#eafaf1;color:#27ae60;width:38px;height:38px;font-size:16px"><i class="fa-solid fa-hdd"></i></div>
    <div><div class="stat-value" style="font-size:18px">{{ $totalSizeMB }} MB</div><div class="stat-label">الحجم الكلي</div></div>
  </div>
</div>

<div style="display:flex;justify-content:space-between;margin-bottom:16px;align-items:center">
  <div class="filter-bar" style="flex:1;margin-bottom:0;margin-left:12px">
    <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
      <select name="file_type" class="form-control" style="max-width:160px">
        <option value="">كل الأنواع</option>
        <option value="image" {{ request('file_type')=='image'?'selected':'' }}>صور</option>
        <option value="video" {{ request('file_type')=='video'?'selected':'' }}>فيديو</option>
        <option value="document" {{ request('file_type')=='document'?'selected':'' }}>مستندات</option>
      </select>
      <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="max-width:200px">
      <button class="btn btn-secondary"><i class="fa-solid fa-search"></i></button>
    </form>
  </div>
  <label class="btn btn-primary" style="cursor:pointer">
    <i class="fa-solid fa-upload"></i> رفع ملف
    <form id="uploadForm" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" style="display:none">
      @csrf
      <input type="file" name="file" id="fileInput" onchange="document.getElementById('uploadForm').submit()">
    </form>
  </label>
</div>

<div class="card">
  <div class="card-header"><span class="card-title">الملفات ({{ $files->total() }})</span></div>
  <div class="card-body">
    @if($files->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px">
      @foreach($files as $file)
      <div style="border:1px solid #e8e8e8;border-radius:10px;overflow:hidden;position:relative;group">
        <div style="height:120px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;overflow:hidden">
          @if($file->file_type=='image')
          <img src="{{ Storage::url($file->file_path) }}" alt="{{ $file->alt_text }}" style="width:100%;height:100%;object-fit:cover" onerror="this.parentElement.innerHTML='<i class=\'fa-solid fa-image\' style=\'font-size:32px;color:#ccc\'></i>'">
          @else
          <i class="fa-solid {{ $file->file_type=='video'?'fa-video':($file->file_type=='audio'?'fa-music':'fa-file') }}" style="font-size:32px;color:#ccc"></i>
          @endif
        </div>
        <div style="padding:8px">
          <div style="font-size:11px;font-weight:600;color:#1a1a2e;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $file->file_name }}</div>
          <div style="font-size:10px;color:#aaa">{{ $file->formatted_size }}</div>
          <div style="display:flex;justify-content:flex-end;margin-top:6px">
            <form method="POST" action="{{ route('admin.media.destroy',$file) }}" onsubmit="return confirm('حذف؟')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm btn-icon" style="width:24px;height:24px;font-size:10px"><i class="fa-solid fa-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="empty-state"><i class="fa-solid fa-photo-film"></i><p>لا توجد ملفات. ارفع ملفاً للبدء.</p></div>
    @endif
  </div>
  @if($files->hasPages())<div style="padding:16px">{{ $files->links() }}</div>@endif
</div>
<script>document.querySelector('label[class*="btn-primary"]').addEventListener('click',function(e){document.getElementById('fileInput').click();});</script>
@endsection
