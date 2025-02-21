<?php

use App\helper\select2;
use App\Http\Controllers\Dashboard\{ClientsController, HomeController, InvoicesController, RolesController, ServicesController, SystemSettings, SystemSettingsController, UsersController};
use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

Route::name('dashboard.')->prefix(LaravelLocalization::setLocale() . '/dashboard')->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])->group(function(){
    require __DIR__.'/auth.php';
    
    Route::middleware(['auth'])->group(function(){
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::resource('services', ServicesController::class)->except('show');

        Route::resource('invoices', InvoicesController::class);
        Route::post('/check-add-service', [InvoicesController::class, 'check_add_service']);
        Route::post('/check-add-payment/{invoice}/{invoice_payment?}', [InvoicesController::class, 'check_add_payment']);
        Route::post('invoice/{invoice}/cancel', [InvoicesController::class, 'cancel']);
        Route::post('invoice/{invoice}/draft', [InvoicesController::class, 'draft']);
        Route::post('invoice/{invoice}/confirm', [InvoicesController::class, 'confirm']);

        Route::get('/invoice/{invoice}', function(Invoice $invoice){
            $action = "view";
            return view('dashboard.templates.invoice', compact('invoice', 'action'));
        })->name('invoice.template');

        Route::get('/invoice/{invoice}/download', function(Invoice $invoice){
            return Pdf::view('dashboard.templates.invoice', compact('invoice'))
                    ->withBrowsershot(function(Browsershot $browsershot){
                        $browsershot->setNodeBinary('/usr/local/bin/node')
                        ->setNpmBinary('/usr/local/bin/npm');
                    })
                    ->format(Format::A5)
                    ->name($invoice->invoice_number . ".pdf");
        })->name('invoice.template.download');

        Route::get('/load_kpis', [HomeController::class, 'render_kpis']);

        Route::resource('clients', ClientsController::class)->except('show');
        Route::resource('users', UsersController::class)->except('show');
        Route::resource('roles', RolesController::class)->except('show');
        Route::resource('system_settings', SystemSettingsController::class)->only(['edit', 'update']);

        Route::prefix('/select2')->name('select2.')->group(function(){
            Route::get('/clients', [select2::class, 'clients'])->name('clients');
            Route::get('/services', [select2::class, 'services'])->name('services');
        });
    });
});