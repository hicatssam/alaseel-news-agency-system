@extends('layouts.admin')
@section('title', __('admin.videos_create'))
@section('breadcrumb') <a href="{{ route('admin.videos.index') }}">{{ __('admin.nav_videos') }}</a> <span class="sep">›</span> {{ __('admin.btn_add') }} @endsection
@section('content')
<div style="max-width:600px">
<form method="POST" action="{{ route('admin.videos.store') }}">
@csrf
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_title') }} <span style="color:#e74c3c">*</span></label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_category') }}</label>
      <select name="category_id" class="form-control">
        <option value="">{{ __('admin.opt_no_category') }}</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_description') }}</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_thumbnail_url') }}</label>
      <input type="text" name="thumbnail" class="form-control" value="{{ old('thumbnail') }}" placeholder="https://...">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_video_url') }}</label>
      <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://youtube.com/...">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_embed_code') }}</label>
      <textarea name="embed_url" class="form-control" rows="3" placeholder="https://www.youtube.com/embed/...">{{ old('embed_url') }}</textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_status') }}</label>
        <select name="status" class="form-control">
          <option value="draft">{{ __('admin.status_draft') }}</option>
          <option value="published">{{ __('admin.status_published') }}</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="is_featured" value="1"> {{ __('admin.label_featured_video') }}</label>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
      <a href="{{ route('admin.videos.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
</div>
@endsection
