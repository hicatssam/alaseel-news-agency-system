@extends('layouts.admin')
@section('title', __('admin.tags_create'))
@section('breadcrumb') <a href="{{ route('admin.tags.index') }}">{{ __('admin.nav_tags') }}</a> <span class="sep">›</span> {{ __('admin.btn_add') }} @endsection
@section('content')
<div style="max-width:400px">
<form method="POST" action="{{ route('admin.tags.store') }}">
@csrf
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_tag_name') }} <span style="color:#e74c3c">*</span></label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="{{ __('admin.placeholder_tag_name') }}">
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" checked> {{ __('admin.label_active_tag') }}</label>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
      <a href="{{ route('admin.tags.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
</div>
@endsection
