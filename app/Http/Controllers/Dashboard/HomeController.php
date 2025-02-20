<?php

namespace App\Http\Controllers\Dashboard;

use App\enum\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicesService;
use App\Models\Service;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    private function get_stastics_data($start_date, $end_date)
    {
        $start_date = Carbon::createFromFormat('d/m/Y', $start_date);
        $end_date = Carbon::createFromFormat('d/m/Y', $end_date);

        $invoices = Invoice::where('due_date', '>=', $start_date)->where('due_date', '<=', $end_date);
        $invoices_collection = $invoices->get();

        $total_invoices = $invoices->count();

        $total_earning = $invoices_collection->reduce(function($carry, $invoice){
            return $carry + $invoice->paid_amount;
        }, 0);

        $remaining_earning = $invoices_collection->reduce(function($carry, $invoice){
            return $carry + ($invoice->total_amount - $invoice->paid_amount);
        }, 0);

        $customers = $invoices_collection->pluck('client_id')->unique()->count();

        $groupedInvoices = $invoices_collection->groupBy(function ($invoice) {
            return Carbon::parse($invoice['due_date'])->format("d/m/Y");
        });

        $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date->copy()->addDay());
        $dailyCounts = collect();

        foreach ($period as $date) {
            $formattedDate = $date->format('d/m/Y'); // e.g., 1/2/2025
            $dailyCounts[$formattedDate] 
                = 
                    $groupedInvoices->has($formattedDate)
                ? 
                    $groupedInvoices->get($formattedDate)->reduce(function($carry, $invoice){
                        return $carry + $invoice->paid_amount;
                    }, 0)
                : 0;
        }

        $money_per_day = $dailyCounts->all();

        $draft_invoices = $invoices_collection->where('status', InvoiceStatus::DRAFT->value)->count();
        $unpaid_invoices = $invoices_collection->where('status', InvoiceStatus::UNPAID->value)->count();
        $partially_invoices = $invoices_collection->where('status', InvoiceStatus::PARTIALLY_PAID->value)->count();
        $paid_invoices = $invoices_collection->where('status', InvoiceStatus::PAID->value)->count();

        $invoices_with_services = InvoicesService::get()->groupBy('service_id')->map(function ($invoice_services){
            return [
                'service_name' => $invoice_services->first()->service->title,
                'total_amount' => $invoice_services->sum('total_amount'),
                'total_quantity' => $invoice_services->sum('quantity'),
                'total_invoices' => $invoice_services->pluck('invoice')->unique()->count()
            ];
        })->values()->sortByDesc('total_amount')->take(10)->all();

        $invoices_with_clients = Invoice::get()->groupBy('client_id')->map(function($invoices){
            return [
                'client_name' => $invoices->first()->client->name,
                'total_invoices' => $invoices->count(),
                'total_amount' => $invoices->sum('total_amount')
            ];
        })->values()->sortByDesc('total_amount')->take(10)->all();

        return [
            "total_invoices" => $total_invoices,
            "total_earning" => $total_earning,
            "remaining_earning" => $remaining_earning,
            "customers" => $customers,
            "money_per_day" => $money_per_day,
            "draft_invoices" => $draft_invoices,
            "unpaid_invoices" => $unpaid_invoices,
            "partially_invoices" => $partially_invoices,
            "paid_invoices" => $paid_invoices,
            "invoices_with_services" => $invoices_with_services,
            'invoices_with_clients' => $invoices_with_clients
        ];
    }

    public function render_kpis(Request $request)
    {
        $start_date = date("d/m/Y", strtotime($request->date_from));
        $end_date = date("d/m/Y", strtotime($request->date_to));

        $statistics = $this->get_stastics_data($start_date, $end_date);
        return view('dashboard.kpis', compact('statistics'));
    }
}
