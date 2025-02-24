<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:users_show', only: ['index']),
            new Middleware('can:users_create', only: ['create', 'store']),
            new Middleware('can:users_edit', only: ['edit', 'update']),
            new Middleware('can:users_delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if($request->ajax())
        {
            $quotes = User::get();
            return DataTables::of($quotes)
            ->rawColumns(['action'])
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>"
                .
                (Auth::user()->hasPermissionTo('users_edit') ?
                "
                    <a href='" . route('dashboard.users.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>
                "
                :
                "")
                .
                (Auth::user()->hasPermissionTo('users_delete') ?

                "
                    <form id='remove_user' data-id='".$row['id']."' onsubmit='remove_user(event, this)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' onclick='remove_button(this)' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                "
                : "")
                .
                "</div>";
            })
            ->editColumn('user', function(User $user){
                return "
                    <div class='d-flex align-items-center gap-2'>
                        <span>{$user->name} ". (Auth::id() == $user->id ? '('. __('dashboard.you') .')' : '') ."</span>
                    </div>
                ";
            })
            ->editColumn('phone', function(User $user){
                return "+" . $user->country_code . $user->phone;
            })
            ->editColumn('role', function(User $user){
                if(isset($user->getRoleNames()[0]))
                {
                    return'<span class="badge bg-primary">'. $user->getRoleNames()[0] .'</span>';
                }
            })
            ->rawColumns(['user', 'role', 'action'])
            ->make(true);
        }
        return view('dashboard.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('dashboard.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', Rule::unique(User::class)],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'exists:roles,id']
        ]);

        $user = User::create($data);

        $role = Role::find($request->role);

        $user->assignRole($role->name);

        return response()->json(['redirectUrl' => route('dashboard.users.edit', $user)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('dashboard.users.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validations = [
            'name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', 'exists:roles,id']
        ];

        if($request->password)
            $validations['password'] = ['required', Password::defaults()];

        $data = $request->validate($validations);

        $user->update($data);

        $role = Role::find($request->role);

        $user->syncRoles([$role->name]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
    }
}
