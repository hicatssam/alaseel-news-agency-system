@extends('layouts.admin')
@section('title', __('admin.newsletter_index'))
@section('breadcrumb') {{ __('admin.nav_newsletter') }} @endsection
@section('content')
<div class="card" style="margin-bottom:16px">
  <div class="card-body" style="text-align:center">
    <div style="font-size:36px;font-weight:900;color:#c9a84c">{{ $totalActive }}</div>
    <div style="font-size:14px;color:#888">{{ __('admin.newsletter_active_subscribers') }}</div>
  </div>
</div>
<div class="card">
  <div class="card-header"><span class="card-title">{{ __('admin.nav_newsletter') }} ({{ $subscribers->total() }})</span></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>{{ __('admin.col_email') }}</th><th>{{ __('admin.col_status') }}</th><th>{{ __('admin.col_subscription_date') }}</th><th>{{ __('admin.col_actions') }}</th></tr></thead>
      <tbody>
        @forelse($subscribers as $s)
        <tr>
          <td style="font-weight:600">{{ $s->email }}</td>
          <td>@if($s->status=='active')<span class="badge badge-success">{{ __('admin.status_active') }}</span>@else<span class="badge badge-secondary">{{ __('admin.status_cancelled') }}</span>@endif</td>
          <td style="font-size:12px;color:#888">{{ $s->created_at->format('Y/m/d') }}</td>
          <td>
            <form method="POST" action="{{ route('admin.newsletter.destroy',$s) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm btn-icon"><i class="fa-solid fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="4"><div class="empty-state"><i class="fa-solid fa-bell"></i><p>{{ __('admin.empty_subscribers') }}</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($subscribers->hasPages())<div style="padding:16px">{{ $subscribers->links() }}</div>@endif
</div>
@endsection
