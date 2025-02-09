@extends('dashboard.layouts.app')

@section('title', __('dashboard.invoice.create'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-sm-auto ms-auto">
                <a href="{{ route('dashboard.invoices.index') }}"><button class="btn btn-light"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<form id="create-invoice-form">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{-- client --}}
                    <div class="d-flex gap-2 mb-3">
                        {{-- services --}}

                        <div class="flex-fill">
                            <label class="form-label" for="quantity">@lang('custom.quantity')</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="quantity" name="quantity" placeholder="@lang('custom.quantity')">
                        </div>
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
    <script src="{{ asset('back/js/invoices.js') }}"></script>
@endsection