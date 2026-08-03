@extends('layouts.admin')
@section('title', __('admin.comments_index'))
@section('breadcrumb') {{ __('admin.nav_comments') }} @endsection
@section('content')
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
  @foreach([['all', __('admin.filter_all')],['pending', __('admin.status_pending')],['approved', __('admin.status_approved')],['rejected', __('admin.status_rejected')]] as [$v,$l])
  <a href="{{ route('admin.comments.index',array_merge(request()->except('status','page'),['status'=>$v=='all'?null:$v])) }}"
     class="btn btn-sm {{ request('status')==$v||($v=='all'&&!request('status'))?'btn-primary':'btn-outline' }}">{{ $l }}</a>
  @endforeach
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.nav_comments') }} ({{ $comments->total() }})</span></div>
  <div class="card-body" style="padding:0">
    @forelse($comments as $c)
    <div style="padding:16px;border-bottom:1px solid #f0f0f0">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
        <div>
          <span style="font-weight:700;font-size:13px">{{ $c->name ?? $c->user?->name ?? __('admin.label_unknown_commenter') }}</span>
          @if($c->email)<span style="font-size:12px;color:#888;margin-right:6px">({{ $c->email }})</span>@endif
        </div>
        <div style="display:flex;align-items:center;gap:6px">
          @if($c->status=='pending')<span class="badge badge-warning">{{ __('admin.status_pending') }}</span>
          @elseif($c->status=='approved')<span class="badge badge-success">{{ __('admin.status_approved') }}</span>
          @else<span class="badge badge-danger">{{ __('admin.status_rejected') }}</span>@endif
          <span style="font-size:11px;color:#aaa">{{ $c->created_at->diffForHumans() }}</span>
        </div>
      </div>
      <p style="font-size:13.5px;color:#444;margin-bottom:8px">{{ $c->content }}</p>
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <a href="{{ route('admin.articles.show',$c->article) }}" style="font-size:12px;color:#c9a84c;text-decoration:none">
          <i class="fa-solid fa-newspaper"></i> {{ Str::limit($c->article?->title??'—',50) }}
        </a>
        <div style="display:flex;gap:6px">
          @if($c->status!='approved')
          <form method="POST" action="{{ route('admin.comments.status',$c) }}">
            @csrf @method('PATCH') <input type="hidden" name="status" value="approved">
            <button class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> {{ __('admin.btn_approve') }}</button>
          </form>
          @endif
          @if($c->status!='rejected')
          <form method="POST" action="{{ route('admin.comments.status',$c) }}">
            @csrf @method('PATCH') <input type="hidden" name="status" value="rejected">
            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i> {{ __('admin.btn_reject') }}</button>
          </form>
          @endif
          <form method="POST" action="{{ route('admin.comments.destroy',$c) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="empty-state" style="padding:60px"><i class="fa-solid fa-comments"></i><p>{{ __('admin.empty_comments') }}</p></div>
    @endforelse
  </div>
  @if($comments->hasPages())<div style="padding:16px">{{ $comments->links() }}</div>@endif
</div>
@endsection
