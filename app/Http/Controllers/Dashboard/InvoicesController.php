<?php

namespace App\Http\Controllers\Dashboard;

use App\Enum\DiscountType;
use App\enum\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicesService;
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
                    InvoiceStatus::DRAFT->value => "<span class='badge bg-dark'>". _('dashboard.draft') ."</span>",
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
            'discount' => 'required|numeric|min:0',
            'discount_type' => ['required', Rule::in(DiscountType::FIXED->value, DiscountType::PERCENTAGE->value)],
            'due_date' => ['required', 'date'],

            'services' => ['required', 'array', 'min:1'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.price' => ['required', 'numeric', 'min:0'],
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.discount' => 'required|numeric|min:0',
            'services.*.discount_type' => ['required', Rule::in(DiscountType::FIXED->value, DiscountType::PERCENTAGE->value)],
        ]);

        $data['due_date'] = date("Y-m-d", strtotime($data['due_date']));
        $data['status'] = InvoiceStatus::DRAFT->value;

        $invoice = Invoice::create([
            'client_id' => $data['client_id'],
            'discount' => $data['discount'],
            'discount_type' => $data['discount_type'],
            'due_date' => $data['due_date']
        ]);
        
        foreach ($data['services'] as $key => $service) {
            InvoicesService::create([
                'invoice_id' => $invoice->id,
                'service_id' => $service['service_id'],
                'quantity' => $service['quantity'],
                'price' => $service['price'],
                'discount' => $service['discount'],
                'discount_type' => $service['discount_type']
            ]);
        }
        
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
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
    }

    public function check_add_service(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required' , 'numeric', 'min:0'],
            'discount' => ['numeric', 'min:0'],
            'discount_type' => ['required', Rule::in(DiscountType::FIXED->value, DiscountType::PERCENTAGE->value)]
        ]);        
    }

    public function cancel(Invoice $invoice)
    {
        if($invoice->status == InvoiceStatus::CANCELED->value)
        {
            return response()->json(['error' => 'Invoice is already canceled and cannot be closed again.'], 400);
        }

        $invoice->payments()->delete();

        $invoice->status = InvoiceStatus::CANCELED->value;

        $invoice->save();
    }

    public function draft(Invoice $invoice)
    {
        if($invoice->status == InvoiceStatus::DRAFT->value)
        {
            return response()->json(['error' => 'Invoice is already drafted and cannot be drafted again.'], 400);
        }

        $invoice->payments()->delete();

        $invoice->status = InvoiceStatus::DRAFT->value;

        $invoice->save();
    }

    public function confirm(Invoice $invoice)
    {
        if(in_array($invoice->status, [InvoiceStatus::PAID->value, InvoiceStatus::PARTIALLY_PAID, InvoiceStatus::UNPAID]))
        {
            return response()->json(['error' => 'Invoice is already confirmed and cannot be confirmed again.'], 400);
        }

        if($invoice->status == InvoiceStatus::CANCELED->value)
        {
            return response()->json(['error' => 'Can\'t confirm a canceled invoice, Please set it to draft first.'], 400);
        }

        $invoice->status = InvoiceStatus::UNPAID->value;

        $invoice->save();
    }
}
