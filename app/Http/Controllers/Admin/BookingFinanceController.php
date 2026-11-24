<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBookingToGoogleServicesJob;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingFinanceController extends Controller
{
    public function updateDiscount(Request $request, $id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->remaining_balance <= 0 && $booking->status !== 'pending_verification') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', "Diskon tidak dapat diubah karena invoice pesanan #{$booking->invoice_number} sudah berstatus LUNAS (Paid).");
        }

        if ($request->has('discount_amount') && !$request->has('discount_percent')) {
            $cleanDiscount = preg_replace('/[^\d]/', '', (string) $request->input('discount_amount'));
            $request->merge(['discount_amount' => $cleanDiscount !== '' ? (float) $cleanDiscount : 0]);
        }

        $validated = $request->validate([
            'discount_percent'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount'    => ['nullable', 'numeric', 'min:0'],
            'vendor_name'        => ['nullable', 'string', 'max:150'],
            'venue_address'      => ['nullable', 'string', 'max:500'],
            'address_landmark'   => ['nullable', 'string', 'max:255'],
            'event_time'         => ['nullable', 'string', 'max:100'],
            'parents_name'       => ['nullable', 'string', 'max:255'],
            'instagram_account'  => ['nullable', 'string', 'max:100'],
            'vendor_mua'         => ['nullable', 'string', 'max:150'],
            'vendor_decor'       => ['nullable', 'string', 'max:150'],
            'vendor_photo_video' => ['nullable', 'string', 'max:150'],
            'vendor_mc_wo'       => ['nullable', 'string', 'max:150'],
        ]);

        $subtotal = (float) $booking->package_price
            + (float) $booking->addon_total_price
            + (float) $booking->additional_day_charge
            + (float) ($booking->transport_fee ?? 0);

        if ($request->filled('discount_percent')) {
            $percent = max(0, min(100, (float) $validated['discount_percent']));
        } elseif ($request->filled('discount_amount')) {
            $rawDiscount = (float) ($validated['discount_amount'] ?? 0);
            $percent = $subtotal > 0 ? round(($rawDiscount / $subtotal) * 100, 2) : 0;
        } else {
            $percent = 0;
        }

        $booking->discount_percent   = $percent;
        $booking->vendor_name        = $validated['vendor_name'] ?? $booking->vendor_name;
        $booking->venue_address      = $validated['venue_address'] ?? $booking->venue_address;
        $booking->address_landmark   = $validated['address_landmark'] ?? $booking->address_landmark;
        $booking->event_time         = $validated['event_time'] ?? $booking->event_time;
        $booking->parents_name       = $validated['parents_name'] ?? $booking->parents_name;
        $booking->instagram_account  = $validated['instagram_account'] ?? $booking->instagram_account;
        $booking->vendor_mua         = $validated['vendor_mua'] ?? $booking->vendor_mua;
        $booking->vendor_decor       = $validated['vendor_decor'] ?? $booking->vendor_decor;
        $booking->vendor_photo_video = $validated['vendor_photo_video'] ?? $booking->vendor_photo_video;
        $booking->vendor_mc_wo       = $validated['vendor_mc_wo'] ?? $booking->vendor_mc_wo;
        $booking->save();

        $booking->recalculateTotals();
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Diskon vendor ({$percent}%) & tagihan pesanan #{$booking->invoice_number} berhasil diperbarui!");
    }

    public function updateOwnerDiscount(Request $request, $id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->remaining_balance <= 0 && $booking->status !== 'pending_verification') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', "Potongan harga khusus owner tidak dapat diubah karena invoice pesanan #{$booking->invoice_number} sudah berstatus LUNAS (Paid).");
        }

        $rawDiscount = $request->input('owner_discount');
        if (is_string($rawDiscount)) {
            $cleaned = preg_replace('/[^\d]/', '', $rawDiscount);
            $rawDiscount = $cleaned !== '' ? (float) $cleaned : 0;
        } else {
            $rawDiscount = (float) ($rawDiscount ?? 0);
        }

        $booking->owner_discount = max(0, $rawDiscount);
        $booking->save();
        $booking->recalculateTotals();

        SyncBookingToGoogleServicesJob::dispatch($booking);

        $formatted = 'Rp ' . number_format((float) $booking->owner_discount, 0, ',', '.');
        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Potongan harga spesial owner ({$formatted}) untuk pesanan #{$booking->invoice_number} berhasil diperbarui!");
    }

    public function updateTransport(Request $request, $id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->remaining_balance <= 0 && $booking->status !== 'pending_verification') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', "Biaya transport tidak dapat diubah karena invoice pesanan #{$booking->invoice_number} sudah berstatus LUNAS (Paid).");
        }

        $validated = $request->validate([
            'delivery_distance_km' => ['nullable', 'numeric', 'min:0'],
            'transport_fee'        => ['nullable', 'string'],
        ]);

        $distance = isset($validated['delivery_distance_km'])
            ? (float) $validated['delivery_distance_km']
            : (float) $booking->delivery_distance_km;

        if ($request->filled('transport_fee')) {
            $transportFee = (float) preg_replace('/[^\d]/', '', (string) $validated['transport_fee']);
        } else {
            $transportFee = Setting::calculateTransportFee($distance);
        }

        $booking->delivery_distance_km = $distance;
        $booking->transport_fee = $transportFee;
        $booking->save();
        $booking->recalculateTotals();

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Biaya transport pengiriman tenda pesanan #{$booking->invoice_number} berhasil diperbarui!");
    }

    public function storeAddon(Request $request, $id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->status === 'completed') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', "Item tidak dapat ditambahkan karena acara pesanan #{$booking->invoice_number} sudah berstatus SELESAI (Completed).");
        }

        if ($request->has('unit_price')) {
            $rawPrice = (string) $request->input('unit_price');
            $cleanPrice = preg_match('/^\d+\.\d{2}$/', $rawPrice)
                ? (float) $rawPrice
                : (float) preg_replace('/[^\d]/', '', $rawPrice);
            $request->merge(['unit_price' => $cleanPrice]);
        }

        $validated = $request->validate([
            'addon_id'   => ['nullable', 'exists:addons,id'],
            'addon_name' => ['required', 'string', 'max:150'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:9999'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $validated['unit_price'];

        $booking->addons()->create([
            'addon_id'   => !empty($validated['addon_id']) ? $validated['addon_id'] : null,
            'addon_name' => $validated['addon_name'],
            'unit_price' => $unitPrice,
            'quantity'   => $quantity,
            'subtotal'   => round($quantity * $unitPrice),
        ]);

        $booking->recalculateTotals();
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Item tambahan '{$validated['addon_name']}' ({$quantity}x) berhasil ditambahkan ke reservasi #{$booking->invoice_number}.");
    }

    public function deleteAddon(Request $request, $id, $addonId): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->status === 'completed') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', "Item tidak dapat dihapus karena acara pesanan #{$booking->invoice_number} sudah berstatus SELESAI (Completed).");
        }

        $addon = $booking->addons()->findOrFail($addonId);
        $name = $addon->addon_name;

        $addon->delete();
        $booking->recalculateTotals();
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Item '{$name}' berhasil dihapus dari reservasi #{$booking->invoice_number}. Tagihan telah diperbarui.");
    }
}
