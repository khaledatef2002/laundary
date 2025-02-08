<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ClientsController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:clients_show', only: ['index']),
            new Middleware('can:clients_create', only: ['create', 'store']),
            new Middleware('can:clients_edit', only: ['edit', 'update']),
            new Middleware('can:clients_delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if($request->ajax())
        {
            $quotes = Client::get();
            return DataTables::of($quotes)
            ->rawColumns(['action'])
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>"
                .
                (Auth::user()->hasPermissionTo('clients_edit') ?
                "
                    <a href='" . route('dashboard.clients.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>
                "
                :
                "")
                .
                (Auth::user()->hasPermissionTo('clients_delete') ?

                "
                    <form id='remove_client' data-id='".$row['id']."' onsubmit='remove_client(event, this)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' onclick='remove_button(this)' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                "
                : "")
                .
                "</div>";
            })
            ->editColumn('client', function(Client $client){
                return "
                    <div class='d-flex align-items-center gap-2'>
                        <span>{$client->name}</span>
                    </div>
                ";
            })
            ->editColumn('address', function(Client $client){
                return str::substr($client->address, 0, strlen($client->address) > 100 ? 100 : strlen($client->address));
            })
            ->rawColumns(['client', 'action'])
            ->make(true);
        }
        return view('dashboard.clients.index');
    }

    public function create()
    {
        return view('dashboard.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'email' => ['string', 'lowercase', 'email', 'max:100', Rule::unique(Client::class)],
            'phone' => ['required'],
            'address' => ['required', 'min:5', 'max:500'],
        ]);

        $client = Client::create($data);

        return response()->json(['redirectUrl' => route('dashboard.clients.edit', $client)]);
    }

    public function edit(Client $client)
    {
        return view('dashboard.clients.edit', compact('client'));
    }

    
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'email' => ['string', 'lowercase', 'email', 'max:100', Rule::unique(Client::class)->ignore($client->id)],
            'phone' => ['required'],
            'address' => ['required', 'min:5', 'max:500'],
        ]);

        $client->update($data);
    }
}
