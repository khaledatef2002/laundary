<?php

namespace App\Enum;

enum PermissionsType: string
{

    // Services
    case services_show = 'services_show';
    case services_edit = 'services_edit';
    case services_delete = 'services_delete';
    case services_create = 'services_create';

    // Clients
    case clients_show = 'clients_show';
    case clients_edit = 'clients_edit';
    case clients_delete = 'clients_delete';
    case clients_create = 'clients_create';

    // Invoices
    case invoices_show = 'invoices_show';
    case invoices_edit = 'invoices_edit';
    case invoices_delete = 'invoices_delete';
    case invoices_create = 'invoices_create';

    // users
    case users_show = 'users_show';
    case users_edit = 'users_edit';
    case users_delete = 'users_delete';
    case users_create = 'users_create';

    // roles
    case roles_show = 'roles_show';
    case roles_edit = 'roles_edit';
    case roles_delete = 'roles_delete';
    case roles_create = 'roles_create';

    // System 
    case system_settings_show = 'system_settings_show';
    case system_settings_edit = 'system_settings_edit';

    // KPIS
    case kpis_show = 'kpis_show';
}
