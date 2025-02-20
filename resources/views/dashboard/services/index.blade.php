@extends('dashboard.layouts.app')

@section('title', __('dashboard.services'))

@section('content')

@if (Auth::user()->hasPermissionTo('services_create'))
    <div class="card">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-auto ms-auto">
                    <a href="{{ route('dashboard.services.create') }}"><button class="btn btn-success"><i class="ri-add-fill me-1 align-bottom"></i> @lang('dashboard.services.add')</button></a>
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
                    <th>@lang('dashboard.id')</th>
                    <th>@lang('dashboard.title')</th>
                    <th>@lang('dashboard.price')</th>
                    <th>@lang('dashboard.action')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
    <script>
        const lang = {
            'delete_message': '@lang("dashboard.delete_message")',
            'update_message': '@lang("dashboard.update_message")',
        }
    </script>
    <script src="{{ asset('back/js/services.js') }}"></script>
    <script>
        var table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.services.index') }}",
                columns: [
                            { data: 'id', name: 'id' },
                            { data: 'title', name: 'title' },
                            { data: 'price', name: 'price' },
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