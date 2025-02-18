@extends('dashboard.layouts.app')

@section('title', __('dashboard.invoice.edit'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="g-2 d-flex align-items-center">
            <div>
                @php($disabled = $invoice->status != App\Enum\InvoiceStatus::DRAFT->value ? "disabled" : "")
                @switch($invoice->status)
                    @case(App\Enum\InvoiceStatus::DRAFT->value)
                        <span class='badge bg-dark fs-5 py-2' id="invoice_status">@lang('dashboard.draft')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::PAID->value)
                        <span class='badge bg-success fs-5 py-2' id="invoice_status">@lang('dashboard.paid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::UNPAID->value)
                        <span class='badge bg-secondary fs-5 py-2' id="invoice_status">@lang('dashboard.unpaid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::PARTIALLY_PAID->value)
                        <span class='badge bg-warning fs-5 py-2' id="invoice_status">@lang('dashboard.partially_paid')</span>
                        @break
                    @case(App\Enum\InvoiceStatus::CANCELED->value)
                        <span class='badge bg-danger fs-5 py-2' id="invoice_status">@lang('dashboard.canceled')</span>
                        @break
                    @default
                @endswitch
                @if (strtotime($invoice->due_date) < strtotime(now()) && $invoice->status != App\Enum\InvoiceStatus::PAID->value && $invoice->status != App\Enum\InvoiceStatus::DRAFT->value && $invoice->status != App\Enum\InvoiceStatus::CANCELED->value)
                    <span class='badge bg-danger fs-5 py-2' id="invoice_overtime">@lang('dashboard.overtime')</span>  
                @else
                    <span class='badge bg-danger fs-5 py-2 d-none' id="invoice_overtime">@lang('dashboard.overtime')</span>  
                @endif
            </div>
            <div class="ms-auto d-flex align-items-center">
                <i class="ri-file-pdf-2-line me-2 fs-2" role="button"></i>
                @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value)
                    <button class="btn btn-success me-2" id="confirm_button">@lang('dashboard.confirm')</button>
                @endif
                @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value || $invoice->status == App\Enum\InvoiceStatus::UNPAID->value)
                    <button class="btn btn-danger me-2" id="cancel_button">@lang('dashboard.cancel')</button>
                @endif
                @if ($invoice->status != App\Enum\InvoiceStatus::DRAFT->value)
                    <button class="btn btn-dark me-2" id="draft_button">@lang('dashboard.draft')</button>
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
                        <select class="form-control" id="client_id" name="client_id" {{ $disabled }}>
                            <option value="{{ $invoice->client->id }}">{{ $invoice->client->name }}</option>
                        </select>
                    </div>
                    <div class="mb-3 d-flex gap-2">
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                            <input oninput="calculate_total()" type="number" step="0.01" min="0" value="{{ $invoice->discount }}" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')" {{ $disabled }}>
                        </div>
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                            <select onchange="calculate_total()" class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')" {{ $disabled }}>
                                <option value="{{ App\Enum\DiscountType::FIXED->value }}" {{ $invoice->discount_type == App\Enum\DiscountType::FIXED->value ? 'selected': '' }}>--@lang('dashboard.fixed')--</option>
                                <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}" {{ $invoice->discount_type == App\Enum\DiscountType::PERCENTAGE->value ? 'selected': '' }}>--@lang('dashboard.percentage')--</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="due_date">@lang('dashboard.due_date')</label>
                        <input type="text" id="due_date" name="due_date" value="{{ $invoice->due_date }}" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y", strtotime($invoice->due_date)) }}" {{ $disabled }}>
                    </div>
                </div>
                <div class="col-12 px-3 mt-2">
                    <div class="step-arrow-nav mb-3">

                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="invoice-services-tab" data-bs-toggle="pill" data-bs-target="#invoice-services" type="button" role="tab" aria-controls="invoice-services" aria-selected="true">Services</button>
                            </li>
                            @if (!in_array($invoice->status, [App\Enum\InvoiceStatus::DRAFT->value, App\Enum\InvoiceStatus::CANCELED->value]))
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="invoice-payments-tab" data-bs-toggle="pill" data-bs-target="#invoice-payments" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="false">Payments</button>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="invoice-services" role="tabpanel" aria-labelledby="invoice-services-tab">
                            <div class="mb-4">
                                @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value)
                                    <button class="btn btn-success ms-auto d-block mb-1" id="openAddServiceModal" type="button">Add +</button>
                                @endif
                                <table id="services_table" class="w-100">
                                    <thead class="bg-dark text-white text-center">
                                        <tr>
                                            <th class="py-3">Service</th>
                                            <th class="py-3">Price</th>
                                            <th class="py-3">Quantity</th>
                                            <th class="py-3">Discount</th>
                                            <th class="py-3">Subtotal</th>
                                            @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value)
                                                <th></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($invoice->services as $service)
                                            <tr>
                                                <td>{{ $service->service->title }}</td>
                                                <td>{{ $service->price }}</td>
                                                <td>{{ $service->quantity }}</td>
                                                <td>{{ $service->discount_amount }}</td>
                                                <td>{{ $service->total_amount }}</td>
                                                @if ($invoice->status == App\Enum\InvoiceStatus::DRAFT->value)
                                                    <td>
                                                        <div class="d-flex gap-3 px-4">
                                                            <i class="ri-edit-box-line text-info fs-2 edit_service" data-invoice-service-id="{{ $service->id }}" data-service-id="{{ $service->service->id }}" data-discount="{{ $service->discount }}" data-discount-type="{{ $service->discount_type }}" role="button"></i>
                                                            <i class="ri-delete-bin-line text-danger fs-2 remove_service" role="button"></i>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if (!in_array($invoice->status, [App\Enum\InvoiceStatus::DRAFT->value, App\Enum\InvoiceStatus::CANCELED->value]))
                            <div class="tab-pane fade mb-4" id="invoice-payments" role="tabpanel" aria-labelledby="invoice-payments-tab">
                                <button class="btn btn-success ms-auto d-block mb-1" id="openAddPaymentModal" type="button">Add +</button>
                                
                                <table id="payment_table" class="w-100">
                                    <thead class="bg-dark text-white text-center">
                                        <tr>
                                            <th class="py-3">Amount</th>
                                            <th class="py-3">date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($invoice->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->amount }}</td>
                                                <td>{{ date("M d, Y", strtotime($payment->date)) }}</td>
                                                <td>
                                                    <div class="d-flex gap-3 px-4">
                                                        <i class="ri-edit-box-line text-info fs-2 edit_payment" data-invoice-payment-id="{{ $payment->id }}" role="button"></i>
                                                        <i class="ri-delete-bin-line text-danger fs-2 remove_payment" role="button"></i>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <!-- end tab content -->
                    <p class="text-end me-3">
                        <span class="fw-bold">Total: </span> <span id="total">{{ $invoice->total_amount }}</span>
                    </p>
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

