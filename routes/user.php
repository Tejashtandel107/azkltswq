<?php

use App\Http\Controllers\Web;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'user')->prefix('user')->name('user.')->group(function () {
    Route::get('/', [Web\HomeController::class, 'index'])->name('home');
    Route::post('dashboard', [Web\HomeController::class, 'show'])->name('dashboard');

    Route::resource('inwards', Web\InwardsController::class)->except('show', 'update');
    Route::get('inwards/{id}/receipt', [Web\InwardsController::class, 'showReceipt'])->name('inwards.showReceipt');
    Route::get('inwards/getInward', [Web\InwardsController::class, 'getInward']);
    Route::get('inwards/print', [Web\InwardsController::class, 'print'])->name('inwards.print');

    Route::resource('outwards', Web\OutwardsController::class)->except('show');
    Route::get('outwards/{id}/receipt', [Web\OutwardsController::class, 'showReceipt'])->name('outwards.showReceipt');
    Route::get('outwards/print', [Web\OutwardsController::class, 'print'])->name('outwards.print');

    // Route::get('stock-report', 'StockReportsController',['except' => ['show']]);
    Route::get('reports/full-ledger', [Web\FullLedgerReportController::class, 'show'])->name('reports.full-ledger.show');
    // Route::get('reports/stock-report',['as' => 'reports.stock-report.show','uses' => 'StockReportsController@show']);
    Route::post('reports/full-ledger/export', [Web\FullLedgerReportController::class, 'export'])->name('reports.full-ledger.export');
    Route::get('reports/insurance-report', [Web\InsuranceReportsController::class, 'show'])->name('reports.insurance-report.show');

    Route::resource('profile', Web\UserController::class)->only('update');
    Route::get('profile/edit', [Web\UserController::class, 'edit'])->name('profile.edit');
    Route::post('changepassword/{id}', [Web\UserController::class, 'changePassword'])->name('changepassword');
});
