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
                    <div class="mb-3 d-flex gap-2">
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount">@lang('dashboard.discount')</label>
                            <input oninput="calculate_total()" type="number" step="0.01" min="0" value="0" class="form-control" id="discount" name="discount" placeholder="@lang('dashboard.discount')">
                        </div>
                        <div class="flex-fill mb-3">
                            <label class="form-label" for="discount_type">@lang('dashboard.discount_type')</label>
                            <select onchange="calculate_total()" class="form-control" id="discount_type" name="discount_type" placeholder="@lang('dashboard.discount_type')">
                                <option value="{{ App\Enum\DiscountType::FIXED->value }}">--@lang('dashboard.fixed')--</option>
                                <option value="{{ App\Enum\DiscountType::PERCENTAGE->value }}">--@lang('dashboard.percentage')--</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="due_date">@lang('dashboard.due_date')</label>
                        <input type="text" id="due_date" name="due_date" class="form-control" data-provider="flatpickr" data-date-format="M d, Y" data-deafult-date="{{ date("M d, Y") }}">
                    </div>
                </div>
                <div class="px-3 my-4">
                    <button class="btn btn-success ms-auto d-block" id="openAddServiceModal" type="button">Add +</button>
                    <table id="services_table" class="w-100">
                        <thead class="bg-dark text-white text-center">
                            <tr>
                                <th class="py-3">Service</th>
                                <th class="py-3">Price</th>
                                <th class="py-3">Quantity</th>
                                <th class="py-3">Discount</th>
                                <th class="py-3">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-center"></tbody>
                    </table>
                </div>
                <p class="d-block ms-auto me-3">
                    <span class="fw-bold">Total: </span> <span id="total">0</span>
                </p>
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

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/invoices.js') }}"></script>
    <script>
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
                minimumInputLength: 0
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