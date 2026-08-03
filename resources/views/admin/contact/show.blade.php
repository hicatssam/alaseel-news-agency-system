@extends('layouts.admin')
@section('title', __('admin.contact_show'))
@section('breadcrumb') <a href="{{ route('admin.contact.index') }}">{{ __('admin.nav_contact_messages') }}</a> <span class="sep">›</span> {{ __('admin.btn_view') }} @endsection
@section('content')
<div style="max-width:700px">
<div class="card">
  <div class="card-header">
    <span class="card-title">{{ __('admin.contact_message_from') }} {{ $contactMessage->name }}</span>
    @if($contactMessage->status=='new')<span class="badge badge-danger">{{ __('admin.status_new') }}</span>
    @elseif($contactMessage->status=='read')<span class="badge badge-info">{{ __('admin.status_read') }}</span>
    @else<span class="badge badge-success">{{ __('admin.status_replied') }}</span>@endif
  </div>
  <div class="card-body">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
      <div><strong>{{ __('admin.label_name') }}:</strong> {{ $contactMessage->name }}</div>
      <div><strong>{{ __('admin.label_email') }}:</strong> <a href="mailto:{{ $contactMessage->email }}" style="color:#c9a84c">{{ $contactMessage->email }}</a></div>
      <div><strong>{{ __('admin.label_phone') }}:</strong> {{ $contactMessage->phone ?? '—' }}</div>
      <div><strong>{{ __('admin.label_date') }}:</strong> {{ $contactMessage->created_at->format('Y/m/d H:i') }}</div>
    </div>
    @if($contactMessage->subject)
    <div style="margin-bottom:12px"><strong>{{ __('admin.label_subject') }}:</strong> {{ $contactMessage->subject }}</div>
    @endif
    <div style="background:#f8f9fa;padding:16px;border-radius:8px;border-right:3px solid #c9a84c;font-size:14px;line-height:1.8;color:#333">
      {{ $contactMessage->message }}
    </div>
    <div style="margin-top:16px;display:flex;gap:8px">
      <a href="mailto:{{ $contactMessage->email }}" class="btn btn-primary"><i class="fa-solid fa-reply"></i> {{ __('admin.btn_reply_email') }}</a>
      <a href="{{ route('admin.contact.index') }}" class="btn btn-outline">{{ __('admin.btn_back_to_list') }}</a>
      <form method="POST" action="{{ route('admin.contact.destroy',$contactMessage) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
        @csrf @method('DELETE')
        <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> {{ __('admin.btn_delete') }}</button>
      </form>
    </div>
  </div>
</div>
</div>
@endsection