<div class="modal" id="AddServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex-fill mb-3">
                    <label class="form-label" for="service_id">@lang('dashboard.service')</label>
                    <select class="form-control" id="service_id" name="service_id">
                        <option></option>
                    </select>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="quantity">@lang('dashboard.quantity')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="1" min="1" value="1" class="form-control" id="quantity" name="quantity" placeholder="@lang('dashboard.quantity')">
                    </div>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="price">@lang('dashboard.price')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" min="1" value="0" class="form-control" id="price" name="price" placeholder="@lang('dashboard.price')">
                    </div>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                    <input type="number" step="0.01" min="0" value="0" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')">
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                    <select class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')">
                        <option value="{{ App\Enum\DiscountType::FIXED->value }}">--@lang('dashboard.fixed')--</option>
                        <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}">--@lang('dashboard.percentage')--</option>
                    </select>
                </div>
                <p class="mb-0 d-flex align-items-center fs-6">
                    <span class="fw-bold">Discount:</span> &nbsp;<span id="discount">0</span>
                </p>
                <p class="mb-0 d-flex align-items-center fs-6">
                    <span class="fw-bold">Subtotal:</span> &nbsp;<span id="subtotal">0</span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary save-add-new">Save & Add New</button>
                <button type="button" class="btn btn-primary save">Save</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="EditServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex-fill mb-3">
                    <label class="form-label" for="service_id">@lang('dashboard.service')</label>
                    <select class="form-control" id="service_id" name="service_id">
                        <option></option>
                    </select>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="quantity">@lang('dashboard.quantity')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="1" min="1" value="1" class="form-control" id="quantity" name="quantity" placeholder="@lang('dashboard.quantity')">
                    </div>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="price">@lang('dashboard.price')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" min="1" value="0" class="form-control" id="price" name="price" placeholder="@lang('dashboard.price')">
                    </div>
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                    <input type="number" step="0.01" min="0" value="0" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')">
                </div>
                <div class="flex-fill mb-3">
                    <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                    <select class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')">
                        <option value="{{ App\Enum\DiscountType::FIXED->value }}">--@lang('dashboard.fixed')--</option>
                        <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}">--@lang('dashboard.percentage')--</option>
                    </select>
                </div>
                <p class="mb-0 d-flex align-items-center fs-6">
                    <span class="fw-bold">Discount:</span> &nbsp;<span id="discount">0</span>
                </p>
                <p class="mb-0 d-flex align-items-center fs-6">
                    <span class="fw-bold">Subtotal:</span> &nbsp;<span id="subtotal">0</span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary save">Save</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="AddPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex-fill mb-3">
                    <label class="form-label" for="amount">@lang('dashboard.payment-amount')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" min="1" value="0" class="form-control" id="amount" name="amount" placeholder="@lang('dashboard.payment-amount')">
                    </div>
                </div>
                <div>
                    <label class="form-label" for="date">@lang('dashboard.payment_date')</label>
                    <input type="text" id="date" name="date" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y") }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary save">Save</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="EditPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="flex-fill mb-3">
                    <label class="form-label" for="amount">@lang('dashboard.payment-amount')</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" min="1" value="0" class="form-control" id="amount" name="amount" placeholder="@lang('dashboard.payment-amount')">
                    </div>
                </div>
                <div>
                    <label class="form-label" for="date">@lang('dashboard.payment_date')</label>
                    <input type="text" id="date" name="date" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y") }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary save">Save</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
    <script src="{{ asset('back/js/invoices.js') }}"></script>
    <script>
        const invoice_status = "{{ $invoice->status }}"

        const service_table = $("#services_table").DataTable({
            lengthChange: false,
            searching: false,
            info: false,
            columnDefs: [
                {orderable: false, targets: -1}
            ]
        })

        const service_select_common_data = {
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
            minimumInputLength: 0,
        }

        const payment_table = $("#payment_table").DataTable({
            lengthChange: false,
            searching: false,
            info: false,
            columnDefs: [
                {orderable: false, targets: -1}
            ]
        })

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
        })
    </script>
@endsection