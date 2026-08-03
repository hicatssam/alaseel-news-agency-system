@extends('layouts.admin')
@section('title', __('admin.tags_index'))
@section('breadcrumb') {{ __('admin.nav_tags') }} @endsection
@section('content')
<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div></div>
  <a href="{{ route('admin.tags.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> {{ __('admin.tags_create') }}</a>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.tags_index') }} ({{ $tags->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_name') }}</th><th>{{ __('admin.col_slug') }}</th><th>{{ __('admin.col_articles_count') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($tags as $tag)
        <tr>
          <td style="font-weight:700">{{ $tag->name }}</td>
          <td style="font-size:12px;color:#888">{{ $tag->slug }}</td>
          <td><span class="badge badge-info">{{ $tag->articles_count }}</span></td>
          <td>@if($tag->status)<span class="badge badge-success">{{ __('admin.status_enabled') }}</span>@else<span class="badge badge-danger">{{ __('admin.status_disabled') }}</span>@endif</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.tags.edit',$tag) }}" class="btn btn-primary btn-sm btn-icon"><i class="fa-solid fa-pen"></i></a>
              <form method="POST" action="{{ route('admin.tags.destroy',$tag) }}" onsubmit="return confirm('{{ __('admin.confirm_delete_tag') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-tags"></i><p>{{ __('admin.empty_tags') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($tags->hasPages())<div style="padding:16px">{{ $tags->links() }}</div>@endif
</div>
@endsection
