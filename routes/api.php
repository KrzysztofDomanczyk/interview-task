<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
Route::get('sendInvoice/{id}', [InvoiceController::class, 'sendInvoice']);
