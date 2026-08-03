@extends('layouts.admin')
@section('title', __('admin.contact_index'))
@section('breadcrumb') {{ __('admin.nav_contact_messages') }} @endsection
@section('content')
<div style="display:flex;gap:8px;margin-bottom:20px">
  @foreach([['all', __('admin.filter_all')],['new', __('admin.contact_filter_new')],['read', __('admin.contact_filter_read')],['replied', __('admin.contact_filter_replied')]] as [$v,$l])
  <a href="{{ route('admin.contact.index',array_merge(request()->except('status','page'),['status'=>$v=='all'?null:$v])) }}"
     class="btn btn-sm {{ request('status')==$v||($v=='all'&&!request('status'))?'btn-primary':'btn-outline' }}">{{ $l }}</a>
  @endforeach
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.nav_contact_messages') }} ({{ $messages->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_sender') }}</th><th>{{ __('admin.col_subject') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_date') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($messages as $m)
        <tr style="{{ $m->status=='new'?'font-weight:700':'' }}">
          <td>
            <div style="font-weight:600">{{ $m->name }}</div>
            <div style="font-size:12px;color:#888">{{ $m->email }}</div>
          </td>
          <td style="font-size:13px">{{ $m->subject ?? Str::limit($m->message,40) }}</td>
          <td>
            @if($m->status=='new')<span class="badge badge-danger">{{ __('admin.status_new') }}</span>
            @elseif($m->status=='read')<span class="badge badge-info">{{ __('admin.status_read') }}</span>
            @else<span class="badge badge-success">{{ __('admin.status_replied') }}</span>@endif
          </td>
          <td style="font-size:12px;color:#888">{{ $m->created_at->format('Y/m/d H:i') }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <a href="{{ route('admin.contact.show',$m) }}" class="btn btn-outline btn-sm btn-icon"><i class="fa-solid fa-eye"></i></a>
              <form method="POST" action="{{ route('admin.contact.destroy',$m) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-envelope"></i><p>{{ __('admin.empty_messages') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($messages->hasPages())<div style="padding:16px">{{ $messages->links() }}</div>@endif
</div>
@endsection
