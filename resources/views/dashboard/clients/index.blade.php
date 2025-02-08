@extends('dashboard.layouts.app')

@section('title', __('dashboard.clients'))

@section('content')

@if (Auth::user()->hasPermissionTo('clients_create'))
    <div class="card">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-auto ms-auto">
                    <a href="{{ route('dashboard.clients.create') }}"><button class="btn btn-success"><i class="ri-add-fill me-1 align-bottom"></i> @lang('dashboard.clients.add')</button></a>
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
                    <th>@lang('dashboard.client')</th>
                    <th>@lang('dashboard.email')</th>
                    <th>@lang('dashboard.phone')</th>
                    <th>@lang('dashboard.address')</th>
                    <th>@lang('dashboard.action')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/clients.js') }}"></script>
    <script>
        var table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.clients.index') }}",
                columns: [
                            { data: 'id', name: 'id' },
                            { data: 'client', name: 'client' },
                            { data: 'email', name: 'email' },
                            { data: 'phone', name: 'phone' },
                            { data: 'address', name: 'address' },
                            { data: 'action', name: 'action'}
                        ]
            });
        });
    </script>
@endsection