@extends('layouts.admin')
@section('title', __('admin.videos_edit'))
@section('breadcrumb') <a href="{{ route('admin.videos.index') }}">{{ __('admin.nav_videos') }}</a> <span class="sep">›</span> {{ __('admin.btn_edit') }} @endsection
@section('content')
<div style="max-width:600px">
<form method="POST" action="{{ route('admin.videos.update',$video) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_title') }}</label>
      <input type="text" name="title" class="form-control" value="{{ old('title',$video->title) }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_category') }}</label>
      <select name="category_id" class="form-control">
        <option value="">{{ __('admin.opt_no_category') }}</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ old('category_id',$video->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_description') }}</label>
      <textarea name="description" class="form-control" rows="4">{{ old('description',$video->description) }}</textarea>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_thumbnail') }}</label>
      <input type="text" name="thumbnail" class="form-control" value="{{ old('thumbnail',$video->thumbnail) }}">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_video_url') }}</label>
      <input type="url" name="video_url" class="form-control" value="{{ old('video_url',$video->video_url) }}">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_embed_url') }}</label>
      <textarea name="embed_url" class="form-control" rows="2">{{ old('embed_url',$video->embed_url) }}</textarea>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_status') }}</label>
      <select name="status" class="form-control">
        @foreach(['draft' => __('admin.status_draft'), 'published' => __('admin.status_published'), 'archived' => __('admin.status_archived')] as $v => $l)
        <option value="{{ $v }}" {{ old('status',$video->status)==$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="is_featured" value="1" {{ $video->is_featured?'checked':'' }}> {{ __('admin.label_featured_video_simple') }}</label>
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
