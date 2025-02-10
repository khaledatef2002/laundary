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
                    <div class="mb-3">
                        <label class="form-label" for="client_id">@lang('dashboard.client')</label>
                        <select class="form-control" id="client_id" name="client_id">
                            <option></option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        {{-- services --}}
                        <div class="flex-fill">
                            <label class="form-label" for="service_id">@lang('dashboard.service')</label>
                            <select class="form-control" id="service_id" name="service_id">
                                <option></option>
                            </select>
                        </div>
                        <div class="flex-fill">
                            <label class="form-label" for="quantity">@lang('dashboard.quantity')</label>
                            <div class="d-flex gap-2">
                                <input type="number" step="1" min="0" class="form-control" id="quantity" name="quantity" placeholder="@lang('dashboard.quantity')">
                                <p class="mb-0 d-flex align-items-center fw-bold fs-4">50</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-fill">
                            <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')">
                        </div>
                        <div class="flex-fill">
                            <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                            <select class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')">
                                <option value="{{ App\Enum\DiscountType::FIXED->value }}">--@lang('dashboard.fixed')--</option>
                                <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}">--@lang('dashboard.subtotal')--</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="due_date">@lang('dashboard.due_date')</label>
                        <input type="text" id="due_date" name="due_date" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y") }}">
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
    <script>
        $(document).ready(function() {
            $('select[name="client_id"]').select2({
                placeholder: "@lang('dashboard.select.choose-option')",
                ajax: {
                    url: '{{ route("dashboard.select2.clients") }}', // Route to fetch users
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // Search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(client) {
                                return {
                                    id: client.id,
                                    text: client.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // Require at least 1 character to start searching
            });
            $('select[name="service_id"]').select2({
                placeholder: "@lang('dashboard.select.choose-option')",
                ajax: {
                    url: '{{ route("dashboard.select2.services") }}', // Route to fetch users
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // Search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(service) {
                                return {
                                    id: service.id,
                                    text: service.title
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // Require at least 1 character to start searching
            });
        })
    </script>
@endsection