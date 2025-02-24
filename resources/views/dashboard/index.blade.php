@extends('dashboard.layouts.app')

@section('title', 'Dashboard')

@section('content')

@if (Auth::user()->hasPermissionTo('kpis_show'))
    <div class="header d-flex justify-content-end">
        <div class="d-flex">
            <input type="text" id="date-range-input" class="form-control border-0 active" style="width:200px !important" readonly="readonly">
            <div class="input-group-text bg-primary border-primary text-white">
                <i class="ri-calendar-2-line"></i>
            </div>
        </div>
    </div>
    <div id="kpis_render">

    </div>
@else
<h2>@lang('dashboard.welcome_back')</h2>
@endif
        
@endsection

@section('custom-js')
<script src="{{ asset('back/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.js"></script>
@if (Auth::user()->hasPermissionTo('kpis_show'))
    <script>
        let draft_invoices = null
        let unpaid_invoices = null
        let partially_invoices = null
        let paid_invoices = null
        let money_per_day = null
        let invoice_pi_chart = null
        let invoice_bar_chart = null

        const lang = {
            'draft': "@lang('dashboard.draft')",
            'unpaid': "@lang('dashboard.unpaid')",
            'partially_paid':" @lang('dashboard.partially_paid')",
            'paid': "@lang('dashboard.paid')",
            'count': "@lang('dashboard.count')",
        }

        let date = new Date(), y = date.getFullYear(), m = date.getMonth();
        const startDate = formatDate(new Date(y, m, 1))
        const endDate = formatDate(new Date(y, m + 1, 0));

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        } 

        $("#date-range-input").flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [startDate, endDate],
            onValueUpdate: (selectedDates, dateStr, instance) =>
            {
                const dateArr = dateStr.split(" ")
                const date_from = dateArr[0]
                const date_to = dateArr[2]

                if(date_from && date_to)
                    render_kpis(date_from, date_to)
            }
        })

        render_kpis(startDate, endDate)

        function render_kpis(date_from, date_to)
        {
            $.ajax({
                url: "/dashboard/load_kpis",
                method: 'GET',
                data: {
                    date_from, date_to  
                },
                processData: true,
                success: function(response) {
                    $("#kpis_render").html(response)
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var firstKey = Object.keys(errors)[0];
                    Swal.fire({
                        text: errors[firstKey][0],
                        icon: "error"
                    });
                    submit_button.prop("disabled", false)
                }
            });    
        }
    </script>
@endif
@endsection