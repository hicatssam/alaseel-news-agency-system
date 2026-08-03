@extends('layouts.admin')
@section('title', __('admin.articles_edit'))
@section('breadcrumb') <a href="{{ route('admin.articles.index') }}">{{ __('admin.nav_articles') }}</a> <span class="sep">›</span> {{ __('admin.btn_edit') }} @endsection
@section('content')

<form method="POST" action="{{ route('admin.articles.update',$article) }}">
@csrf @method('PUT')
<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_article_content') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_main_title') }} <span style="color:#e74c3c">*</span></label>
          <input type="text" name="title" class="form-control" value="{{ old('title',$article->title) }}" required style="font-size:16px;font-weight:700">
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_summary') }}</label>
          <textarea name="summary" class="form-control" rows="3">{{ old('summary',$article->summary) }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_full_content') }} <span style="color:#e74c3c">*</span></label>
          <textarea name="content" class="form-control" rows="18">{{ old('content',$article->content) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_revision_note') }}</label>
          <input type="text" name="revision_note" class="form-control" placeholder="{{ __('admin.placeholder_revision_note') }}">
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><span class="card-title">{{ __('admin.label_seo_section') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_seo_title') }}</label>
          <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title',$article->seo_title) }}">
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_seo_description') }}</label>
          <textarea name="seo_description" class="form-control" rows="2">{{ old('seo_description',$article->seo_description) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_meta_keywords') }}</label>
          <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords',$article->meta_keywords) }}">
        </div>
      </div>
    </div>
  </div>
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_publish_short') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_status') }}</label>
          <select name="status" class="form-control">
            @foreach(['draft' => __('admin.status_draft'), 'under_review' => __('admin.status_review'), 'approved' => __('admin.status_approved'), 'published' => __('admin.status_published'), 'scheduled' => __('admin.status_scheduled'), 'archived' => __('admin.status_archived'), 'rejected' => __('admin.status_rejected')] as $v => $l)
            <option value="{{ $v }}" {{ (old('status',$article->status))==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_schedule_date') }}</label>
          <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at',$article->scheduled_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div style="display:flex;flex-direction:column;gap:10px">
          <label class="form-check"><input type="checkbox" name="is_breaking" value="1" {{ $article->is_breaking?'checked':'' }}> {{ __('admin.label_breaking_news') }}</label>
          <label class="form-check"><input type="checkbox" name="is_featured" value="1" {{ $article->is_featured?'checked':'' }}> {{ __('admin.label_featured_item') }}</label>
          <label class="form-check"><input type="checkbox" name="is_editor_pick" value="1" {{ $article->is_editor_pick?'checked':'' }}> {{ __('admin.label_editor_pick_item') }}</label>
        </div>
      </div>
    </div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_category_journalist') }}</span></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">{{ __('admin.label_category') }}</label>
          <select name="category_id" class="form-control">
            <option value="">{{ __('admin.opt_no_category') }}</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id',$article->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">{{ __('admin.label_journalist') }}</label>
          <select name="journalist_id" class="form-control">
            <option value="">{{ __('admin.opt_no_journalist') }}</option>
            @foreach($journalists as $j)
            <option value="{{ $j->id }}" {{ old('journalist_id',$article->journalist_id)==$j->id?'selected':'' }}>{{ $j->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_main_image_section') }}</span></div>
      <div class="card-body">
        @if($article->main_image)
        <img src="{{ $article->main_image }}" alt="" style="width:100%;border-radius:8px;margin-bottom:8px;object-fit:cover;height:120px" onerror="this.style.display='none'">
        @endif
        <input type="text" name="main_image" class="form-control" value="{{ old('main_image',$article->main_image) }}" placeholder="https://...">
      </div>
    </div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-header"><span class="card-title">{{ __('admin.label_tags') }}</span></div>
      <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach($tags as $tag)
          <label style="cursor:pointer;display:flex;align-items:center;gap:4px;background:#f8f9fa;border:1px solid #dee2e6;padding:4px 8px;border-radius:6px;font-size:12px">
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ $article->tags->contains($tag)?'checked':'' }} style="accent-color:#c9a84c">
            {{ $tag->name }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> {{ __('admin.btn_save_changes') }}</button>
      <a href="{{ route('admin.articles.revisions',$article) }}" class="btn btn-outline"><i class="fa-solid fa-clock-rotate-left"></i> {{ __('admin.btn_revision_log') }} ({{ $article->revisions->count() }})</a>
      <a href="{{ route('admin.articles.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
    </div>
  </div>
</div>
</form>
@endsection
