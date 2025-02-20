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
        const lang = {
            'delete_message': '@lang("dashboard.delete_message")',
            'update_message': '@lang("dashboard.update_message")',
            'cancel_invoice_title': '@lang("dashboard.cancel_invoice_title")',
            'cancel_invoice_text': '@lang("dashboard.cancel_invoice_text")',
            'draft_invoice_title': '@lang("dashboard.draft_invoice_title")',
            'draft_invoice_text': '@lang("dashboard.draft_invoice_text")',
            'by_applying': '@lang("dashboard.by_applying")',
            'which_is_more': '@lang("dashboard.which_is_more")',
        }
        var table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.invoices.index') }}",
                columns: [
                            { data: 'invoice_number', name: 'invoice_number' },
                            { data: 'client', name: 'client' },
                            { data: 'total', name: 'total' }, 
                            { data: 'status', name: 'status' },
                            { data: 'due_date', name: 'due_date' },
                            { data: 'action', name: 'action'}
                        ],
                language: {
                    search: "@lang('datatable.search')",
                    lengthMenu: "@lang('datatable.show') _MENU_ @lang('datatable.entries')",
                    info: "@lang('datatable.showing') _START_ @lang('datatable.to') _END_ @lang('datatable.of') _TOTAL_ @lang('datatable.records')",
                    paginate: {
                        first: "@lang('datatable.first')",
                        last: "@lang('datatable.last')",
                        next: "@lang('datatable.next')",
                        previous: "@lang('datatable.previous')"
                    },
                    emptyTable: "@lang('datatable.empty')",
                    zeroRecords: "@lang('datatable.zero')",
                }
            });
        });
    </script>
@endsection