@extends('layouts.admin')
@section('title', __('admin.journalists_create'))
@section('breadcrumb') <a href="{{ route('admin.journalists.index') }}">{{ __('admin.nav_journalists') }}</a> <span class="sep">›</span> {{ __('admin.btn_add') }} @endsection
@section('content')
<div style="max-width:700px">
<form method="POST" action="{{ route('admin.journalists.store') }}">
@csrf
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.label_journalist_data') }}</span></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_name') }} <span style="color:#e74c3c">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_job_title') }}</label>
        <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}" placeholder="{{ __('admin.placeholder_job_title') }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_phone') }}</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_image_url') }}</label>
      <input type="text" name="photo" class="form-control" value="{{ old('photo') }}" placeholder="https://...">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_bio') }}</label>
      <textarea name="bio" class="form-control" rows="4" placeholder="{{ __('admin.placeholder_bio') }}">{{ old('bio') }}</textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-facebook" style="color:#1877f2"></i> Facebook</label>
        <input type="text" name="facebook" class="form-control" value="{{ old('facebook') }}" placeholder="https://facebook.com/...">
      </div>
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-x-twitter" style="color:#000"></i> Twitter / X</label>
        <input type="text" name="x_twitter" class="form-control" value="{{ old('x_twitter') }}" placeholder="https://twitter.com/...">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-instagram" style="color:#e1306c"></i> Instagram</label>
        <input type="text" name="instagram" class="form-control" value="{{ old('instagram') }}">
      </div>
      <div class="form-group">
        <label class="form-label"><i class="fa-brands fa-youtube" style="color:#ff0000"></i> YouTube</label>
        <input type="text" name="youtube" class="form-control" value="{{ old('youtube') }}">
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" checked> {{ __('admin.label_active_journalist') }}</label>
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
