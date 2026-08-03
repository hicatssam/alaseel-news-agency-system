@extends('layouts.admin')
@section('title', __('admin.advertisements_create'))
@section('breadcrumb') <a href="{{ route('admin.advertisements.index') }}">{{ __('admin.nav_advertisements') }}</a> <span class="sep">›</span> {{ __('admin.btn_create') }} @endsection
@section('content')
<div style="max-width:620px">
<form method="POST" action="{{ route('admin.advertisements.store') }}" enctype="multipart/form-data">
@csrf
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_ad_title') }} <span style="color:#e74c3c">*</span></label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.col_position') }} <span style="color:#e74c3c">*</span></label>
        <select name="position" class="form-control" required>
          @foreach(['header'=>__('admin.ad_pos_header'),'homepage'=>__('admin.ad_pos_homepage'),'sidebar'=>__('admin.ad_pos_sidebar'),'inside_article'=>__('admin.ad_pos_inside_article'),'footer'=>__('admin.ad_pos_footer'),'popup'=>__('admin.ad_pos_popup'),'video'=>__('admin.ad_pos_video')] as $v=>$l)
          <option value="{{ $v }}" {{ old('position')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.col_type') }}</label>
        <select name="type" class="form-control">
          <option value="banner">{{ __('admin.ad_type_banner') }}</option>
          <option value="text">{{ __('admin.ad_type_text') }}</option>
          <option value="video">{{ __('admin.ad_type_video') }}</option>
        </select>
      </div>
    </div>

    {{-- Image source selector --}}
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_image') }}</label>
      <div style="display:flex;gap:16px;margin-bottom:10px">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
          <input type="radio" name="image_source" value="url" checked onchange="toggleImgSrc('url')">
          {{ __('admin.profile_url') }}
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
          <input type="radio" name="image_source" value="file" onchange="toggleImgSrc('file')">
          {{ __('admin.profile_from_device') }}
        </label>
      </div>
      <div id="img_url_wrap">
        <input type="url" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://example.com/banner.jpg">
      </div>
      <div id="img_file_wrap" style="display:none">
        <input type="file" name="image_file" class="form-control" accept="image/*" onchange="previewAdImg(this)">
        <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px">JPG, PNG, GIF, WEBP — {{ __('admin.profile_max_size') }}</p>
        <div id="ad_img_preview" style="margin-top:8px"></div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">{{ __('admin.label_target_url') }}</label>
      <input type="url" name="link" class="form-control" value="{{ old('link') }}" placeholder="https://...">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_starts_at') }}</label>
        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_ends_at') }}</label>
        <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" checked> {{ __('admin.label_active_ad') }}</label>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
      <a href="{{ route('admin.advertisements.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
</div>

<script>
function toggleImgSrc(src) {
  document.getElementById('img_url_wrap').style.display  = src === 'url'  ? '' : 'none';
  document.getElementById('img_file_wrap').style.display = src === 'file' ? '' : 'none';
}
function previewAdImg(input) {
  const wrap = document.getElementById('ad_img_preview');
  if (!input.files || !input.files[0]) { wrap.innerHTML = ''; return; }
  const reader = new FileReader();
  reader.onload = e => {
    wrap.innerHTML = `<img src="${e.target.result}" style="max-height:140px;border-radius:8px;border:1px solid var(--border)">`;
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
