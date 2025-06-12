<?php

use App\Http\Controllers\Licenses;
use Illuminate\Support\Facades\Route;
use App\Models\License;
use App\Models\LicenseSeat;
use Tabuna\Breadcrumbs\Trail;
use App\Models\Setting;

// Licenses
Route::group(['prefix' => 'licenses', 'middleware' => ['auth']], function () {
    Route::get('{licenseId}/clone', [Licenses\LicensesController::class, 'getClone'])->name('clone/license');

    Route::get('{licenseId}/freecheckout',
        [Licenses\LicensesController::class, 'getFreeLicense']
    )->name('licenses.freecheckout');


        
    Route::get('{license}/audit', [Licenses\LicensesController::class, 'audit'])
        ->name('licenses.audit.create')
        ->breadcrumbs(fn (Trail $trail, License $license) =>
        $trail->parent('licenses.show', $license)
            ->push(trans('general.audit'))
        );

    Route::post('{license}/audit',
        [Licenses\LicensesController::class, 'auditStore']
    )->name('licenses.audit.store');

    Route::get('{license}/checkout/{seatId?}', [Licenses\LicenseCheckoutController::class, 'create'])
        ->name('licenses.checkout')
        ->breadcrumbs(fn (Trail $trail, License $license) =>
        $trail->parent('licenses.show', $license)
            ->push(trans('general.checkout'), route('licenses.checkout', $license))
        );

    Route::post(
        '{licenseId}/checkout/{seatId?}',
        [Licenses\LicenseCheckoutController::class, 'store']
    ); //name() would duplicate here, so we skip it.

    Route::get('{licenseSeat}/checkin/{backto?}', [Licenses\LicenseCheckinController::class, 'create'])
        ->name('licenses.checkin')
        ->breadcrumbs(fn (Trail $trail, LicenseSeat $licenseSeat) =>
        $trail->parent('licenses.show', $licenseSeat->license)
            ->push(trans('general.checkin'), route('licenses.checkin', $licenseSeat))
        );

    Route::post('{licenseId}/checkin/{backto?}',
        [Licenses\LicenseCheckinController::class, 'store']
    )->name('licenses.checkin.save');

    Route::post(
        '{licenseId}/bulkcheckin',
        [Licenses\LicenseCheckinController::class, 'bulkCheckin']
    )->name('licenses.bulkcheckin');

    Route::post(
        '{licenseId}/bulkcheckout',
        [Licenses\LicenseCheckoutController::class, 'bulkCheckout']
    )->name('licenses.bulkcheckout');

    Route::post('bulkedit',
        [Licenses\LicensesController::class, 'bulkEdit']
    )->name('licenses.bulkedit');

    Route::post('bulkaudit',
        [Licenses\LicensesController::class, 'bulkAudit']
    )->name('licenses.bulkaudit');

    // Handle GET requests to bulkaudit (for back button navigation) by redirecting to licenses index
    Route::get('bulkaudit', function() {
        return redirect()->route('licenses.index');
    });

    Route::post(
    '{licenseId}/upload',
        [Licenses\LicenseFilesController::class, 'store']
    )->name('upload/license');

    Route::delete(
    '{licenseId}/deletefile/{fileId}',
        [Licenses\LicenseFilesController::class, 'destroy']
    )->name('delete/licensefile');
    Route::get(
    '{licenseId}/showfile/{fileId}/{download?}',
        [Licenses\LicenseFilesController::class, 'show']
    )->name('show.licensefile');
    Route::get(
        'export',
        [
            Licenses\LicensesController::class,
            'getExportLicensesCsv'
        ]
    )->name('licenses.export');
});

Route::resource('licenses', Licenses\LicensesController::class, [
    'middleware' => ['auth'],
]);

// License seats management (with audit functionality)
Route::get('license-seats', [Licenses\LicenseSeatsController::class, 'dueForAudit'])
    ->name('license_seats.index')
    ->breadcrumbs(fn (Trail $trail) =>
    $trail->parent('licenses.index')
        ->push(trans('admin/licenses/general.license_seats'), route('license_seats.index'))
    );

    
Route::get('seats/{licenseSeat}/audit', [Licenses\LicenseSeatsController::class, 'audit'])
    ->name('license_seats.audit.create')
    ->breadcrumbs(fn (Trail $trail, LicenseSeat $licenseSeat) =>
    $trail->parent('licenses.show', $licenseSeat->license)
        ->push(trans('general.audit'))
    );

Route::post('seats/{licenseSeat}/audit',
    [Licenses\LicenseSeatsController::class, 'auditStore']
)->name('license_seats.audit.store');

Route::post('seats/bulkaudit',
    [Licenses\LicenseSeatsController::class, 'bulkAudit']
)->name('license_seats.bulkaudit');

// Handle GET requests to bulkaudit (for back button navigation) by redirecting to license seats page
Route::get('seats/bulkaudit', function() {
    return redirect()->route('license_seats.index');
});

Route::get('license-seats/bulkaudit', function() {
    return redirect()->route('license_seats.index');
});

Route::post('seats/storeaudit',
    [Licenses\LicenseSeatsController::class, 'storeBulkAudit']
)->name('license_seats.storeaudit');


Route::post('seats/storebulknotes',
    [Licenses\LicenseSeatsController::class, 'storeBulkNotes']
)->name('license_seats.storebulknotes');
