@extends('layouts.admin')
@section('title', $stream->exists ? __('admin.live_stream_edit') : __('admin.live_stream_new'))
@section('breadcrumb')
  <a href="{{ route('admin.live-streams.index') }}">{{ __('admin.nav_live_stream') }}</a>
  <span class="sep">›</span>
  {{ $stream->exists ? __('admin.btn_edit') : __('admin.btn_create') }}
@endsection

@section('content')
<div style="max-width:700px">
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <i class="fa-solid fa-tower-broadcast" style="color:var(--gold)"></i>
        {{ $stream->exists ? __('admin.live_stream_edit') : __('admin.live_stream_new') }}
      </div>
    </div>
    <div class="card-body">
      <form action="{{ $stream->exists ? route('admin.live-streams.update', $stream) : route('admin.live-streams.store') }}"
            method="POST">
        @csrf
        @if($stream->exists) @method('PUT') @endif

        <div class="form-group">
          <label class="form-label">{{ __('admin.field_stream_title') }} *</label>
          <input type="text" name="title" class="form-control" required
                 value="{{ old('title', $stream->title) }}"
                 placeholder="{{ __('admin.field_stream_title_placeholder') }}">
        </div>

        <div class="form-group">
          <label class="form-label">{{ __('admin.field_embed_url') }} *</label>
          <input type="url" name="embed_url" class="form-control" required
                 value="{{ old('embed_url', $stream->embed_url) }}"
                 placeholder="https://www.youtube.com/embed/...">
          <div style="font-size:12px;color:#888;margin-top:5px">
            {{ __('admin.field_embed_url_hint') }}
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">{{ __('admin.field_stream_description') }}</label>
          <textarea name="description" class="form-control" rows="3"
                    placeholder="{{ __('admin.field_stream_description_placeholder') }}">{{ old('description', $stream->description) }}</textarea>
        </div>

        <div class="form-group">
          <label class="form-label">{{ __('admin.field_viewers_label') }}</label>
          <input type="text" name="viewers_label" class="form-control"
                 value="{{ old('viewers_label', $stream->viewers_label) }}"
                 placeholder="{{ __('admin.field_viewers_label_placeholder') }}">
          <div style="font-size:12px;color:#888;margin-top:5px">{{ __('admin.field_viewers_label_hint') }}</div>
        </div>

        <div class="form-group" style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:14px">
          <label class="form-check" style="gap:10px">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active_check"
                   {{ old('is_active', $stream->is_active) ? 'checked' : '' }}>
            <div>
              <span style="font-weight:700;color:var(--dark)">{{ __('admin.field_activate_stream') }}</span>
              <div style="font-size:12px;color:#666;margin-top:2px">{{ __('admin.field_activate_stream_hint') }}</div>
            </div>
          </label>
        </div>

        {{-- Notify subscribers — only shown when "activate" is checked and stream is not yet active --}}
        <div id="notify_row"
             style="display:{{ (old('is_active', $stream->is_active) && $stream->exists) ? 'none' : (old('is_active') ? 'block' : 'none') }};
                    background:#e8f5e9;border:1px solid #a5d6a7;border-radius:8px;padding:14px;margin-top:-8px">
          <label class="form-check" style="gap:10px">
            <input type="hidden" name="notify_subscribers" value="0">
            <input type="checkbox" name="notify_subscribers" value="1"
                   {{ old('notify_subscribers', '1') == '1' ? 'checked' : '' }}>
            <div>
              <span style="font-weight:700;color:var(--dark)">{{ __('admin.field_notify_subscribers') }}</span>
              <div style="font-size:12px;color:#555;margin-top:2px">{{ __('admin.field_notify_subscribers_hint') }}</div>
            </div>
          </label>
        </div>

        <script>
          (function () {
            var activateCheck = document.getElementById('is_active_check');
            var notifyRow     = document.getElementById('notify_row');
            var wasActive     = {{ $stream->exists && $stream->is_active ? 'true' : 'false' }};

            function syncNotifyRow() {
              // Show the notify row only when activating a stream that was not already active
              notifyRow.style.display = (activateCheck.checked && !wasActive) ? 'block' : 'none';
            }

            activateCheck.addEventListener('change', syncNotifyRow);
            syncNotifyRow();
          })();
        </script>

        <div style="display:flex;gap:10px;margin-top:24px">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i>
            {{ $stream->exists ? __('admin.btn_update') : __('admin.btn_create') }}
          </button>
          <a href="{{ route('admin.live-streams.index') }}" class="btn btn-outline">{{ __('admin.btn_cancel') }}</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
