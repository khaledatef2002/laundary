@extends('dashboard.layouts.app')

@section('title', __('dashboard.service.create'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-sm-auto ms-auto">
                <a href="{{ route('dashboard.services.index') }}"><button class="btn btn-light"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<form id="create-service-form">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @foreach (LaravelLocalization::getSupportedLocales() as $key => $lang)
                        <div class="mb-3">
                            <label class="form-label" for="title:{{ $key }}">@lang('dashboard.' . $key . '.title')</label>
                        <input type="text" class="form-control" id="title:{{ $key }}" name="{{ $key }}[title]" placeholder="@lang('dashboard.' . $key . '.title')">
                        </div>
                    @endforeach

                    <div class="mb-3">
                        <label class="form-label" for="price">@lang('dashboard.price')</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" placeholder="@lang('dashboard.price')">
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
    <script src="{{ asset('back/js/services.js') }}"></script>
@endsection