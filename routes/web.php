<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DocumentController;

Route::get('/invoice/stream', [DocumentController::class, 'streamInvoice']);
Route::get('/delivery-note/stream', [DocumentController::class, 'streamDeliveryNote']);
Route::get('/delivery-note/tailwind-stream', [DocumentController::class, 'streamTailwindDeliveryNote']);
Route::get('/invoice/download', [DocumentController::class, 'downloadInvoice']);