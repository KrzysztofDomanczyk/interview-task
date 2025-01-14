<?php

declare(strict_types=1);
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/../src/Modules/Notifications/Presentation/routes.php';

Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
Route::get('sendInvoice/{id}', [InvoiceController::class, 'sendInvoice']);
