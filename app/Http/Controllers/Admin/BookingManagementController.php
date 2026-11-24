<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBookingToGoogleServicesJob;
use App\Models\Booking;
use App\Services\AdminDashboardService;
use Illuminate\Http\RedirectResponse;

class BookingManagementController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $dashboardService
    ) {}

    public function index()
    {
        return view('admin.bookings.index', $this->dashboardService->getDashboardData());
    }

    public function show($id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id]);
    }

    public function confirm($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'confirmed']);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} berhasil diverifikasi & dikonfirmasi sah!");
    }

    public function reject($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'rejected']);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} telah ditolak.");
    }

    public function complete($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update([
            'status'            => 'completed',
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Status acara #{$booking->invoice_number} ({$booking->customer_name}) telah diselesaikan. Pembayaran otomatis tercatat LUNAS 100%.");
    }

    public function settle($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update([
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Pelunasan reservasi #{$booking->invoice_number} ({$booking->customer_name}) berhasil dicatat. Status pembayaran kini LUNAS 100%.");
    }
}
