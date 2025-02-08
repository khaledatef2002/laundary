<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\PermissionsType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:roles_show', only: ['index']),
            new Middleware('can:roles_create', only: ['create', 'store']),
            new Middleware('can:roles_edit', only: ['edit', 'update']),
            new Middleware('can:roles_delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $quotes = Role::get();
            return DataTables::of($quotes)
            ->rawColumns(['action'])
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>"
                .
                (Auth::user()->hasPermissionTo('roles_edit') ?
                "   
                    <a href='" . route('dashboard.roles.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>    
                "
                :"")
                .
                (Auth::user()->hasPermissionTo('roles_delete') ?
                "
                    <form id='remove_role' data-id='".$row['id']."' onsubmit='remove_role(event, this)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' onclick='remove_button(this)' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                " : "")
                .
                "</div>";
            })
            ->addColumn('users', function(Role $role){
                return $role->users->count();
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('dashboard.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $permissions = array_map(fn($status) => $status->value, PermissionsType::cases());
        $data = $request->validate([
            'name' => ['required', 'unique:roles', 'min:2'],
            'permission' => ['required', 'array'],
            'permission.*' => ['required', Rule::in($permissions)]
        ]);

        $role = Role::create($data);
        $role->syncPermissions($data['permission']);

        return response()->json(['redirectUrl' => route('dashboard.roles.edit', $role)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('dashboard.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $permissions = array_map(fn($status) => $status->value, PermissionsType::cases());
        $data = $request->validate([
            'name' => ['required', 'unique:roles,name,' . $role->id, 'min:2'],
            'permission' => ['required', 'array'],
            'permission.*' => ['required', Rule::in($permissions)]
        ]);

        $role->update($data);
        $role->syncPermissions($data['permission']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
    }
}
