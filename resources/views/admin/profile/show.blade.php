@extends('layouts.admin')
@section('title', __('admin.menu_profile'))
@section('breadcrumb') {{ __('admin.menu_profile') }} @endsection
@section('content')
@php
  $avatarUrl = $user->avatar
    ? (str_starts_with($user->avatar, 'http') ? $user->avatar : Storage::url($user->avatar))
    : null;
@endphp
<div style="max-width:620px">
<form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
@csrf @method('PUT')

{{-- Avatar card --}}
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><h3 class="card-title">{{ __('admin.profile_avatar') }}</h3></div>
  <div class="card-body">
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:18px">
      <div id="avatar-preview-wrap" style="flex-shrink:0">
        @if($avatarUrl)
        <img id="avatar-preview" src="{{ $avatarUrl }}" alt="avatar"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)">
        @else
        <div id="avatar-initials"
             style="width:80px;height:80px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:#111">
          {{ mb_substr($user->name, 0, 1) }}
        </div>
        @endif
      </div>
      <div style="flex:1">
        <p style="color:rgba(255,255,255,.5);font-size:13px;margin:0 0 10px">{{ __('admin.profile_avatar_hint') }}</p>
        <div style="display:flex;gap:16px;margin-bottom:12px">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
            <input type="radio" name="avatar_source" value="url" id="src_url"
                   {{ ($user->avatar && str_starts_with($user->avatar,'http')) ? 'checked' : ((!$user->avatar || !str_starts_with($user->avatar,'http')) && !($user->avatar && !str_starts_with($user->avatar,'http')) ? 'checked' : '') }}
                   onchange="toggleAvatarSource('url')">
            {{ __('admin.profile_url') }}
          </label>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
            <input type="radio" name="avatar_source" value="file" id="src_file"
                   {{ ($user->avatar && !str_starts_with($user->avatar,'http')) ? 'checked' : '' }}
                   onchange="toggleAvatarSource('file')">
            {{ __('admin.profile_from_device') }}
          </label>
        </div>
        <div id="avatar_url_wrap" style="{{ ($user->avatar && !str_starts_with($user->avatar,'http')) ? 'display:none' : '' }}">
          <input type="url" name="avatar_url" class="form-control" id="avatar_url_input"
                 value="{{ ($user->avatar && str_starts_with($user->avatar,'http')) ? $user->avatar : '' }}"
                 placeholder="https://example.com/photo.jpg">
        </div>
        <div id="avatar_file_wrap" style="{{ ($user->avatar && !str_starts_with($user->avatar,'http')) ? '' : 'display:none' }}">
          <input type="file" name="avatar_file" id="avatar_file_input" class="form-control"
                 accept="image/*" onchange="previewAvatar(this)">
          <p style="color:rgba(255,255,255,.4);font-size:11px;margin-top:4px">JPG, PNG, GIF, WEBP — {{ __('admin.profile_max_size') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Personal info --}}
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><h3 class="card-title">{{ __('admin.profile_personal_info') }}</h3></div>
  <div class="card-body">
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_name') }} <span style="color:#e74c3c">*</span></label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_email') }} <span style="color:#e74c3c">*</span></label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_phone') }}</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+966...">
    </div>
  </div>
</div>

{{-- Change password --}}
<div class="card" style="margin-bottom:20px">
  <div class="card-header"><h3 class="card-title">{{ __('admin.profile_change_password') }}</h3></div>
  <div class="card-body">
    <p style="color:rgba(255,255,255,.45);font-size:13px;margin:0 0 14px">{{ __('admin.profile_password_hint') }}</p>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_new_password') }}</label>
      <input type="password" name="password" class="form-control" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label class="form-label">{{ __('admin.label_password_confirm') }}</label>
      <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
    </div>
  </div>
</div>

<div style="display:flex;gap:8px">
  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save') }}</button>
</div>
</form>
</div>

<script>
function toggleAvatarSource(src) {
  document.getElementById('avatar_url_wrap').style.display  = src === 'url'  ? '' : 'none';
  document.getElementById('avatar_file_wrap').style.display = src === 'file' ? '' : 'none';
  // Clear the inactive input
  if (src === 'url')  document.getElementById('avatar_file_input').value = '';
  else                document.getElementById('avatar_url_input').value  = '';
}
function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    let img = document.getElementById('avatar-preview');
    const initials = document.getElementById('avatar-initials');
    if (!img) {
      img = document.createElement('img');
      img.id = 'avatar-preview';
      img.style.cssText = 'width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--gold)';
      document.getElementById('avatar-preview-wrap').innerHTML = '';
      document.getElementById('avatar-preview-wrap').appendChild(img);
    }
    if (initials) initials.style.display = 'none';
    img.src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}
// Set initial radio state
(function(){
  const urlRadio  = document.getElementById('src_url');
  const fileRadio = document.getElementById('src_file');
  if (fileRadio && fileRadio.checked) toggleAvatarSource('file');
  else toggleAvatarSource('url');
})();
</script>
@endsection
