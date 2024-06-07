<?php

use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\SecurityController;
use App\Http\Controllers\API\StaticController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('authenticate', [SecurityController::class, 'authenticate']);
Route::get('countries', [StaticController::class, 'countries']);
Route::get('transactions', [TransactionController::class, 'transactions']);
Route::get('transactions/{id}/list', [TransactionController::class, 'transaction_customer']);
Route::get('cards/{id}/list', [CardController::class, 'card_customer']);
Route::get('accounts/{id}', [SecurityController::class, 'getAccount']);
Route::post('create_account', [SecurityController::class, 'create']);
Route::post('cards', [CardController::class, 'create']);
Route::post('transactions/create_deposit', [TransactionController::class, 'create_deposit']);
Route::delete('accounts/{id}/delete', [SecurityController::class, 'delete_account']);
Route::post('accounts/{id}/change_password', [SecurityController::class, 'changepassword']);
Route::post('accounts/{id}/update', [SecurityController::class, 'update']);
