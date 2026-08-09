@extends('layouts.admin')

@section('title', 'إضافة عضو فريق')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:20px;flex-wrap:wrap">
    <div>
        <h2 style="margin:0;color:#1a1a2e;font-size:22px;font-weight:800">
            <i class="fa-solid fa-user-plus" style="color:#c9a84c"></i>
            إضافة عضو فريق
        </h2>

        <p style="margin:5px 0 0;color:#888;font-size:13px">
            سيظهر العضو تلقائيًا في صفحة من نحن عند تفعيل حالته.
        </p>
    </div>

    <a href="{{ route('admin.team-members.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-right"></i>
        العودة للقائمة
    </a>
</div>

@if($errors->any())
    <div style="padding:14px 18px;margin-bottom:20px;border:1px solid #f3c2c2;border-radius:8px;background:#fdf0f0;color:#b83232">
        <div style="font-weight:800;margin-bottom:7px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            يرجى مراجعة الأخطاء التالية:
        </div>

        <ul style="margin:0;padding-right:20px;font-size:13px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('admin.team-members.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    <div class="team-form-grid">
        <div class="card">
            <div class="card-header">
                <span class="card-title">
                    <i class="fa-solid fa-address-card" style="color:#c9a84c"></i>
                    بيانات العضو
                </span>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="name">
                        اسم عضو الفريق
                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        maxlength="255"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="job_title">
                        المسمى الوظيفي
                        <span style="color:#e74c3c">*</span>
                    </label>

                    <input
                        type="text"
                        id="job_title"
                        name="job_title"
                        class="form-control @error('job_title') is-invalid @enderror"
                        value="{{ old('job_title') }}"
                        maxlength="255"
                        placeholder="مثال: رئيس التحرير"
                        required
                    >

                    @error('job_title')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label for="display_order">ترتيب الظهور</label>

                    <input
                        type="number"
                        id="display_order"
                        name="display_order"
                        class="form-control @error('display_order') is-invalid @enderror"
                        value="{{ old('display_order', 0) }}"
                        min="0"
                        max="9999"
                    >

                    <small class="field-help">
                        الرقم الأصغر يظهر أولًا في صفحة من نحن.
                    </small>

                    @error('display_order')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-camera" style="color:#c9a84c"></i>
                        صورة العضو
                    </span>
                </div>

                <div class="card-body">
                    <div class="member-image-preview" id="member-image-preview">
                        <div class="member-preview-placeholder">
                            <i class="fa-solid fa-user"></i>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:20px;margin-bottom:0">
                        <label for="image">
                            اختيار الصورة
                            <span style="color:#e74c3c">*</span>
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control @error('image') is-invalid @enderror"
                            accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
                            required
                        >

                        <small class="field-help">
                            يفضل استخدام صورة مربعة وواضحة لأن الصورة ستظهر بشكل دائري.
                        </small>

                        @error('image')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <i class="fa-solid fa-toggle-on" style="color:#c9a84c"></i>
                        حالة العضو
                    </span>
                </div>

                <div class="card-body">
                    <label class="status-switch-row" for="is_active">
                        <div>
                            <div style="font-weight:800;color:#1a1a2e;font-size:13px">
                                عضو نشط
                            </div>

                            <div style="font-size:11px;color:#999;margin-top:3px">
                                يظهر العضو في صفحة من نحن.
                            </div>
                        </div>

                        <span class="switch-control">
                            <input type="hidden" name="is_active" value="0">

                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                @checked((bool) old('is_active', true))
                            >

                            <span class="switch-slider"></span>
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px">
                <i class="fa-solid fa-floppy-disk"></i>
                حفظ عضو الفريق
            </button>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .team-form-grid {
        display:grid;
        grid-template-columns:minmax(0,1.35fr) minmax(300px,.65fr);
        gap:20px;
        align-items:start;
    }

    .field-error {
        margin-top:6px;
        color:#e74c3c;
        font-size:11.5px;
        font-weight:700;
    }

    .field-help {
        display:block;
        margin-top:7px;
        color:#999;
        font-size:11px;
        line-height:1.7;
    }

    .is-invalid {
        border-color:#e74c3c !important;
    }

    .member-image-preview {
        display:flex;
        width:190px;
        height:190px;
        align-items:center;
        justify-content:center;
        margin:0 auto;
        overflow:hidden;
        border:4px solid #fff;
        border-radius:50%;
        background:#f7f7f7;
        box-shadow:0 0 0 3px rgba(201,168,76,.55),0 12px 30px rgba(0,0,0,.14);
    }

    .member-image-preview img {
        display:block;
        width:100%;
        height:100%;
        object-fit:cover;
    }

    .member-preview-placeholder {
        display:flex;
        width:100%;
        height:100%;
        align-items:center;
        justify-content:center;
        color:#c9a84c;
        background:linear-gradient(135deg,#faf7ef,#f0e7cf);
        font-size:65px;
    }

    .status-switch-row {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        margin:0;
        cursor:pointer;
    }

    .switch-control {
        position:relative;
        display:inline-block;
        width:48px;
        height:26px;
        flex-shrink:0;
    }

    .switch-control input[type="checkbox"] {
        width:0;
        height:0;
        opacity:0;
    }

    .switch-slider {
        position:absolute;
        inset:0;
        border-radius:50px;
        background:#d6d6d6;
        transition:.2s;
    }

    .switch-slider::before {
        position:absolute;
        right:3px;
        bottom:3px;
        width:20px;
        height:20px;
        border-radius:50%;
        background:#fff;
        box-shadow:0 2px 6px rgba(0,0,0,.2);
        content:"";
        transition:.2s;
    }

    .switch-control input:checked + .switch-slider {
        background:#27ae60;
    }

    .switch-control input:checked + .switch-slider::before {
        transform:translateX(-22px);
    }

    @media(max-width:850px) {
        .team-form-grid {
            grid-template-columns:1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('image')?.addEventListener('change', function () {
        const file = this.files?.[0];
        const preview = document.getElementById('member-image-preview');

        if (!file || !file.type.startsWith('image/') || !preview) {
            return;
        }

        const imageUrl = URL.createObjectURL(file);

        preview.innerHTML = '';

        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = 'معاينة صورة عضو الفريق';

        image.onload = () => URL.revokeObjectURL(imageUrl);

        preview.appendChild(image);
    });
</script>
@endpush