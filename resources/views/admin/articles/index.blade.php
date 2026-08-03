@extends('layouts.admin')
@section('title', __('admin.articles_index'))
@section('breadcrumb') {{ __('admin.articles_index') }} @endsection
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    @foreach([['all', __('admin.filter_all')], ['published', __('admin.status_published')], ['draft', __('admin.status_draft')], ['under_review', __('admin.status_review')], ['scheduled', __('admin.status_scheduled')], ['archived', __('admin.status_archived')]] as [$v,$l])
    <a href="{{ route('admin.articles.index',array_merge(request()->except('status','page'),['status'=>$v=='all'?null:$v])) }}"
       class="btn btn-sm {{ request('status')==$v || ($v=='all'&&!request('status')) ? 'btn-primary' : 'btn-outline' }}">{{ $l }}</a>
    @endforeach
  </div>
  <a href="{{ route('admin.articles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.articles_create') }}</a>
</div>

<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.placeholder_search_articles') }}" value="{{ request('search') }}" style="max-width:280px">
    <select name="category_id" class="form-control" style="max-width:180px">
      <option value="">{{ __('admin.filter_all_categories') }}</option>
      @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
    <select name="journalist_id" class="form-control" style="max-width:180px">
      <option value="">{{ __('admin.filter_all_journalists') }}</option>
      @foreach($journalists as $j)
      <option value="{{ $j->id }}" {{ request('journalist_id')==$j->id?'selected':'' }}>{{ $j->name }}</option>
      @endforeach
    </select>
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i> {{ __('admin.btn_search') }}</button>
    @if(request()->hasAny(['search','category_id','journalist_id']))
    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline">{{ __('admin.btn_reset') }}</a>
    @endif
  </form>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">{{ __('admin.articles_index') }} ({{ $articles->total() }})</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('admin.col_title') }}</th>
          <th>{{ __('admin.col_category') }}</th>
          <th>{{ __('admin.col_journalist') }}</th>
          <th>{{ __('admin.col_status') }}</th>
          <th>{{ __('admin.col_views') }}</th>
          <th>{{ __('admin.col_date') }}</th>
          <th>{{ __('admin.col_actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
        <tr>
          <td style="font-size:12px;color:#999">{{ $article->id }}</td>
          <td style="max-width:280px">
            <div style="font-weight:600;font-size:13px;color:#1a1a2e;line-height:1.4">
              {{ Str::limit($article->title, 65) }}
            </div>
            <div style="margin-top:4px;display:flex;gap:4px;flex-wrap:wrap">
              @if($article->is_breaking)<span class="badge badge-danger" style="font-size:10px">{{ __('admin.badge_breaking') }}</span>@endif
              @if($article->is_featured)<span class="badge badge-gold" style="font-size:10px">{{ __('admin.badge_featured') }}</span>@endif
              @if($article->is_editor_pick)<span class="badge badge-info" style="font-size:10px">{{ __('admin.badge_editor_pick') }}</span>@endif
            </div>
          </td>
          <td><span class="badge badge-secondary">{{ $article->category?->name ?? '—' }}</span></td>
          <td style="font-size:13px">{{ $article->journalist?->name ?? '—' }}</td>
          <td>
            <form method="POST" action="{{ route('admin.articles.status',$article) }}">
              @csrf @method('PATCH')
              <select name="status" class="form-control" style="padding:4px 8px;font-size:12px;width:auto" onchange="this.form.submit()">
                @foreach(['draft' => __('admin.status_draft'), 'under_review' => __('admin.status_review'), 'approved' => __('admin.status_approved'), 'published' => __('admin.status_published'), 'scheduled' => __('admin.status_scheduled'), 'archived' => __('admin.status_archived'), 'rejected' => __('admin.status_rejected')] as $v => $l)
                <option value="{{ $v }}" {{ $article->status==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </form>
          </td>
          <td style="font-weight:700;color:#c9a84c">{{ number_format($article->views) }}</td>
          <td style="font-size:12px;color:#888;white-space:nowrap">{{ $article->created_at->format('Y/m/d') }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('articles.show',$article->slug) }}" target="_blank" class="btn btn-outline btn-sm btn-icon" title="{{ __('admin.btn_view') }}"><i class="fa-solid fa-eye"></i></a>
              <a href="{{ route('admin.articles.show',$article) }}" class="btn btn-outline btn-sm btn-icon" title="{{ __('admin.btn_view') }}"><i class="fa-solid fa-circle-info"></i></a>
              <a href="{{ route('admin.articles.edit',$article) }}" class="btn btn-primary btn-sm btn-icon" title="{{ __('admin.btn_edit') }}"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.articles.destroy',$article) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_article') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="{{ __('admin.btn_delete') }}"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-newspaper"></i><p>{{ __('admin.empty_articles') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
  <div style="padding:16px">{{ $articles->links() }}</div>
  @endif
</div>
@endsection
