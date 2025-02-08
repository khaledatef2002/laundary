<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceTranslation;
use Astrotomic\Translatable\Validation\RuleFactory;
use Astrotomic\Translatable\Validation\Rules\TranslatableUnique;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ServicesController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:services_show', only: ['index']),
            new Middleware('can:services_create', only: ['create', 'store']),
            new Middleware('can:services_edit', only: ['edit', 'update']),
            new Middleware('can:services_delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $quotes = Service::get();
            return DataTables::of($quotes)
            ->rawColumns(['action'])
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>"
                .
                (Auth::user()->hasPermissionTo('services_edit') ?
                "
                    <a href='" . route('dashboard.services.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>
                "
                :
                "")
                .
                (Auth::user()->hasPermissionTo('services_delete') ?

                "
                    <form id='remove_services' data-id='".$row['id']."' onsubmit='remove_services(event, this)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' onclick='remove_button(this)' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                "
                : "")
                .
                "</div>";
            })
            ->editColumn('title', function(Service $service){
                return $service->title;
            })
            ->rawColumns(['client', 'action'])
            ->make(true);
        }
        return view('dashboard.services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = RuleFactory::make([
            '%title%' => ['required_with:%title%', 'string', 'min:2', 'max:200', new TranslatableUnique(Service::class, 'title')],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $data = $request->validate($rules);

        $service = Service::create($data);

        return response()->json(['redirectUrl' => route('dashboard.services.edit', $service)]);
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
    public function edit(Service $service)
    {
        return view('dashboard.services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $rules = RuleFactory::make([
            '%title%' => ['required_with:%title%', 'string', 'min:2', 'max:200', (new TranslatableUnique(Service::class, 'title'))->ignore($service->id)],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $data = $request->validate($rules);

        $service->update($data);

        return response()->json(['redirectUrl' => route('dashboard.services.edit', $service)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
    }
}
