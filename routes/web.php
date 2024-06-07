<?php

use App\Http\Controllers\CallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('redirect_flutterwave', [CallbackController::class, 'success_flutterwave'])->name('redirect_flutterwave');
Route::get('success_paydunya', [CallbackController::class, 'success_paydunya'])->name('success_paydunya');
Route::get('echec_paydunya', [CallbackController::class, 'echec_paydunya'])->name('echec_paydunya');
