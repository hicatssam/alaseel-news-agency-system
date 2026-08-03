@extends('layouts.admin')
@section('title', __('admin.advertisements_edit'))
@section('breadcrumb') <a href="{{ route('admin.advertisements.index') }}">{{ __('admin.nav_advertisements') }}</a> <span class="sep">›</span> {{ __('admin.btn_edit') }} @endsection
@section('content')
@php
  $hasImage  = (bool) $advertisement->image;
  $isFileImg = $hasImage && !str_starts_with($advertisement->image, 'http');
  $imgPreviewUrl = $hasImage
    ? ($isFileImg ? Storage::url($advertisement->image) : $advertisement->image)
    : null;
@endphp
<div style="max-width:620px">
<form method="POST" action="{{ route('admin.advertisements.update',$advertisement) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_ad_title') }}</label>
      <input type="text" name="title" class="form-control" value="{{ old('title',$advertisement->title) }}" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.col_position') }}</label>
        <select name="position" class="form-control">
          @foreach(['header'=>__('admin.ad_pos_header'),'homepage'=>__('admin.ad_pos_homepage'),'sidebar'=>__('admin.ad_pos_sidebar'),'inside_article'=>__('admin.ad_pos_inside_article'),'footer'=>__('admin.ad_pos_footer'),'popup'=>__('admin.ad_pos_popup'),'video'=>__('admin.ad_pos_video')] as $v=>$l)
          <option value="{{ $v }}" {{ old('position',$advertisement->position)==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.col_type') }}</label>
        <select name="type" class="form-control">
          @foreach(['banner'=>__('admin.ad_type_banner'),'text'=>__('admin.ad_type_text'),'video'=>__('admin.ad_type_video')] as $v=>$l)
          <option value="{{ $v }}" {{ old('type',$advertisement->type)==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Image --}}
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_image') }}</label>

      {{-- Current image preview --}}
      @if($imgPreviewUrl)
      <div style="margin-bottom:10px">
        <img src="{{ $imgPreviewUrl }}" alt="" style="max-height:100px;border-radius:8px;border:1px solid var(--border)" id="current-ad-img">
        <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px">{{ __('admin.ad_current_image') }}</p>
      </div>
      @endif

      {{-- Source toggle --}}
      <div style="display:flex;gap:16px;margin-bottom:10px">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
          <input type="radio" name="image_source" value="url" {{ $isFileImg ? '' : 'checked' }} onchange="toggleImgSrc('url')">
          {{ __('admin.profile_url') }}
        </label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
          <input type="radio" name="image_source" value="file" {{ $isFileImg ? 'checked' : '' }} onchange="toggleImgSrc('file')">
          {{ __('admin.profile_from_device') }}
        </label>
      </div>

      <div id="img_url_wrap" style="{{ $isFileImg ? 'display:none' : '' }}">
        <input type="url" name="image_url" class="form-control"
               value="{{ old('image_url', $isFileImg ? '' : $advertisement->image) }}"
               placeholder="https://example.com/banner.jpg">
        <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px">{{ __('admin.ad_leave_blank_to_keep') }}</p>
      </div>
      <div id="img_file_wrap" style="{{ $isFileImg ? '' : 'display:none' }}">
        <input type="file" name="image_file" class="form-control" accept="image/*" onchange="previewAdImg(this)">
        <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px">{{ __('admin.ad_leave_blank_to_keep') }} · JPG, PNG, GIF, WEBP — {{ __('admin.profile_max_size') }}</p>
        <div id="ad_img_preview" style="margin-top:8px"></div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">{{ __('admin.label_target_url') }}</label>
      <input type="url" name="link" class="form-control" value="{{ old('link',$advertisement->link) }}">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_starts_at') }}</label>
        <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at',$advertisement->starts_at?->format('Y-m-d\TH:i')) }}">
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_ends_at') }}</label>
        <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at',$advertisement->ends_at?->format('Y-m-d\TH:i')) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" {{ $advertisement->status?'checked':'' }}> {{ __('admin.label_active_ad') }}</label>
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
  const curr = document.getElementById('current-ad-img');
  if (!input.files || !input.files[0]) { wrap.innerHTML = ''; return; }
  const reader = new FileReader();
  reader.onload = e => {
    if (curr) curr.src = e.target.result;
    else wrap.innerHTML = `<img src="${e.target.result}" style="max-height:140px;border-radius:8px;border:1px solid var(--border)">`;
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
