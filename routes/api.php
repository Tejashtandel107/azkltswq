<?php

use App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::name('api.')->group(function () {
    Route::post('inwards/search', [Api\InwardsController::class, 'Search']);
    Route::get('item-marka/fetchMarka', [Api\MarkaController::class, 'fetchMarka']);
    Route::get('item-marka/fetchCustomerMarka', [Api\MarkaController::class, 'fetchCustomerMarka']);
    Route::get('customers/getCustomer', [Api\CustomerController::class, 'getCustomer']);
    Route::post('inwards/getInward', [Api\InwardsController::class, 'getInward']);
});
