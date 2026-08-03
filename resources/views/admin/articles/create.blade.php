@extends('layouts.admin')
@section('title', __('admin.articles_create'))
@section('breadcrumb') <a href="{{ route('admin.articles.index') }}">{{ __('admin.nav_articles') }}</a> <span class="sep">›</span> {{ __('admin.articles_create') }} @endsection
@section('content')

<form method="POST" action="{{ route('admin.articles.store') }}">
@csrf
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_article_content') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_main_title') }} <span style="color:#e74c3c">*</span></label>
          <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="{{ __('admin.placeholder_article_title') }}" required style="font-size:16px;font-weight:700">
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_summary_intro') }}</label>
          <textarea name="summary" class="form-control" rows="3" placeholder="{{ __('admin.placeholder_summary') }}">{{ old('summary') }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_full_content') }} <span style="color:#e74c3c">*</span></label>
          <textarea name="content" id="content" class="form-control" rows="18" placeholder="{{ __('admin.placeholder_content') }}">{{ old('content') }}</textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title">{{ __('admin.label_seo_section') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_seo_title') }}</label>
          <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title') }}" placeholder="{{ __('admin.placeholder_seo_title') }}">
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_seo_description') }}</label>
          <textarea name="seo_description" class="form-control" rows="2" placeholder="{{ __('admin.placeholder_seo_description') }}">{{ old('seo_description') }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_meta_keywords') }}</label>
          <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords') }}" placeholder="{{ __('admin.placeholder_keywords') }}">
        </div>
      </div>
    </div>
  </div>

  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_publish_section') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_status') }}</label>
          <select name="status" class="form-control">
            <option value="draft">{{ __('admin.status_draft') }}</option>
            <option value="under_review">{{ __('admin.status_review') }}</option>
            <option value="approved">{{ __('admin.status_approved') }}</option>
            <option value="published">{{ __('admin.status_published') }}</option>
            <option value="scheduled">{{ __('admin.status_scheduled') }}</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_schedule_date') }}</label>
          <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <label class="form-check"><input type="checkbox" name="is_breaking" value="1"> <span>{{ __('admin.label_breaking_news') }}</span></label>
          <label class="form-check"><input type="checkbox" name="is_featured" value="1"> <span>{{ __('admin.label_featured_item') }}</span></label>
          <label class="form-check"><input type="checkbox" name="is_editor_pick" value="1"> <span>{{ __('admin.label_editor_pick_item') }}</span></label>
        </div>
      </div>
    </div>

    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_category_journalist') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_category') }}</label>
          <select name="category_id" class="form-control">
            <option value="">{{ __('admin.opt_select_category') }}</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_journalist') }}</label>
          <select name="journalist_id" class="form-control">
            <option value="">{{ __('admin.opt_select_journalist') }}</option>
            @foreach($journalists as $j)
            <option value="{{ $j->id }}" {{ old('journalist_id')==$j->id?'selected':'' }}>{{ $j->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_main_image_section') }}</span></div>
      <div class="card-body">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_image_url') }}</label>
          <input type="text" name="main_image" class="form-control" value="{{ old('main_image') }}" placeholder="https://...">
        </div>
      </div>
    </div>

    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_tags') }}</span></div>
      <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach($tags as $tag)
          <label style="cursor:pointer;display:flex;align-items:center;gap:4px;background:#f8f9fa;border:1px solid #dee2e6;padding:4px 8px;border-radius:6px;font-size:12px">
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" style="accent-color:#c9a84c">
            {{ $tag->name }}
          </label>
          @endforeach
        </div>
      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save_article') }}</button>
      <a href="{{ route('admin.articles.index') }}" class="btn btn-outline" style="flex:1">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
@endsection
