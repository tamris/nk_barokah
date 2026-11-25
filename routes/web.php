<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingFinanceController;
use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\BookingOperationsController;
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

Route::prefix('admin')->name('admin.')->middleware('admin.locale')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Admin Panel Routes (Accessible by both Owner & Field Coordinator)
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.bookings.index');
        });

        // Per-device display language switcher (Owner + Field Coordinator)
        Route::post('/locale', [AdminSettingsController::class, 'updateLocale'])->name('locale.update');

        // Shared Operational Views & Field Actions (Owner + Field Staff)
        Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingManagementController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{id}/addons', [BookingFinanceController::class, 'storeAddon'])->name('bookings.addons.store');
        Route::post('/bookings/{id}/survey-status', [BookingOperationsController::class, 'updateSurveyStatus'])->name('bookings.survey_status');
        Route::post('/bookings/{id}/operations', [BookingOperationsController::class, 'updateOperations'])->name('bookings.operations');
        Route::get('/bookings/{id}/spk', [BookingOperationsController::class, 'showSpk'])->name('bookings.spk');

        // Strictly Owner-Only Actions (Financials, Verifications, Master Catalog & Settings)
        Route::middleware('owner')->group(function () {
            Route::post('/bookings/{id}/confirm', [BookingManagementController::class, 'confirm'])->name('bookings.confirm');
            Route::post('/bookings/{id}/reject', [BookingManagementController::class, 'reject'])->name('bookings.reject');
            Route::post('/bookings/{id}/settle', [BookingManagementController::class, 'settle'])->name('bookings.settle');
            Route::post('/bookings/{id}/complete', [BookingManagementController::class, 'complete'])->name('bookings.complete');

            Route::post('/bookings/{id}/discount', [BookingFinanceController::class, 'updateDiscount'])->name('bookings.discount');
            Route::post('/bookings/{id}/owner-discount', [BookingFinanceController::class, 'updateOwnerDiscount'])->name('bookings.owner_discount');
            Route::post('/bookings/{id}/transport', [BookingFinanceController::class, 'updateTransport'])->name('bookings.transport');
            Route::delete('/bookings/{id}/addons/{addon_id}', [BookingFinanceController::class, 'deleteAddon'])->name('bookings.addons.delete');

            // Master Packages & Addons
            Route::get('/masters', [MasterDataController::class, 'index'])->name('masters.index');
            Route::post('/packages', [MasterDataController::class, 'storePackage'])->name('packages.store');
            Route::put('/packages/{id}', [MasterDataController::class, 'updatePackage'])->name('packages.update');
            Route::patch('/packages/{id}/toggle', [MasterDataController::class, 'togglePackage'])->name('packages.toggle');

            Route::post('/addons', [MasterDataController::class, 'storeAddon'])->name('addons.store');
            Route::put('/addons/{id}', [MasterDataController::class, 'updateAddon'])->name('addons.update');
            Route::patch('/addons/{id}/toggle', [MasterDataController::class, 'toggleAddon'])->name('addons.toggle');

            // Settings & System Sync
            Route::post('/settings/update-rules', [AdminSettingsController::class, 'updateBusinessRules'])->name('settings.update_rules');
            Route::post('/settings/update-banks', [AdminSettingsController::class, 'updateBankAccounts'])->name('settings.update_banks');
            Route::post('/settings/update-pin', [AdminSettingsController::class, 'updateOwnerPin'])->name('settings.update_pin');
            Route::post('/settings/update-staff-access', [AdminSettingsController::class, 'updateStaffAccess'])->name('settings.update_staff');
            Route::post('/settings/update-google', [AdminSettingsController::class, 'updateGoogleSettings'])->name('settings.update_google');
            Route::post('/settings/sync-all', [AdminSettingsController::class, 'syncAllGoogle'])->name('settings.sync_all');
        });
    });
});
