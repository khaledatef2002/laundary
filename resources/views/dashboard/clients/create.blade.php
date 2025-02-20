@extends('dashboard.layouts.app')

@section('title', __('dashboard.client.create'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-sm-auto ms-auto">
                <a href="{{ route('dashboard.clients.index') }}"><button class="btn btn-light"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<form id="create-client-form">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="name">@lang('dashboard.name')</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="@lang('dashboard.name')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">@lang('dashboard.email')</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="@lang('dashboard.email')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">@lang('dashboard.phone')</label>
                        <input type="phone" class="form-control" id="phone" name="phone" placeholder="@lang('dashboard.phone')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">@lang('dashboard.address')</label>
                        <textarea class="form-control" id="address" name="address" placeholder="@lang('dashboard.address')"></textarea>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <div class="row">
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-success w-sm">@lang('dashboard.create')</button>
        </div>
    </div>
</form>

@endsection

@section('custom-js')
    <script>
        const lang = {
            'delete_message': '@lang("dashboard.delete_message")',
            'update_message': '@lang("dashboard.update_message")',
        }
    </script>
    <script src="{{ asset('back/js/clients.js') }}"></script>
@endsection