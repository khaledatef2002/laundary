@extends('dashboard.layouts.app')

@section('title', __('dashboard.system_setting.edit'))

@section('content')

<form id="edit-system_setting-form" data-id="{{ $system_setting->id }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="position-relative d-inline-block auto-image-show">
                        <div class="position-absolute top-100 start-100 translate-middle">
                            <label for="logo" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                <div class="avatar-xs">
                                    <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                        <i class="ri-image-fill"></i>
                                    </div>
                                </div>
                            </label>
                            <input class="form-control d-none" name="logo" id="logo" type="file" accept="image/png, image/gif, image/jpeg">
                        </div>
                        <div class="avatar-lg">
                            <div class="avatar-title bg-light rounded">
                                <img src="{{ asset($system_setting->logo) }}" id="product-img" style="min-height: 100%;min-width: 100%;" />
                            </div>
                            <span class="text-muted text-center d-block">@lang('dashboard.logo')</span>
                        </div>
                    </div>
                    <div class="mt-3 flex-fill">
                        <label class="form-label" for="title">@lang('dashboard.title')</label>
                        <input type="text" class="form-control" value="{{ $system_setting->title }}" id="title" name="title" placeholder="@lang('dashboard.title')">
                    </div>
                    <div class="mt-3 flex-fill">
                        <label class="form-label" for="description">@lang('dashboard.description')</label>
                        <textarea class="form-control" id="description" name="description" placeholder="@lang('dashboard.description')">{{ $system_setting->description }}</textarea>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    @if (Auth::user()->hasPermissionTo('system_settings_edit'))
        <div class="row">
            <div class="text-end mb-3">
                <button type="submit" class="btn btn-success w-sm">@lang('dashboard.save')</button>
            </div>
        </div>
    @endif
</form>

@endsection

@section('custom-js')
    <script>
        const lang = {
            'delete_message': '@lang("dashboard.delete_message")',
            'update_message': '@lang("dashboard.update_message")',
        }
    </script>
    <script src="{{ asset('back/js/system_settings.js') }}"></script>
@endsection