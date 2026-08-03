@extends('layouts.admin')
@section('title', __('admin.users_edit'))
@section('breadcrumb') <a href="{{ route('admin.users.index') }}">{{ __('admin.nav_users') }}</a> <span class="sep">›</span> {{ __('admin.btn_edit') }} @endsection
@section('content')
<div style="max-width:550px">
<form method="POST" action="{{ route('admin.users.update',$user) }}">
@csrf @method('PUT')
<div class="card">
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_name') }}</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_email') }}</label>
      <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_new_password') }}</label>
        <input type="password" name="password" class="form-control" minlength="8">
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_password_confirm') }}</label>
        <input type="password" name="password_confirmation" class="form-control">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_phone') }}</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone',$user->phone) }}">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_permissions') }}</label>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        @foreach($roles as $role)
        <label class="form-check" style="background:#f8f9fa;padding:6px 10px;border-radius:6px;border:1px solid #dee2e6">
          <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ $user->roles->contains($role)?'checked':'' }} style="accent-color:#c9a84c">
          {{ $role->name }}
        </label>
        @endforeach
      </div>
    </div>
    <div class="form-group">
      <label class="form-check"><input type="checkbox" name="status" value="1" {{ $user->status?'checked':'' }}> {{ __('admin.label_active_user') }}</label>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
</div>
@endsection
