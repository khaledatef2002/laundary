@extends('dashboard.layouts.app')

@section('title', __('dashboard.invoice.edit'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="g-2 d-flex align-items-center">
            <div>
                @switch($invoice->status)
                    @case(App\Enum\InvoiceStatus::DRAFT->value)
                        <span class='badge bg-dark fs-5 py-2'>@lang('dashboard.draft')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::PAID->value)
                        <span class='badge bg-success fs-5 py-2'>@lang('dashboard.paid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::UNPAID->value)
                        <span class='badge bg-secondary fs-5 py-2'>@lang('dashboard.unpaid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::PARTIALLY_PAID->value)
                        <span class='badge bg-warning fs-5 py-2'>@lang('dashboard.partially_paid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::CANCELED->value)
                        <span class='badge bg-danger fs-5 py-2'>@lang('dashboard.canceled')</span>
                        @break
                    @default
                @endswitch
            </div>
            <div class="ms-auto">
                @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value || App\Enum\InvoiceStatus::UNPAID->value)
                    <button class="btn btn-danger me-2">@lang('dashboard.cancel')</button>
                @endif
                @if ($invoice->status != App\Enum\InvoiceStatus::DRAFT->value)
                    <button class="btn btn-danger me-2">@lang('dashboard.draft')</button>
                @endif
                <a href="{{ route('dashboard.invoices.index') }}"><button class="btn btn-light"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<form id="edit-invoice-form" data-id="{{ $invoice->id }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{-- client --}}
                    <div class="mb-3">
                        <label class="form-label" for="client_id">@lang('dashboard.client')</label>
                        <select class="form-control" id="client_id" name="client_id">
                            <option value="{{ $invoice->client->id }}">{{ $invoice->client->name }}</option>
                        </select>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                            <input oninput="calculate_total()" type="number" step="0.01" min="0" value="{{ $invoice->discount }}" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')">
                        </div>
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                            <select onchange="calculate_total()" class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')">
                                <option value="{{ App\Enum\DiscountType::FIXED->value }}" {{ $invoice->discount_type == App\Enum\DiscountType::FIXED->value ? 'selected': '' }}>--@lang('dashboard.fixed')--</option>
                                <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}" {{ $invoice->discount_type == App\Enum\DiscountType::PERCENTAGE->value ? 'selected': '' }}>--@lang('dashboard.percentage')--</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="due_date">@lang('dashboard.due_date')</label>
                        <input type="text" id="due_date" name="due_date" value="{{ $invoice->due_date }}" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y", strtotime($invoice->due_date)) }}">
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
                            text: service.title,
                            price: service.price
                        };
                        })
                    };
                    },
                    cache: true
                },
                minimumInputLength: 0 // Require at least 1 character to start searching
            }).on('select2:select', function (e) {
                const data = e.params.data;
                const price =  data.price

                $("p#subtotal").attr("data-price", price)

                check_sub_total()
            });
        })

        $("input[name='quantity']").keyup(() => check_sub_total())

        function check_sub_total()
        {
            const subtotal = $("p#subtotal")
            const price = Number(subtotal.attr("data-price"))
            const quantity = Number($("input[name='quantity']").val() ?? 0)
            subtotal.text(price * quantity)
        }
    </script>
@endsection