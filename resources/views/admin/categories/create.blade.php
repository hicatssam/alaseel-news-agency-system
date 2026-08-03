@extends('layouts.admin')
@section('title', __('admin.categories_create'))
@section('breadcrumb') <a href="{{ route('admin.categories.index') }}">{{ __('admin.nav_categories') }}</a> <span class="sep">›</span> {{ __('admin.btn_add') }} @endsection
@section('content')
<div class="card" style="max-width:680px">
  <div class="card-header">
    <div class="card-title"><i class="fa-solid fa-folder-plus" style="color:var(--gold)"></i> {{ __('admin.categories_create') }}</div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-right"></i> {{ __('admin.btn_back') }}</a>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.categories.store') }}">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_category_name') }} <span style="color:red">*</span></label>
          <input name="name" class="form-control" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_slug_auto') }}</label>
          <input name="slug" class="form-control" value="{{ old('slug') }}" dir="ltr" placeholder="auto-generated">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_parent_category') }}</label>
          <select name="parent_id" class="form-control">
            <option value="">{{ __('admin.opt_no_parent') }}</option>
            @foreach($parents as $p)
            <option value="{{ $p->id }}" {{ old('parent_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_color_optional') }}</label>
          <input name="color" class="form-control" value="{{ old('color') }}" placeholder="#C89A2B" dir="ltr">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">{{ __('admin.label_description') }}</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_image_url') }}</label>
          <input name="image" class="form-control" value="{{ old('image') }}" placeholder="https://..." dir="ltr">
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_sort_order') }}</label>
          <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',0) }}">
        </div>
      </div>

      <div style="background:#f8f9fa;border-radius:8px;padding:16px;margin-bottom:16px">
        <div style="font-size:13px;font-weight:700;margin-bottom:12px;color:#333"><i class="fa-solid fa-eye" style="color:var(--gold)"></i> {{ __('admin.label_display_settings') }}</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
          <label class="form-check">
            <input type="checkbox" name="status" value="1" {{ old('status',1)? 'checked':'' }}>
            <span>{{ __('admin.label_active_category') }}</span>
          </label>
          <label class="form-check">
            <input type="checkbox" name="show_in_header" value="1" {{ old('show_in_header',1)? 'checked':'' }}>
            <span>{{ __('admin.label_show_in_header') }}</span>
          </label>
          <label class="form-check">
            <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer',1)? 'checked':'' }}>
            <span>{{ __('admin.label_show_in_footer') }}</span>
          </label>
          <label class="form-check">
            <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage',1)? 'checked':'' }}>
            <span>{{ __('admin.label_show_on_homepage') }}</span>
          </label>
        </div>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.btn_create_category') }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
