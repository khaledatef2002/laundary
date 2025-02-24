<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box py-2">
        <!-- Dark Logo-->
        <a href="{{ route('dashboard.index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset($settings->logo) }}" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ asset($settings->logo) }}" alt="" height="70">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('dashboard.index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset($settings->logo) }}" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ asset($settings->logo) }}" alt="" height="70">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.index' ? 'active' : ''}}" href="{{ route('dashboard.index') }}" role="button">
                        <i class="ri-home-3-fill"></i> <span>@lang('dashboard.home')</span>
                    </a>
                </li>
                @if (Auth::user()->hasPermissionTo('services_show'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.services.index' ? 'active' : ''}}" href="{{ route('dashboard.services.index') }}" role="button">
                            <i class="ri-hand-coin-line"></i> <span>@lang('dashboard.services')</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermissionTo('invoices_show'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.invoices.index' ? 'active' : ''}}" href="{{ route('dashboard.invoices.index') }}" role="button">
                            <i class="ri-shopping-basket-2-line"></i> <span>@lang('dashboard.invoices')</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermissionTo('clients_show'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.clients.index' ? 'active' : ''}}" href="{{ route('dashboard.clients.index') }}" role="button">
                            <i class="ri-shake-hands-fill"></i> <span>@lang('dashboard.clients')</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermissionTo('users_show'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.users.index' ? 'active' : ''}}" href="{{ route('dashboard.users.index') }}" role="button">
                            <i class="ri-user-fill"></i> <span>@lang('dashboard.users')</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermissionTo('roles_show'))
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.roles.index' ? 'active' : ''}}" href="{{ route('dashboard.roles.index') }}" role="button">
                            <i class="ri-key-2-fill"></i> <span>@lang('dashboard.roles')</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->hasPermissionTo('system_settings_show'))                    
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Route::currentRouteName() ==  'dashboard.system_settings.edit' ? 'active' : ''}}" href="{{ route('dashboard.system_settings.edit', 1) }}" role="button">
                            <i class="ri-tools-fill"></i> <span>@lang('dashboard.system_settings')</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>