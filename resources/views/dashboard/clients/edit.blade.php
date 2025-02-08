@extends('dashboard.layouts.app')

@section('title', __('dashboard.client.edit'))

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
<form id="edit-client-form" data-id="{{ $client->id }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 flex-fill">
                        <label class="form-label" for="name">@lang('custom.name')</label>
                        <input type="text" class="form-control" value="{{ $client->name }}" id="name" name="name" placeholder="@lang('custom.name')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">@lang('custom.email')</label>
                        <input type="text" class="form-control" value="{{ $client->email }}" id="email" name="email" placeholder="@lang('custom.email')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">@lang('custom.phone')</label>
                        <input type="phone" class="form-control" value="{{ $client->phone }}" id="phone" name="phone" placeholder="@lang('custom.phone')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">@lang('custom.address')</label>
                        <textarea class="form-control" id="address" name="address" placeholder="@lang('custom.address')">{{ $client->address }}</textarea>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <div class="row">
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-success w-sm">@lang('dashboard.save')</button>
        </div>
    </div>
</form>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/clients.js') }}"></script>
@endsection