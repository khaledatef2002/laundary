<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\DiscountType;
use App\enum\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class InvoicesController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:invoices_show', only: ['index']),
            new Middleware('can:invoices_create', only: ['create', 'store']),
            new Middleware('can:invoices_edit', only: ['edit', 'update']),
            new Middleware('can:invoices_delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $quotes = Invoice::get();
            return DataTables::of($quotes)
            ->rawColumns(['action'])
            ->addColumn('action', function($row){
                return 
                "<div class='d-flex align-items-center justify-content-center gap-2'>"
                .
                (Auth::user()->hasPermissionTo('invoices_edit') ?
                "
                    <a href='" . route('dashboard.invoices.edit', $row) . "'><i class='ri-settings-5-line fs-4' type='submit'></i></a>
                "
                :
                "")
                .
                (Auth::user()->hasPermissionTo('invoices_delete') ?

                "
                    <form id='remove_invoice' data-id='".$row['id']."' onsubmit='remove_invoice(event, this)'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value='" . csrf_token() . "'>
                        <button class='remove_button' onclick='remove_button(this)' type='button'><i class='ri-delete-bin-5-line text-danger fs-4'></i></button>
                    </form>
                "
                : "")
                .
                "</div>";
            })
            ->addColumn('client', function(Invoice $invoice){
                return "<a href='". route('dashboard.clients.edit', $invoice->client->id) ."'>" . $invoice->client->name . "</a>";
            })
            ->addColumn('total', function(Invoice $invoice){
                return $invoice->total_amount;
            })
            ->editColumn('status', function(Invoice $invoice){
                return match($invoice->status)
                {
                    InvoiceStatus::PAID->value => "<span class='badge bg-success'>". _('dashboard.paid') ."</span>",
                    InvoiceStatus::UNPAID->value => "<span class='badge bg-secondary'>". _('dashboard.unpaid') ."</span>",
                    InvoiceStatus::PARTIALLY_PAID->value => "<span class='badge bg-warning'>". _('dashboard.partially_paid') ."</span>",
                    InvoiceStatus::CANCELED->value => "<span class='badge bg-danger'>". _('dashboard.canceled') ."</span>",
                };
            })
            ->rawColumns(['client', 'status', 'action'])
            ->make(true);
        }
        return view('dashboard.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'required|numeric|min:0',
            'discount_type' => ['required', Rule::in(DiscountType::FIXED->value, DiscountType::PERCENTAGE->value)],
            'due_date' => ['required', 'date'],
        ]);

        $data['due_date'] = date("Y-m-d", strtotime($data['due_date']));
        $data['status'] = InvoiceStatus::UNPAID->value;

        $invoice = Invoice::create($data);
        
        return response()->json(['redirectUrl' => route('dashboard.invoices.edit', $invoice)]);
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
    public function edit(Invoice $invoice)
    {
        return view('dashboard.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
