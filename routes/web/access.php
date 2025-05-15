<?php

use App\Http\Controllers\AccessController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

// Access Management

Route::group(['middleware' => ['auth']], function () {
    
    Route::get('access', [AccessController::class, 'index'])->name('access.index')
        ->breadcrumbs(fn (Trail $trail) =>
        $trail->parent('home')
            ->push(trans('general.access'), route('access.index')));

    Route::get('access/create', [AccessController::class, 'create'])->name('access.create')
        ->breadcrumbs(fn (Trail $trail) =>
        $trail->parent('access.index')
            ->push(trans('admin/access/general.create'), route('access.create')));

    Route::post('access', [AccessController::class, 'store'])->name('access.store');

    Route::get('access/{access}', [AccessController::class, 'show'])->name('access.show')
        ->breadcrumbs(fn (Trail $trail, $access) =>
        $trail->parent('access.index')
            ->push(trans('general.access') . ' ' . $access->access_tag, route('access.show', $access)));

    Route::get('access/{access}/edit', [AccessController::class, 'edit'])->name('access.edit')
        ->breadcrumbs(fn (Trail $trail, $access) =>
        $trail->parent('access.show', $access)
            ->push(trans('admin/access/general.edit'), route('access.edit', $access)));

    Route::put('access/{access}', [AccessController::class, 'update'])->name('access.update');
    Route::delete('access/{access}', [AccessController::class, 'destroy'])->name('access.destroy');

    // Access Checkout/Checkin
    Route::get('access/{access}/checkout', [AccessController::class, 'getCheckout'])->name('access.checkout.show')
        ->breadcrumbs(fn (Trail $trail, $access) =>
        $trail->parent('access.show', $access)
            ->push(trans('admin/access/general.checkout'), route('access.checkout.show', $access)));

    Route::post('access/{access}/checkout', [AccessController::class, 'postCheckout'])->name('access.checkout.store');
    
    Route::get('access/{access}/checkin', [AccessController::class, 'getCheckin'])->name('access.checkin.show')
        ->breadcrumbs(fn (Trail $trail, $access) =>
        $trail->parent('access.show', $access)
            ->push(trans('admin/access/general.checkin'), route('access.checkin.show', $access)));

    Route::post('access/{access}/checkin', [AccessController::class, 'postCheckin'])->name('access.checkin.store');
}); 