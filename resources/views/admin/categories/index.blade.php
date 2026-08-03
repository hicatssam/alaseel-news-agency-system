@extends('layouts.admin')
@section('title', __('admin.categories_index'))
@section('breadcrumb') {{ __('admin.nav_categories') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.categories_create') }}</a>
</div>
<div class="filter-bar">
  <form method="GET" style="display:flex;gap:10px;flex:1">
    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.placeholder_search_categories') }}" value="{{ request('search') }}" style="max-width:280px">
    <button class="btn btn-secondary"><i class="fa-solid fa-search"></i> {{ __('admin.btn_search') }}</button>
  </form>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.categories_index') }} ({{ $categories->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>#</th><th>{{ __('admin.col_name') }}</th><th>{{ __('admin.col_slug') }}</th><th>{{ __('admin.col_articles_count') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_order') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($categories as $cat)
        <tr>
          <td style="color:#999;font-size:12px">{{ $cat->id }}</td>
          <td style="font-weight:700;color:#1a1a2e">{{ $cat->name }}</td>
          <td style="font-size:12px;color:#888">{{ $cat->slug }}</td>
          <td><span class="badge badge-info">{{ $cat->articles_count }}</span></td>
          <td>
            @if($cat->status)
            <span class="badge badge-success">{{ __('admin.status_enabled') }}</span>
            @else
            <span class="badge badge-danger">{{ __('admin.status_disabled') }}</span>
            @endif
          </td>
          <td style="color:#888">{{ $cat->sort_order }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('categories.show',$cat->slug) }}" target="_blank" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-eye"></i></a>
              <a href="{{ route('admin.categories.edit',$cat) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.categories.destroy',$cat) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_category') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-folder"></i><p>{{ __('admin.empty_categories') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($categories->hasPages())<div style="padding:16px">{{ $categories->links() }}</div>@endif
</div>
@endsection
