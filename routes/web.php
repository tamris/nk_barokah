<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\MasterDataController;

Route::get('/', function () {
    return redirect()->route('booking.create');
});

// Client Booking Routes
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'create'])->name('create');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('/success/{invoiceNumber}', [BookingController::class, 'success'])->name('success')->where('invoiceNumber', '.*');
    Route::get('/receipt/{invoiceNumber}', [BookingController::class, 'downloadReceipt'])->name('receipt')->where('invoiceNumber', '.*');
});

// Admin Authentication Routes
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Panel Routes
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.bookings.index');
        });

        // Booking Management
        Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingManagementController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{id}/confirm', [BookingManagementController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{id}/reject', [BookingManagementController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{id}/settle', [BookingManagementController::class, 'settle'])->name('bookings.settle');
        Route::post('/bookings/{id}/complete', [BookingManagementController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{id}/discount', [BookingManagementController::class, 'updateDiscount'])->name('bookings.discount');
        Route::post('/bookings/{id}/owner-discount', [BookingManagementController::class, 'updateOwnerDiscount'])->name('bookings.owner_discount');
        Route::post('/bookings/{id}/transport', [BookingManagementController::class, 'updateTransport'])->name('bookings.transport');
        Route::post('/bookings/{id}/addons', [BookingManagementController::class, 'storeAddon'])->name('bookings.addons.store');
        Route::delete('/bookings/{id}/addons/{addon_id}', [BookingManagementController::class, 'deleteAddon'])->name('bookings.addons.delete');

        // Master Packages & Addons
        Route::get('/masters', [MasterDataController::class, 'index'])->name('masters.index');
        Route::post('/packages', [MasterDataController::class, 'storePackage'])->name('packages.store');
        Route::put('/packages/{id}', [MasterDataController::class, 'updatePackage'])->name('packages.update');
        Route::patch('/packages/{id}/toggle', [MasterDataController::class, 'togglePackage'])->name('packages.toggle');

        Route::post('/addons', [MasterDataController::class, 'storeAddon'])->name('addons.store');
        Route::put('/addons/{id}', [MasterDataController::class, 'updateAddon'])->name('addons.update');
        Route::patch('/addons/{id}/toggle', [MasterDataController::class, 'toggleAddon'])->name('addons.toggle');

        // Settings & System Sync
        Route::post('/settings/update-rules', [BookingManagementController::class, 'updateBusinessRules'])->name('settings.update_rules');
        Route::post('/settings/update-banks', [BookingManagementController::class, 'updateBankAccounts'])->name('settings.update_banks');
        Route::post('/settings/update-google', [BookingManagementController::class, 'updateGoogleSettings'])->name('settings.update_google');
        Route::post('/settings/sync-all', [BookingManagementController::class, 'syncAllGoogle'])->name('settings.sync_all');
    });
});
