<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBookingToGoogleServicesJob;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    public function syncAllGoogle(): RedirectResponse
    {
        $bookings = Booking::with('addons')
            ->whereIn('status', ['confirmed', 'completed', 'pending_verification'])
            ->get();

        foreach ($bookings as $b) {
            SyncBookingToGoogleServicesJob::dispatch($b);
        }

        return back()->with('success', "Database synchronization for {$bookings->count()} bookings has been queued successfully!");
    }

    public function updateBusinessRules(Request $request): RedirectResponse
    {
        if ($request->has('min_dp_amount')) {
            $cleanMinDp = preg_replace('/[^\d]/', '', (string) $request->input('min_dp_amount'));
            $request->merge(['min_dp_amount' => $cleanMinDp !== '' ? (float) $cleanMinDp : 0]);
        }

        if ($request->has('transport_fee_per_tier')) {
            $cleanTransport = preg_replace('/[^\d]/', '', (string) $request->input('transport_fee_per_tier'));
            $request->merge(['transport_fee_per_tier' => $cleanTransport !== '' ? (float) $cleanTransport : 300000]);
        }

        $validated = $request->validate([
            'standard_package_max_days' => ['required', 'integer', 'min:1', 'max:14'],
            'extra_day_charge_rate'     => ['required', 'numeric', 'min:0', 'max:100'],
            'min_dp_amount'             => ['required', 'numeric', 'min:0'],
            'max_events_per_day'        => ['required', 'integer', 'min:1', 'max:10'],
            'admin_whatsapp'            => ['required', 'string', 'max:25'],
            'warehouse_address'         => ['nullable', 'string', 'max:255'],
            'warehouse_lat'             => ['nullable', 'string', 'max:50'],
            'warehouse_lng'             => ['nullable', 'string', 'max:50'],
            'free_distance_km'          => ['nullable', 'numeric', 'min:0'],
            'distance_tier_km'          => ['nullable', 'numeric', 'min:1'],
            'transport_fee_per_tier'    => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Operational rules & event policies updated successfully!');
    }

    public function updateBankAccounts(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bca_account_number' => ['required', 'string', 'max:30'],
            'bca_account_name'   => ['required', 'string', 'max:100'],
            'bri_account_number' => ['required', 'string', 'max:30'],
            'bri_account_name'   => ['required', 'string', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Official payment bank accounts updated successfully!');
    }

    public function updateGoogleSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_sheet_id'    => ['nullable', 'string', 'max:255'],
            'google_calendar_id' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('google_sheet_id', trim($validated['google_sheet_id'] ?? ''));
        Setting::set('google_calendar_id', trim($validated['google_calendar_id'] ?? ''));

        return back()->with('success', 'Google Sheets and Google Calendar IDs updated successfully!');
    }

    public function updateOwnerPin(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'string', 'digits:6', 'confirmed'],
        ], [
            'pin.required'  => 'The 6-digit PIN is required.',
            'pin.digits'    => 'The PIN must be exactly 6 digits.',
            'pin.confirmed' => 'The PIN confirmation does not match.',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->pin = $request->pin;
            $user->save();
        }

        return back()->with('success', 'Owner access PIN has been successfully updated!');
    }
}
