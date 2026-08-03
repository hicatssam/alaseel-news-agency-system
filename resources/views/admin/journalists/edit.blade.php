@extends('layouts.admin')
@section('title', __('admin.journalists_edit'))
@section('breadcrumb') <a href="{{ route('admin.journalists.index') }}">{{ __('admin.nav_journalists') }}</a> <span class="sep">›</span> {{ __('admin.btn_edit') }} @endsection
@section('content')
<div style="max-width:700px">
<form method="POST" action="{{ route('admin.journalists.update',$journalist) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.journalists_edit') }}: {{ $journalist->name }}</span></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_name') }} <span style="color:#e74c3c">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name',$journalist->name) }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_job_title') }}</label>
        <input type="text" name="job_title" class="form-control" value="{{ old('job_title',$journalist->job_title) }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email',$journalist->email) }}">
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_phone') }}</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone',$journalist->phone) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_image_url') }}</label>
      <input type="text" name="photo" class="form-control" value="{{ old('photo',$journalist->photo) }}">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_bio') }}</label>
      <textarea name="bio" class="form-control" rows="4">{{ old('bio',$journalist->bio) }}</textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-facebook" style="color:#1877f2"></i> Facebook</label>
        <input type="text" name="facebook" class="form-control" value="{{ old('facebook',$journalist->facebook) }}">
      </div>
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-x-twitter" style="color:#000"></i> Twitter / X</label>
        <input type="text" name="x_twitter" class="form-control" value="{{ old('x_twitter',$journalist->x_twitter) }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-instagram" style="color:#e1306c"></i> Instagram</label>
        <input type="text" name="instagram" class="form-control" value="{{ old('instagram',$journalist->instagram) }}">
      </div>
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-youtube" style="color:#ff0000"></i> YouTube</label>
        <input type="text" name="youtube" class="form-control" value="{{ old('youtube',$journalist->youtube) }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" {{ $journalist->status?'checked':'' }}> {{ __('admin.label_active_journalist') }}</label>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
      <a href="{{ route('admin.journalists.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
</div>
@endsection
