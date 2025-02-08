<?php

use App\Http\Controllers\Dashboard\{ClientsController, HomeController, RolesController, ServicesController, UsersController};
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::name('dashboard.')->prefix(LaravelLocalization::setLocale() . '/dashboard')->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])->group(function(){
    require __DIR__.'/auth.php';
    
    
    Route::middleware(['auth'])->group(function(){
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::resource('users', UsersController::class)->except('show');
        Route::resource('clients', ClientsController::class)->except('show');
        Route::resource('services', ServicesController::class)->except('show');
        Route::resource('roles', RolesController::class)->except('show');
    });
});