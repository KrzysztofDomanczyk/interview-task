<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;
use Modules\Notifications\Presentation\Http\NotificationController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::get('/', static function () {
    return 'Ingenious BE interview task. Please refer to the README.md file for more information.';
});




