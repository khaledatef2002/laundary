@extends('dashboard.layouts.app')

@section('title', __('dashboard.invoices'))

@section('content')

@if (Auth::user()->hasPermissionTo('invoices_create'))
    <div class="card">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-auto ms-auto">
                    <a href="{{ route('dashboard.invoices.create') }}"><button class="btn btn-success"><i class="ri-add-fill me-1 align-bottom"></i> @lang('dashboard.invoices.add')</button></a>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
    </div>
@endif
<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="dataTables">
            <thead>
                <tr class="table-dark">
                    <th>@lang('dashboard.invoice_number')</th>
                    <th>@lang('dashboard.client')</th>
                    <th>@lang('dashboard.quantity')</th>
                    <th>@lang('dashboard.total')</th>
                    <th>@lang('dashboard.status')</th>
                    <th>@lang('dashboard.due_date')</th>
                    <th>@lang('dashboard.action')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/invoices.js') }}"></script>
    <script>
        var table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.invoices.index') }}",
                columns: [
                            { data: 'invoice_number', name: 'invoice_number' },
                            { data: 'client', name: 'client' },
                            { data: 'quantity', name: 'quantity' },
                            { data: 'total', name: 'total' }, 
                            { data: 'status', name: 'status' },
                            { data: 'due_date', name: 'due_date' },
                            { data: 'action', name: 'action'}
                        ]
            });
        });
    </script>
@endsection