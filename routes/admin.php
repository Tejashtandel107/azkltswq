<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('test', [Admin\TestController::class, 'index']);
    Route::get('masterimport', [Admin\MasterImportController::class, 'index']);
    Route::get('orderimport', [Admin\OrderItemImportController::class, 'index']);
    Route::get('script', [Admin\Test1Controller::class, 'index']);

    Route::resource('profile', Admin\UserController::class)->only('edit', 'update');
    Route::post('user/changepassword/{id}', [Admin\UserController::class, 'changePassword'])->name('user.changepassword');
    Route::patch('updatecompanyinfo/{id}', [Admin\UserController::class, 'updateCompanyINFO'])->name('updatecompanyinfo');
    Route::post('storecompanyinfo', [Admin\UserController::class, 'storeCompanyINFO'])->name('storecompanyinfo');

    Route::resource('items', Admin\ItemController::class)->except('show');
    Route::get('items/openmodal', [Admin\ItemController::class, 'openModal'])->name('items.openmodal');
    Route::post('items/savemodal', [Admin\ItemController::class, 'saveModal'])->name('items.savemodal');

    Route::resource('item-marka', Admin\MarkaController::class)->except('show');
    Route::get('item-marka/openmodal', [Admin\MarkaController::class, 'openModal'])->name('item-marka.openmodal');
    Route::post('item-marka/savemodal', [Admin\MarkaController::class, 'saveModal'])->name('item-marka.savemodal');
    Route::get('item-marka/getAll', [Admin\MarkaController::class, 'getAll']);
    Route::get('item-marka/{id}', [Admin\MarkaController::class, 'show'])->name('item-marka');

    Route::middleware('role')->group(function () {
        Route::get('admins/create', [Admin\UserController::class, 'create'])->name('admins.create');
        Route::post('admins/store', [Admin\UserController::class, 'store'])->name('admins.store');
        Route::get('admins', [Admin\UserController::class, 'index'])->name('admins');
        Route::get('admins/edit/{id}', [Admin\UserController::class, 'editAdmin'])->name('admins.edit');
        Route::delete('admins/destroy/{id}', [Admin\UserController::class, 'destroy'])->name('admins.destroy');
        Route::patch('admins/update/{id}', [Admin\UserController::class, 'updateAdmin'])->name('admins.update');

        Route::get('trash/orders', [Admin\OrderTrashController::class, 'index'])->name('trash.orders');
        Route::delete('trash/orders/destroy/{id}', [Admin\OrderTrashController::class, 'destroy'])->name('trash.orders.destroy');
        Route::post('trash/orders/restore/{id}', [Admin\OrderTrashController::class, 'restoreOrder'])->name('trash.orders.restore');

        Route::post('admins/changepass/{id}', [Admin\UserController::class, 'changeAdminPassword'])->name('admins.changepass');
    });

    Route::resource('customers', Admin\CustomerController::class)->except('show');
    Route::get('customers/openmodal', [Admin\CustomerController::class, 'openModal'])->name('customers.openmodal');
    Route::post('customers/savemodal', [Admin\CustomerController::class, 'saveModal'])->name('customers.savemodal');
    Route::patch('customers/update-invoice/{id}', [Admin\CustomerController::class, 'updateInvoice'])->name('customers.update-invoice');

    Route::get('user/create/{id}', [Admin\CustomerController::class, 'createUser'])->name('user.create');
    Route::post('user/store', [Admin\CustomerController::class, 'storeUser'])->name('user.store');
    Route::get('user/edit/{id}', [Admin\CustomerController::class, 'editUser'])->name('user.edit');
    Route::patch('user/update/{id}', [Admin\CustomerController::class, 'updateUser'])->name('user.update');
    Route::post('user/changepass/{id}', [Admin\CustomerController::class, 'changePassword'])->name('user.changepass');
    Route::delete('user/destroy/{id}', [Admin\CustomerController::class, 'destroyUser'])->name('user.destroy');
    // Route::get('customers/getcustomer', 'CustomerController@getCustomer');

    Route::resource('inwards', Admin\InwardsController::class)->except('show');
    Route::get('inwards/{id}/receipt', [Admin\InwardsController::class, 'showReceipt'])->name('inwards.showReceipt');
    Route::get('inwards/getInward', [Admin\InwardsController::class, 'getInward']);
    Route::get('inwards/print', [Admin\InwardsController::class, 'print'])->name('inwards.print');

    Route::resource('outwards', Admin\OutwardsController::class)->except('show');
    Route::get('outwards/{id}/receipt', [Admin\OutwardsController::class, 'showReceipt'])->name('outwards.showReceipt');
    Route::get('outwards/print', [Admin\OutwardsController::class, 'print'])->name('outwards.print');

    // Route::get('stock-report', 'StockReportsController',['except' => ['show']]);
    Route::get('reports/full-ledger', [Admin\FullLedgerReportController::class, 'show'])->name('reports.full-ledger.show');
    Route::get('reports/stock-report', [Admin\StockReportsController::class, 'show'])->name('reports.stock-report.show');
    Route::post('reports/full-ledger/export', [Admin\FullLedgerReportController::class, 'export'])->name('reports.full-ledger.export');
    Route::get('reports/insurance-report', [Admin\InsuranceReportsController::class, 'show'])->name('reports.insurance-report.show');

    Route::post('dashboard', [Admin\HomeController::class, 'show'])->name('dashboard');
    Route::post('outstanding-payments', [Admin\HomeController::class, 'showOutstandingPayment'])->name('outstanding-payments');
});
