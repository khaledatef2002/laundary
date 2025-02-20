<div class="kanban mt-3">
    <div class="row">
        {{-- Total invoices --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> @lang('dashboard.total_invoices')</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                {{-- $ --}}
                                <span id="total_invoices_count">{{ $statistics['total_invoices'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                <i class="bx bx-shopping-bag text-info"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- Total Earning --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                         <p class="text-uppercase fw-medium text-muted text-truncate mb-0">@lang('dashboard.total_earning')</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="total_earning_number">{{ $statistics['total_earning'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-dollar-circle text-success"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- Remaining Money --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">@lang('dashboard.remaining_money')</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="remaining_money_number">{{ $statistics['remaining_earning'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                <i class="bx bx-wallet text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        {{-- Customers --}}
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> @lang('dashboard.customers')</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                <span id="customer_count">{{ $statistics['customers'] }}</span>
                            </h4>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-user-circle text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>
</div>

<div class="chars d-flex mb-3">
    <div class="col-xl-8 col-md-7 pe-1">
        <div class="card h-100">
            <div class="card-header">
                @lang('dashboard.total_money_per_day')
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="invoices_bar_chart" style="width:100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-5 ps-2">
        <div class="card h-100">
            <div class="card-header">
                @lang('dashboard.total_invoices_by_status')
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="invoices_pi_chart" style="width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="graphs d-flex">
    <div class="col-xl-8 col-md-7 pe-1">
        <div class="card">
            <div class="card-header">
                @lang('dashboard.best_seller')
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <tbody>
                            @foreach ($statistics['invoices_with_services'] as $key => $service)
                                <tr>
                                    <td class="text-center">
                                        {{ $key + 1 }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h5 class="fs-14 my-1">
                                                    {{ $service['service_name'] }}
                                                </h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $service['total_invoices'] }}</h5>
                                        <span class="text-muted">@lang('dashboard.invoices')</span>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $service['total_quantity'] }}</h5>
                                        <span class="text-muted">@lang('dashboard.quantity')</span>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $service['total_amount'] }}</h5>
                                        <span class="text-muted">@lang('dashboard.amount')</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-5 ps-2">
        <div class="card">
            <div class="card-header">
                @lang('dashboard.top_customers')
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    @foreach ($statistics['invoices_with_clients'] as $key => $client)
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        {{ $key + 1 }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h5 class="fs-14 my-1">
                                                    {{ $client['client_name'] }}
                                                </h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $client['total_invoices'] }}</h5>
                                        <span class="text-muted">@lang('dashboard.invoices')</span>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ $client['total_amount'] }}</h5>
                                        <span class="text-muted">@lang('dashboard.amount')</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    draft_invoices = {{ $statistics['draft_invoices'] }}
    unpaid_invoices = {{ $statistics['unpaid_invoices'] }}
    partially_invoices = {{ $statistics['partially_invoices'] }}
    paid_invoices = {{ $statistics['paid_invoices'] }}
    money_per_day = @json($statistics["money_per_day"])
</script>
<script src="{{ asset('back/js/home.js') }}"></script>