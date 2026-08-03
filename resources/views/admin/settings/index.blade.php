@extends('layouts.admin')
@section('title', __('admin.settings_index'))
@section('breadcrumb') {{ __('admin.nav_settings') }} @endsection
@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  @foreach($settings as $group => $groupSettings)
  <div class="card">
    <div class="card-header">
      <span class="card-title">
        @php
          $groupNames = [
            'general'  => __('admin.settings_group_general'),
            'contact'  => __('admin.settings_group_contact'),
            'social'   => __('admin.settings_group_social'),
            'display'  => __('admin.settings_group_display'),
            'features' => __('admin.settings_group_features'),
          ];
        @endphp
        {{ $groupNames[$group] ?? $group }}
      </span>
    </div>
    <div class="card-body">
      @foreach($groupSettings as $setting)
      <div class="form-group">
        <label class="form-label">{{ $setting->key }}</label>
        <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ old('settings.'.$setting->key, $setting->value) }}">
      </div>
      @endforeach
    </div>
  </div>
  @endforeach
</div>
<div style="margin-top:20px">
  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save_settings') }}</button>
</div>
</form>
@endsection
