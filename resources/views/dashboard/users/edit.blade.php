@extends('dashboard.layouts.app')

@section('title', __('dashboard.user.edit'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-sm-auto ms-auto">
                <a href="{{ route('dashboard.users.index') }}"><button class="btn btn-light"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<form id="edit-user-form" data-id="{{ $user->id }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 flex-fill">
                        <label class="form-label" for="full_name">@lang('custom.full-name')</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" id="full_name" name="name" placeholder="@lang('custom.enter-first-name')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">@lang('custom.email')</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" id="email" name="email" placeholder="@lang('custom.email')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">@lang('dashboard.users.new-password')</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="@lang('custom.password')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="role">Role:</label>
                        <select class="form-control" id="role" name="role">
                            <option>@lang('dashboard.select.choose-option')</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->roles?->first()?->id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
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
    <script src="{{ asset('back/js/users.js') }}"></script>
@endsection